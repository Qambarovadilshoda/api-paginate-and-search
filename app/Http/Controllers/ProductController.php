<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(10);
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product();
        $product->user_id = Auth::id();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;

        $uploadedImage = $this->uploadImage($request->file("image"));
        $product->image = $uploadedImage;

        $product->save();
        return response()->json([
            "status"=> "success",
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('comments.user')->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        if(Auth::id() !== $product->user_id){
            abort(403);
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        if( $request->hasFile("image") ){
            if($product->image){
                $this->deleteImage($product->image);
            }
            $updatedImage = $this->uploadImage($request->file("image"));
            $product->image = $updatedImage;
        }
        $product->save();
        return response()->json([
            "status"=> "success",
            "product" => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if(Auth::id() !== $product->user_id){
            abort(403);
        }
        $this->deleteImage($product->image);
        $product->delete();
        return response()->noContent(204);
    }
    public function uploadImage($image){
        $imagePath = time() . '.' . $image->getClientOriginalExtension();
        $uploadedImage = $image->storeAs('images', $imagePath, 'public');
        return $uploadedImage;
    }
    public function deleteImage($image){
        @unlink(storage_path('app/public'. $image));
        return;
    }
    public function search(Request $request){

        $products = Product::when($request->q, function($query, $q){
            return $query->where('name', 'like', "%$q%");
        })->paginate(20);
        return response()->json($products);
    }
}
