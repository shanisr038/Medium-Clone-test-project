<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    /**
     * Toggle follow/unfollow for a user.
     */
    public function toggleFollow(User $user)
    {
        $authUser = Auth::user();

        // Prevent user from following themselves
        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        // Check if already following
        $isFollowing = Follower::where('user_id', $user->id)
            ->where('follower_id', $authUser->id)
            ->exists();

        if ($isFollowing) {
            // Unfollow
            Follower::where('user_id', $user->id)
                ->where('follower_id', $authUser->id)
                ->delete();

            $message = 'Unfollowed successfully';
        } else {
            // Follow
            Follower::create([
                'user_id' => $user->id,
                'follower_id' => $authUser->id,
            ]);

            $message = 'Followed successfully';
        }

        // Get updated follower count
        $followersCount = Follower::where('user_id', $user->id)->count();

        // If request is AJAX, return JSON response for live updates
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'following' => !$isFollowing,
                'followers_count' => $followersCount,
                'message' => $message,
            ]);
        }

        // For normal redirect
        return back()->with('status', $message);
    }
}
