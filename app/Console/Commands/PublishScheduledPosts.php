<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Publish scheduled posts, auto-publish approved posts, and archive old posts';

    public function handle()
    {
        $now = Carbon::now();
        $startOfHour = $now->copy()->startOfHour();
        $endOfHour   = $now->copy()->endOfHour();

        Post::where('status', 'scheduled')
            ->whereBetween('schedule_at', [$startOfHour, $endOfHour])
            ->update([
                'status' => 'published',
                'schedule_at' => null,
            ]);


        $hour = $now->hour;
        if ($hour >= 1 && $hour <= 4) {
            Post::where('status', 'editor_approved')
                ->whereNull('schedule_at')
                ->update([
                    'status' => 'published',
                ]);
        }


        $today = $now->subDays(30);
        Post::where('created_at', '<=', $today)
            ->where('status', 'published')
            ->update([
                'status' => 'archived',
                'schedule_at' => null,
            ]);

        $this->info('Posts processed successfully.');
    }
}
