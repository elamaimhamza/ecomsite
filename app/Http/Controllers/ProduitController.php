<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the genre_id from request
        $genreName = $request->input('genre'); // or $request->input('genre_id');

        $genre =  Genre::where('nom', $genreName)->first();

        // Filter products by genre if genre_id is provided
        $produits = Produit::with(['genre', 'typeProduit'])
            ->when($genre, function ($query, $genre) {
                return $query->where('genre_id', $genre->id);
            })
            ->get();

        return response()->json([
            "message" => "fetched products",
            "data" => $produits,
            "Genre" => $genre
        ]);
    }

    public function getAll(Request $request)
    {
        // Set a default page size (e.g., 15 items per page)
        $perPage = $request->get('per_page', 15);

        // Get sorting parameters
        $sortBy = $request->get('sort_by', 'id'); // Default sort column
        $sortOrder = $request->get('sort_order', 'asc'); // Default sort order (asc or desc)

        // Ensure sort order is valid
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        try {
            $query = Produit::with(['genre', 'typeProduit']);

            // --- Filtering Logic ---
            // Example: Filter by product name (produit_nom)
            if ($request->has('produit_nom')) {
                $query->where('produit_nom', 'LIKE', '%' . $request->get('produit_nom') . '%');
            }

            // Example: Filter by a related genre ID
            if ($request->has('genre_id')) {
                $query->where('genre_id', $request->get('genre_id'));
            }

            // --- Sorting Logic ---
            $query->orderBy($sortBy, $sortOrder);

            // --- Pagination ---
            $produits = $query->paginate($perPage);

            // Check if no products were found
            if ($produits->isEmpty() && $produits->currentPage() > $produits->lastPage() && $produits->lastPage() > 0) {
                // This is a common issue when fetching a page beyond the last one.
                return response()->json([
                    "message" => "Page number exceeds the last available page."
                ], 404);
            }

            return response()->json([
                "message" => "Products fetched successfully",
                "data" => [
                    // 1. Get the actual array of product objects
                    "products" => $produits->items(),

                    // 2. Manually construct the meta object
                    "meta" => [
                        "current_page" => $produits->currentPage(),
                        "last_page"    => $produits->lastPage(),
                        "per_page"     => $produits->perPage(),
                        "total"        => $produits->total(),
                        "from"         => $produits->firstItem(),
                        "to"           => $produits->lastItem(),
                        "links"        => $produits->linkCollection(),
                        // Optional: Include Next/Prev URLs
                        "next_page_url" => $produits->nextPageUrl(),
                        "prev_page_url" => $produits->previousPageUrl(),
                    ]
                ],
            ]);
        } catch (\Exception $e) { // Use the base \Exception for more generic error capture
            // Log the error for debugging purposes (optional but recommended)
            Log::error("Error fetching products: " . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                "message" => "An error occurred while fetching products.",
                // In a production environment, avoid showing $e->getMessage() for security.
                // "error_details" => $e->getMessage() 
            ], 500); // Use 500 for Internal Server Error
        }
    }

    public function updateAdmin(Request $request, $id)
    {
        // 1. Validation Rules
        $validator = Validator::make($request->all(), [
            'nom'   => 'sometimes|required|string|max:255|unique:produits,nom,' . $id,
            'description'   => 'sometimes|string',
            'prix'          => 'sometimes|required|numeric|min:0',
            'stock'         => 'sometimes|required|integer|min:0',
            'genre_id'      => 'sometimes|required|exists:genres,id',
            'type_produit_id' => 'sometimes|required|exists:type_produits,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // 2. Find the Product
            $produit = Produit::find($id);

            if (!$produit) {
                return response()->json([
                    "message" => "Product not found"
                ], 404); // 404 Not Found
            }

            // 3. Update Attributes
            $produit->fill($request->all());
            $produit->save();

            // 4. Return Success Response
            return response()->json([
                "message" => "Product updated successfully",
                "data" => $produit->load(['genre', 'typeProduit']) // Reload relations for the response
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating product ID: {$id}. " . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                "message" => "An error occurred while updating the product."
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation of the incoming data
        // We ensure all required fields from your React form are present and valid.
        $validator = Validator::make($request->all(), [
            'nom'             => 'required|string|max:255',
            'prix'            => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',

            // Validate that these IDs actually exist in their respective tables
            // Adjust 'type_produits' and 'genres' to match your actual database table names
            'type_produit_id' => 'required|exists:type_produits,id',
            'genre_id'        => 'required|exists:genres,id',

            // Based on your React component, you are sending a URL string
            'image'           => 'nullable|url|max:2048',
            'description'     => 'nullable|string',

            // 'marque' is in your $fillable, but was not in the React form. 
            // We make it nullable here.
            'marque'          => 'nullable|string|max:255',
        ]);

        // 2. Return errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        // 3. Create the product
        // Since $fillable is set correctly in your model, we can pass the validated data directly.
        $product = Produit::create($validator->validated());

        // 4. Return the response
        return response()->json([
            'status' => 'success',
            'message' => 'Produit créé avec succès !',
            'data' => $product
        ], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produit = Produit::with(['genre', 'typeProduit', 'referencement'])->findOrFail($id);
        return response()->json($produit);
    }

    public function getProducts(Request $request)
    {
        $produitsList = $request->input('produitsIds');
        $produits = Produit::find($produitsList);
        return response()->json(["produits" => $produits]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $prix = $request->input("prix");
        if (is_null($prix)) {
            return response()->json(["message" => "prix est obligé"], 400);
        }
        $description = $request->input("description");
        $produit = Produit::find($id);
        $produit->update(["prix" => $prix, "description" => $description]);
        return response()->json(["message" => "product to update", "id" => $id, "produit" => $produit, $prix, $description]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 1. Find the product by ID
        $product = Produit::find($id);

        // 2. Check if the product exists
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produit non trouvé.'
            ], 404);
        }

        try {
            // 3. (Optional) Delete the associated image if it is a local file
            // Since your current setup uses a URL string, this isn't strictly necessary yet.
            // But if you switch to file uploads later, you would uncomment this:
            /*
            if ($product->image && \Storage::exists('public/' . $product->image)) {
                \Storage::delete('public/' . $product->image);
            }
            */

            // 4. Delete the record from the database
            $product->delete();

            // 5. Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Produit supprimé avec succès.'
            ], 200);
        } catch (\Exception $e) {
            // Handle Foreign Key constraints (e.g., Product is inside an Order)
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de supprimer ce produit car il est lié à d\'autres données (commandes, paniers, etc.).',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
