<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentFlagged extends Notification
{
    use Queueable;

    public $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A comment has been flagged for review')
            ->line('A comment on the post "' . $this->comment->post->title . '" has been flagged.')
            ->line('Reason: ' . ucfirst($this->comment->flag_reason))
            ->line('Comment snippet: ' . \Illuminate\Support\Str::limit($this->comment->content, 50))
            ->action('Review Comment', route('admin.comments.index', ['status' => 'flagged']))
            ->line('Please review the flagged comment in the admin panel.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'reason' => $this->comment->flag_reason,
            'post_title' => $this->comment->post->title,
        ];
    }
}
