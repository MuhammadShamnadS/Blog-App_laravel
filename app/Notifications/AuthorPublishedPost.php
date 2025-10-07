<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthorPublishedPost extends Notification implements ShouldQueue
{
    use Queueable;

    protected $author;
    protected $post;

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
        return ['broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */


    //     public function toDatabase($notifiable)
    // {
    //     return [
    //         'author_id'   => $this->author->id,
    //         'author_name' => $this->author->name,
    //         'post_id'     => $this->post->id,
    //         'post_title'  => $this->post->title,
    //     ];
    // }
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }


        public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'author_id'   => $this->author->id,
            'author_name' => $this->author->name,
            'post_id'     => $this->post->id,
            'post_title'  => $this->post->title,
        ]);
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */



    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
}
