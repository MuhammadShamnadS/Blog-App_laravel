<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Publish scheduled posts when the scheduled time arrives';

    public function handle()
    {
        $now = Carbon::now();

        $posts = Post::where('status', 'scheduled')
            ->where('schedule_at', '<=', $now)
            ->get();

        foreach ($posts as $post) {
            $post->status = 'published';
            $post->schedule_at = null;  
            $post->save();
        }

        $this->info('Scheduled posts published successfully.');
    }
}