<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchRandomUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::get('https://randomuser.me/api/');

   
        if ($response->successful()) {

            $results = $response->json('results');

      
            Log::info('Random User Results:', $results);
        } else {
            Log::error('Failed to fetch data from randomuser.me');
        }
    }
}
