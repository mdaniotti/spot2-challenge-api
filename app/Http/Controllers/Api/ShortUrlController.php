<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Spot2 URL Shortener API",
 *     version="1.0.0",
 *     description="API for shortening URLs securely"
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Development server"
 * )
 */
class ShortUrlController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/urls",
     *     summary="Get all shortened URLs",
     *     tags={"URLs"},
     *     @OA\Response(
     *         response=200,
     *         description="List of shortened URLs",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="original_url", type="string"),
     *                     @OA\Property(property="short_code", type="string"),
     *                     @OA\Property(property="short_url", type="string"),
     *                     @OA\Property(property="clicks", type="integer"),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $shortUrls = ShortUrl::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $shortUrls->map(function ($url) {
                return [
                    'id' => $url->id,
                    'original_url' => $url->original_url,
                    'short_code' => $url->short_code,
                    'short_url' => URL::to('/' . $url->short_code),
                    'clicks' => $url->clicks,
                    'expires_at' => $url->expires_at,
                    'created_at' => $url->created_at,
                ];
            })
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/urls",
     *     summary="Create a new shortened URL",
     *     tags={"URLs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"url"},
     *             @OA\Property(property="url", type="string", format="url", example="https://example.com/very-long-url"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2024-12-31T23:59:59Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Shortened URL created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="URL shortened successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="original_url", type="string"),
     *                 @OA\Property(property="short_code", type="string"),
     *                 @OA\Property(property="short_url", type="string"),
     *                 @OA\Property(property="clicks", type="integer"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if URL already exists
        $existingUrl = ShortUrl::where('original_url', $request->url)->first();
        if ($existingUrl && !$existingUrl->isExpired()) {
            return response()->json([
                'success' => true,
                'message' => 'URL already exists',
                'data' => [
                    'id' => $existingUrl->id,
                    'original_url' => $existingUrl->original_url,
                    'short_code' => $existingUrl->short_code,
                    'short_url' => URL::to('/' . $existingUrl->short_code),
                    'clicks' => $existingUrl->clicks,
                    'expires_at' => $existingUrl->expires_at,
                    'created_at' => $existingUrl->created_at,
                ]
            ]);
        }

        $shortUrl = ShortUrl::create([
            'original_url' => $request->url,
            'short_code' => ShortUrl::generateShortCode(),
            'expires_at' => $request->expires_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'URL shortened successfully',
            'data' => [
                'id' => $shortUrl->id,
                'original_url' => $shortUrl->original_url,
                'short_code' => $shortUrl->short_code,
                'short_url' => URL::to('/' . $shortUrl->short_code),
                'clicks' => $shortUrl->clicks,
                'expires_at' => $shortUrl->expires_at,
                'created_at' => $shortUrl->created_at,
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/urls/{id}",
     *     summary="Get a specific shortened URL",
     *     tags={"URLs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shortened URL found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="original_url", type="string"),
     *                 @OA\Property(property="short_code", type="string"),
     *                 @OA\Property(property="short_url", type="string"),
     *                 @OA\Property(property="clicks", type="integer"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortened URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="URL not found")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $shortUrl = ShortUrl::find($id);
        
        if (!$shortUrl) {
            return response()->json([
                'success' => false,
                'message' => 'URL not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shortUrl->id,
                'original_url' => $shortUrl->original_url,
                'short_code' => $shortUrl->short_code,
                'short_url' => URL::to('/' . $shortUrl->short_code),
                'clicks' => $shortUrl->clicks,
                'expires_at' => $shortUrl->expires_at,
                'created_at' => $shortUrl->created_at,
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/{code}",
     *     summary="Redirect to original URL",
     *     tags={"Redirect"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Redirect to original URL",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="original_url", type="string", example="https://example.com/very-long-url")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortened URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="URL not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="Shortened URL expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="URL has expired")
     *         )
     *     )
     * )
     */
    public function redirect(string $code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();
        
        if (!$shortUrl) {
            return response()->json([
                'success' => false,
                'message' => 'URL not found'
            ], 404);
        }

        if ($shortUrl->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'URL has expired'
            ], 410);
        }

        $shortUrl->incrementClicks();

        return response()->json([
            'success' => true,
            'original_url' => $shortUrl->original_url
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/urls/{id}",
     *     summary="Update a shortened URL",
     *     tags={"URLs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="original_url", type="string", format="url", example="https://example.com/very-long-url"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2024-12-31T23:59:59Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shortened URL updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="URL updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="original_url", type="string"),
     *                 @OA\Property(property="short_code", type="string"),
     *                 @OA\Property(property="short_url", type="string"),
     *                 @OA\Property(property="clicks", type="integer"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortened URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="URL not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false), 
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $shortUrl = ShortUrl::find($id);
        
        if (!$shortUrl) {
            return response()->json([
                'success' => false,
                'message' => 'URL not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'original_url' => 'sometimes|required|url|max:2048',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $shortUrl->update($request->only(['original_url', 'expires_at']));

        return response()->json([
            'success' => true,
            'message' => 'URL updated successfully',
            'data' => [
                'id' => $shortUrl->id,
                'original_url' => $shortUrl->original_url,
                'short_code' => $shortUrl->short_code,
                'short_url' => URL::to('/' . $shortUrl->short_code),
                'clicks' => $shortUrl->clicks,
                'expires_at' => $shortUrl->expires_at,
                'created_at' => $shortUrl->created_at,
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/urls/{id}",
     *     summary="Delete a shortened URL",
     *     tags={"URLs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Shortened URL deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="URL deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Shortened URL not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="URL not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $shortUrl = ShortUrl::find($id);
        
        if (!$shortUrl) {
            return response()->json([
                'success' => false,
                'message' => 'URL not found'
            ], 404);
        }

        $shortUrl->delete();

        return response()->json([
            'success' => true,
            'message' => 'URL deleted successfully'
        ]);
    }
}
