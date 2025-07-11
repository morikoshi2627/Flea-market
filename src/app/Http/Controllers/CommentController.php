<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;
use App\Http\Requests\CommentRequest;


class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->comment,
        ]);
        
        return redirect()->route('items.show', ['item' => $item->id])
            ->with('success', 'コメントを投稿しました');
    }
}
