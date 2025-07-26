<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\Purchase;

class StripeCheckoutController extends Controller
{
    public function checkout(Request $request, Item $item)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),

            // metadata を追加
            'metadata' => [
                'user_id' => auth()->id(),
                'item_id' => $item->id,
                'postal_code' => auth()->user()->postal_code,
                'address' => auth()->user()->address,
                'building' => auth()->user()->building,
                'payment_method' => $request->input('payment_method'),
            ],
        ]);

        // Stripeの支払い画面へ
        return redirect($session->url);
    }

    public function success(Request $request)
    {

        return redirect()->route('items.index')->with('success', '決済が完了しました');
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}