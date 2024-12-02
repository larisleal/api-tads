<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API de Produtos",
 *     version="1.0.0",
 *     description="API para gerenciamento de produtos com autenticação usando Keycloak"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id", type="integer", example=1, description="ID do produto"),
 *     @OA\Property(property="name", type="string", example="Produto A", description="Nome do produto"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99, description="Preço do produto"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z", description="Data de criação do produto"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z", description="Data da última atualização do produto"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário que criou o produto")
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Listar todos os produtos",
     *     tags={"Produtos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de produtos retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Criar um novo produto",
     *     tags={"Produtos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string", example="Produto A", description="Nome do produto"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99, description="Preço do produto")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação, dados informados estão incorretos"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $product = Product::create(array_merge($request->all(), ['user_id' => auth()->id()]));
        return response()->json($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Obter um produto específico",
     *     tags={"Produtos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto a ser obtido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Atualizar um produto existente",
     *     tags={"Produtos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto a ser atualizado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *             @OA\Property(property="name", type="string", example="Produto A", description="Nome do produto"),
     *             @OA\Property(property="price", type="number", format="float", example=99.99, description="Preço do produto")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado ou acesso não autorizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $product = Product::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado ou acesso não autorizado'], 404);
        }

        $product->update($request->all());
        return response()->json($product, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Remover um produto",
     *     tags={"Produtos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do produto a ser removido",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Produto removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produto não encontrado ou acesso não autorizado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado ou acesso não autorizado'], 404);
        }

        $product->delete();
        return response()->json(null, 204);
    }
}
