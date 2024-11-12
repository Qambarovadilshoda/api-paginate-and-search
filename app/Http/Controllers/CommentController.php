<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $product->comments()->create([
            "user_id"=> Auth::id(),
            "product_id"=> $product->id,
            "comment"=> $request->comment,
        ]);
        return response()->json([
            "message"=> "Success",
        ],201);
    }

    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);
        if(Auth::id() !== $comment->user_id){
            abort(403);
        }
        $comment->delete();
        return response()->noContent(204);
    }
}
