<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Support\Facades\Log;

class SubscribedPostNotification extends Notification implements ShouldQueue
{
    use Queueable;


    protected $author;
    protected $post;

    /**
     * Create a new notification instance.
     */
    public function __construct($author, $post)
    {
        $this->author = $author;
        $this->post   = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {

        $url = "/dashboard/guest/post/{$this->post->id}";
        \Log::info("Push URL being sent: " . $url);
        return (new WebPushMessage)

            ->title("New post from {$this->author->name}")
            ->body($this->post->title)
            ->icon('/blog.jpeg')
            ->data(['url' => $url]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
}
