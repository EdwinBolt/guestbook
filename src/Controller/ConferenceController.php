<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use App\SpamChecker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{

    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }


    /**
     * @Route("/", name="homepage")
     */

    public function index(ConferenceRepository $conferenceRepository)
    {
        $response = new Response($this->twig->render('conference/index.html.twig',
            [
        'conferences'=> $conferenceRepository->findAll(),
        ]));

//        $response->setSharedMaxAge(3600);
        return $response;

    }

    /**
     * @Route("/conference_header", name="conference_header")
     */
    public function conferenceHeader(ConferenceRepository $conferenceRepository){
        $response = new Response($this->twig->render('conference/header.html.twig',[
            'conferences'=>$conferenceRepository->findAll(),
        ]));

//        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/conference/{slug}", name="conference")
     */
    public function show(Request $request, Conference $conference, CommentRepository $commentRepository, ConferenceRepository $conferenceRepository, string $photoDir, NotifierInterface $notifier)
    {
        $comment= new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setConference($conference);
            if ($photo = $form['photo']->getData()){
                $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
                try{
                    $photo->move($photoDir, $filename);
                } catch (FileException $e){
//                    unable to load photo
                }
                $comment->setPhotoFileName($filename);
            }
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $context = [
                'user-ip'=>$request->getClientIp(),
                'user-agent'=>$request->headers->get('user_agent'),
                'referrer'=>$request->headers->get('referer'),
                'permalink'=>$request->getUri(),
            ];
            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));

            $notifier->send(new Notification('Thanks for your feedback. After moderation your comment will be placed',['browser']));

            return $this->redirectToRoute('conference',['slug'=>$conference->getSlug()]);
        }

        if ($form->isSubmitted()){
            $notifier->send(new Notification('Please check your comment, something appears wrong'.['browser']));
        }

        $offset = max(0, $request->query->getInt('offset',0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conference/show.html.twig',[
            'conferences' => $conferenceRepository->findAll(),
            'conference' => $conference,
//            'comments' => $commentRepository->findBy(['conference' => $conference]
//        , ['createdAt' => 'DESC']),
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),
            ]));
    }

}
