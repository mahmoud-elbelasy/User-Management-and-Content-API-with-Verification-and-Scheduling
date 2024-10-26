<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function stats()
    {
        // Try to get the stats from cache
        $stats = Cache::get('stats');

        // If not found in cache, recalculate and store it
        if (!$stats) {
            $stats = $this->recalculateStats();
        }

        return response()->json($stats);
    }
    public function recalculateStats() {

        $stats = Cache::remember('stats', now()->addMinutes(30), function(){
            $users_count = User::count();
            $posts_count = Post::whereNull('deleted_at')->count();
            $users_without_posts = User::whereDoesntHave('posts')->count();

            \Log::info('Stats calculated and cached:', [
                'total_users' => $users_count,
                'total_posts' => $posts_count,
                'users_with_no_posts' => $users_without_posts,
            ]);

            return [
                'total_users' => $users_count,
                'total_posts' => $posts_count,
                'users_with_no_posts' => $users_without_posts,
            ];
        });
        
        return response()->json($stats,200);
    }
}
