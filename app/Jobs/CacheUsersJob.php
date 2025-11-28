<?php


/**
 * Job to cache a batch of users in Redis.
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * Job to cache a batch of users in Redis.
 */
class CacheUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $key;
    public $users;

    /**
     * Create a new job instance.
     *
     * @param array $users
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job: cache each user in Redis for 5 minutes.
     */
    public function handle()
    {
        foreach ($this->users as $user) {
            $userId = is_array($user) ? $user['id'] : $user->id;
            Cache::put("user:{$userId}", $user, now()->addMinutes(5));
        }
    }
}