<?php


namespace App\Notification;


use App\Entity\Comment;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

class CommentReviewNotification extends Notification implements EmailNotificationInterface
{
    private $comment;


    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        parent::__construct('New comment posted');
    }


    public function asEmailMessage(Recipient $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient, $transport);
        if (preg_match('{\b(great|awesome)\b}i', $this->comment->getText())) {
            $message->getMessage()
                ->htmlTemplate('emails/comment_notification.html.twig')

                ->context(['comment' => $this->comment]);
        }else{
            $message->getMessage()
                ->htmlTemplate('emails/comment_notification_awesome.html.twig')
                ->context(['comment' => $this->comment]);

        }
        return $message;
    }

//    public function getChannels(Recipient $recipient, string $transport = null)
//    {
//        if (preg_match('{\b(great|awesome)\b}i', $this->comment->getText())){
//            $message = EmailMessage::fromNotification($this, $recipient, $transport);
//            $message->getMessage()
//                ->htmlTemplate('emails/comment_notification.html.twig')
//                ->context(['comment'=>$this->comment])
//            ;
//
//            return $message;
//        }
//
//    }
}