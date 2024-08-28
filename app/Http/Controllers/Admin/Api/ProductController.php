<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use Yajra\DataTables\DataTables;
use Illuminate\Auth\Access\AuthorizationException;

class ProductController extends Controller
{
    protected $url;
    protected $user;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }
    public function getProductsList()
    {
        try{
            $this->authorizeForUser($this->user,'list', new Product);
            $medicines = Product::query();
            return Datatables::of($medicines)
                ->addColumn('image_url', function ($med) {
                    return URL::to("product/" . $med->image_url);
                })
                ->toJson();
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
    public function getProducts()
    {
        try{
            $this->authorizeForUser($this->user,'list', new Product);
            return response()->json([
                'data' => Product::select('products.*')->get()
            ], 200);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
        
    }
    public function SaveProduct(Request $request)
    {
        try {
            $this->authorizeForUser($this->user, 'add', new Product);
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0.01',
                'quantity' => 'required|integer|min:1',
            ]);

            // If validation passes, create the product
            Product::create([
                'name' => $validatedData['name'],
                'price' => $validatedData['price'],
                'description' => $validatedData['description'],
                'quantity' => $validatedData['quantity']
            ]);

            return response()->json([
                'message' => "success"
            ]);

        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    public function getProductById($id,Request $request)
    {
        try {
            $this->authorizeForUser($this->user, 'list', new Product);
            $product = Product::find($id);

            return response()->json([
                'data' => $product
            ]);

        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function EditProduct($id, Request $request)
    {
        try {
            $this->authorizeForUser($this->user, 'edit', new Product);
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found.'], 404);
            }

            // Validation rules
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0.01',
                'quantity' => 'required|integer|min:1',
            ]);

            // Update product details
            $product->name = $validatedData['name'];
            $product->price = $validatedData['price'];
            $product->description = $validatedData['description'];
            $product->quantity = $validatedData['quantity'];
            $product->save();

            return response()->json([
                'message' => "success"
            ]);

        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    public function DeleteProduct($id, Request $request)
    {
        try{
            $this->authorizeForUser($this->user,'delete', new Product);
            $medicine = Product::where("id", $id)->delete();
            return response()->json([
                'message' => "success"
            ]);
        } catch (AuthorizationException $e) {
            return  $e->getMessage();
        }
    }
}
