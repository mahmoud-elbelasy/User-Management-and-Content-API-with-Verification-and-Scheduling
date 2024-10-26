<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;



class PostObserver
{
    public function created(Post $post)
    {
        $this->updateStatsCache();
    }

    public function updated(Post $post)
    {
        $this->updateStatsCache();
    }

    public function deleted(Post $post)
    {
        $this->updateStatsCache();
    }

    protected function updateStatsCache()
    {
        
        Cache::forget('stats');

        $userCount = User::count();
        $postCount = Post::count();
        $usersWithNoPosts = User::doesntHave('posts')->count();

        $stats = [
            'userCount' => $userCount,
            'postCount' => $postCount,
            'usersWithNoPosts' => $usersWithNoPosts,
        ];

     
        Cache::put('stats', $stats, 30); 
    }
}
