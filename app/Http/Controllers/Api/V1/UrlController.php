<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UrlCreateRequest;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use App\Services\UrlShortenerService;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function listUrls(Request $request)
    {
        $user = $request->user();
        $urls = $user->urls;

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $urls->count(),
                'urls' => UrlResource::collection($urls)
            ],
            'message' => 'URLs have been retrieved successfully.'
        ], 200);
    }

    public function shortenUrl(UrlCreateRequest $request, UrlShortenerService $service)
    {
        $validatedData = $request->validated();
        $user = $request->user();

        $existingUrl = Url::where('user_id', $user->id)
            ->where('original_url', $validatedData['original_url'])
            ->first();

        if ($existingUrl) {
            return response()->json([
                'status' => 'success',
                'data' => new UrlResource($existingUrl),
                'message' => 'URL already exists.'
            ], 200);
        }

        try {
            $shortenUrl = $service->shortenUrl($validatedData['original_url']);

            if ($shortenUrl['error']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $shortenUrl['error']
                ], 400);
            }

            $url = $user->urls()->create([
                'original_url' => $shortenUrl['original_url'],
                'short_url' => $shortenUrl['short_url'],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => new UrlResource($url),
                'message' => 'URL shortened successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL shortening failed. Please try again.'
            ], 500);
        }
    }

    public function redirect(Url $url)
    {
        if (!$url) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL not found'
            ], 404);
        }

        return redirect()->away($url->original_url);
    }
}
