<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/figure/{id}/comment", name="comment_post", methods={"POST"})
     * @param Figure $figure
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function store(Figure $figure, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_post', ['id' => $figure->getId()]),
            'method' => 'POST',
        ]);

        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()){
            $comment->setCreatedAt(new \DateTime())
                ->setFigure($figure)
                ->setAuthor($manager->getRepository(User::class)->find(60));
            $manager->persist($comment);
            $manager->flush($comment);

            return $this->redirectToRoute('figure_show', ['slug' => $figure->getSlug()]);
        }

        return $this->render('comment/index.html.twig', [
            'commentForm' => $formComment->createView(),
        ]);
    }

    /**
     * @Route("/comment/load_more", name="comment_load_more")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     */
    public function loadMore(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $page = (int)$request->request->get('page');
        $figureId = (int)$request->request->get('figure_id');

        $repo = $manager->getRepository(Comment::class);
        $comments = $repo->paginateComments($page, $figureId);
        $lastPage = $repo->lastPageComments($figureId);

        return new JsonResponse([
            'nextComments' =>  $this->render('comment/load_more.html.twig', ['comments'=> $comments])->getContent(),
            'page' => $page < $lastPage ? $lastPage : false
        ], 200);

    }

}
