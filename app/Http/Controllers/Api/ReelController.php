<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reel;
use App\Models\ReelLike;
use App\Models\ReelComment;
use App\Models\SocialMediaLink;
use Laravel\Sanctum\PersonalAccessToken;

class ReelController extends Controller
{
    public function index(Request $request)
    {
        $customerId = null;

        if ($request->bearerToken()) {
            $token = PersonalAccessToken::findToken($request->bearerToken());
            $customerId = $token?->tokenable?->id;
        }

        $likedReels = $customerId
            ? ReelLike::where('user_id', $customerId)->pluck('reel_id')->toArray()
            : [];

        $reels = Reel::orderBy('id', 'desc')
            ->get()
            ->map(function ($reel) use ($likedReels) {
                $data = $reel->toArray();

                $data['image_url'] = $reel->image
                    ? asset($reel->image)
                    : null;

                $data['media_url'] = $reel->media_file
                    ? asset($reel->media_file)
                    : null;

                $data['is_liked'] = in_array($reel->id, $likedReels);

                return $data;
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'data'    => $reels,
        ]);
    }

    public function reels_details(Request $request)
    {

        $id = $request->query('id');
        $reel = Reel::where('id', $id)
            ->first();

        if (!$reel) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'error'   => 'REELS_NOT_FOUND',
                'message' => 'Reels not found',
            ], 404);
        }

        $commentsCount = ReelComment::where('reel_id', $reel->id)->count();

        $relatedReels = Reel::where('category_id', $reel->category_id)
            ->where('id', '!=', $reel->id)
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $data = $item->toArray();
                $data['media_url'] = asset($item->media_file);
                return $data;
            });

        return response()->json([
            'success' => true,
            'code'    => 200,
            'data'    => [
                'reel' => [
                    ...$reel->toArray(),
                    'media_url' => asset($reel->media_file),
                    'comments_count' => $commentsCount,
                ],
                'related_reels' => $relatedReels
            ]
        ]);
    }

    public function toggleLike(Request $request)
    {
        /* ---------------------------------
     | 1. Validate reel id
     --------------------------------- */
        $reelId = $request->query('id');

        if (!$reelId) {
            return response()->json([
                'success' => false,
                'code'    => 400,
                'message' => 'Reel id is not found',
            ], 400);
        }

        /* ---------------------------------
     | 2. Validate token
     --------------------------------- */
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());

        if (!$accessToken || !($accessToken->tokenable instanceof \App\Models\Customer)) {
            return response()->json([
                'success' => false,
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $customer = $accessToken->tokenable;

        /* ---------------------------------
     | 3. Check reel exists
     --------------------------------- */
        $reel = Reel::find($reelId);

        if (!$reel) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'message' => 'Reel not found',
            ], 404);
        }

        /* ---------------------------------
     | 4. Toggle like
     --------------------------------- */
        $like = ReelLike::where('reel_id', $reelId)
            ->where('user_id', $customer->id)
            ->first();

        if ($like) {
            // UNLIKE
            $like->delete();

            if ($reel->likes_count > 0) {
                $reel->decrement('likes_count');
            }

            return response()->json([
                'success' => true,
                'liked'   => false,
                'likes_count' => $reel->likes_count,
            ]);
        }

        // LIKE
        ReelLike::create([
            'reel_id' => $reelId,
            'user_id' => $customer->id,
        ]);

        $reel->increment('likes_count');

        return response()->json([
            'success' => true,
            'liked'   => true,
            'likes_count' => $reel->likes_count,
        ]);
    }

    public function comments_list(Request $request)
    {
        $comments_id = $request->query('id');

        if (!$comments_id) {
            return response()->json([
                'success' => false,
                'code'    => 400,
                'message' => 'Reel id is required',
            ], 400);
        }
        $comments = ReelComment::where('reel_id', $comments_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $comments,
        ]);
    }
    public function addComment(Request $request)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);
        $reelId = $request->id;

        if (!$reelId) {
            return response()->json([
                'success' => false,
                'code'    => 400,
                'message' => 'Reel id is not found',
            ], 400);
        }
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $customer = $token->tokenable;

        $comment = ReelComment::create([
            'reel_id' => $reelId,
            'user_id' => $customer->id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $comment,
        ]);
    }

    public function social_media_link()
    {
        // Assuming only ONE row exists
        $links = SocialMediaLink::latest()->first();

        if (!$links) {
            return response()->json([
                'success' => false,
                'code'    => 404,
                'message' => 'Social media links not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'Social media links fetched successfully',
            'data'    => [
                'facebook'  => $links->facebook,
                'twitter'   => $links->twitter,
                'instagram' => $links->instagram,
                'linkedin'  => $links->linkedin,
                'whatsapp'  => $links->whatsapp,
                'youtube'   => $links->youtube,
            ],
        ], 200);
    }
}
