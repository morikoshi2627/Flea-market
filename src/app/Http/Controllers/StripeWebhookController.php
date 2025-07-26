<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Event;
use App\Models\Item;
use App\Models\Purchase;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // 無効なペイロード
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // 無効な署名
            return response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // すでに処理済みの支払いはスキップ
            if (Purchase::where('payment_id', $session->id)->exists()) {
                return response('Already handled', 200);
            }

            $user_id = $session->metadata->user_id ?? null;
            $item_id = $session->metadata->item_id ?? null;

            // if (!$user_id || !$item_id) {
            //     Log::error('Missing metadata in session', ['session' => $session]);
            //     return response('Missing metadata', 400);
            // }

            $item = Item::find($item_id);

            if (!$item || $item->status === 'sold') {
                return response()->json(['error' => 'Item not found or already sold'], 400);
            }

            // 商品を「売却済み」にする
            $item->status = 'sold';
            $item->buyer_id = $user_id;
            $item->save();

            // 配送先情報などは $session->customer_details などから取得可
            $shipping = $session->shipping ?? [];

            // 支払い方法を取得
            $payment_method_type = $session->payment_method_types[0] ?? 'unknown';

            // 購入情報を保存
            Purchase::create([
                'user_id'        => $user_id,
                'item_id'        => $item_id,
                'payment_method' => $payment_method_type,
                'postal_code'    => $shipping['address']['postal_code'] ?? '',
                'address'        => $shipping['address']['line1'] ?? '',
                'building'       => $shipping['address']['line2'] ?? '',
                'payment_id'     => $session->id,
            ]);

            Log::info('Stripe payment processed successfully.', ['session' => $session]);
        }

        return response()->json(['status' => 'success'], 200);
    }
}