<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Models\Item;

class TransactionController extends Controller
{

    public function show(Item $item)
    {
        if (!in_array(auth()->id(), [$item->user_id, $item->buyer_id])) {
            abort(403);
        }

        return redirect()->route('chat.index', [
            'item' => $item->id,
            'edit' => request('edit'),
        ]);
    }
}
