<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Message\CommentMessage;
use App\Notification\CommentReviewNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

/**
 * @Route ("/admin")
 */

class AdminController extends AbstractController
{
    private $twig;
    private $entityManager;
    private $bus;
    private $workflow;
    private $mailer;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager, MessageBusInterface $bus, WorkflowInterface $commentStateMachine, MailerInterface $mailer)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        $this->workflow = $commentStateMachine;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/comment/review/{id}", name="review_comment")
     */
    public function reviewComment(Request $request, Comment $comment, Registry $registry)
    {
        $accepted = !$request->query->get('reject');

        $machine = $registry->get($comment);
        if ($machine->can($comment, 'publish')) {
            $transition = $accepted ? 'publish' : 'reject';
            $this->mailer->send((new NotificationEmail())
                ->subject('comment accepted')
                ->htmlTemplate('emails/confirmation_mail_to_commenter.html.twig')
                ->from('admin@example.nl')
                ->to($comment->getEmail())
                ->context(['comment' => $comment])
            );
        } elseif ($machine->can($comment, 'publish_ham')) {
            $transition = $accepted ? 'publish_ham' : 'reject_ham';
            $this->mailer->send((new NotificationEmail())
                ->subject('comment accepted')
                ->htmlTemplate('emails/confirmation_mail_to_commenter.html.twig')
                ->from('admin@example.nl')
                ->to($comment->getEmail())
                ->context(['comment' => $comment])
            );
        } else {
            return new Response('Comment already reviewed or not in the right state.');
        }

        $machine->apply($comment, $transition);
        $this->entityManager->flush();

        if ($accepted) {
            $this->bus->dispatch(new CommentMessage($comment->getId()));
        }

        return $this->render('admin/review.html.twig', [
            'transition' => $transition,
            'comment' => $comment,
        ]);
    }

    /**
     * @Route ("/http-cache/{uri<.*>}", methods={"PURGE"})
     */
    public function purgeHttpClient(KernelInterface $kernel, Request $request, string $uri){
        if ('prod' === $kernel->getEnvironment()){
            return new Response('KO', 400);
        }

        $store = (new class($kernel) extends HttpCache{})->getStore();
        $store->purge($request->getSchemeAndHttpHost().'/'.$uri);

        return new Response('Done');

    }

}