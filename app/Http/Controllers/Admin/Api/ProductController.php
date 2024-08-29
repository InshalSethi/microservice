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

/**
 * @OA\Info(
 *     title="Koraspond Microservices API",
 *     version="1.0.0",
 *     description="API documentation for the application endpoints."
 * )
 *
 * @OA\Tag(
 *     name="Product",
 *     description="Product related APIs"
 * )
 * 
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Product Name"),
 *     @OA\Property(property="description", type="string", example="Product Description"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99),
 *     @OA\Property(property="quantity", type="integer", example=100),
 * )
 */
class ProductController extends Controller
{
    protected $url;
    protected $user;
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
        $this->user = Auth::guard('admin')->user();
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products-list",
     *     summary="Get a list of products",
     *     tags={"Product"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             description="Request body for getting product list",
     *             properties={}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product list retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="draw",
     *                 type="integer",
     *                 example=0
     *             ),
     *             @OA\Property(
     *                 property="recordsTotal",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="recordsFiltered",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=10
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="pro"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="abc"
     *                     ),
     *                     @OA\Property(
     *                         property="price",
     *                         type="string",
     *                         example="10.00"
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2024-08-29T11:00:10.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2024-08-29T11:10:50.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="image_url",
     *                         type="string",
     *                         format="url",
     *                         example="http://127.0.0.1:8000/product"
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="disableOrdering",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="queries",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="query",
     *                         type="string",
     *                         example="select count(*) as aggregate from `products`"
     *                     ),
     *                     @OA\Property(
     *                         property="bindings",
     *                         type="array",
     *                         items={
     *                             "type": "string"
     *                         }
     *                     ),
     *                     @OA\Property(
     *                         property="time",
     *                         type="number",
     *                         format="float",
     *                         example=1.93
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="input",
     *                 type="array",
     *                 items={
     *                     "type": "string"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/api/admin/products",
     *     summary="Save a new product",
     *     tags={"Product"},
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "price", "quantity"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the product",
     *                 example="pro"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the product",
     *                 example="abc"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 format="float",
     *                 description="Price of the product",
     *                 example=10
     *             ),
     *             @OA\Property(
     *                 property="quantity",
     *                 type="integer",
     *                 description="Quantity of the product",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="success"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={"type": "string"}
     *             )
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/api/admin/product/{id}",
     *     summary="Get product details by ID",
     *     tags={"Product"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=10
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="pro"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="abc"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="string",
     *                     example="10.00"
     *                 ),
     *                 @OA\Property(
     *                     property="quantity",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-08-29T11:00:10.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-08-29T11:10:50.000000Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
 */
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
    /**
     * @OA\Put(
     *     path="/api/admin/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Product"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "price", "quantity"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name of the product",
     *                 example="pro"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the product",
     *                 example="abc"
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="number",
     *                 format="float",
     *                 description="Price of the product",
     *                 example=10
     *             ),
     *             @OA\Property(
     *                 property="quantity",
     *                 type="integer",
     *                 description="Quantity of the product",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="success"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties={"type": "string"}
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/admin/products/{id}",
     *     summary="Delete a product by ID",
     *     tags={"Product"},
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="success"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
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
