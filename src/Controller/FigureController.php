<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Form\UpdateMediaType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    /**
     * @Route("/", name="figure_index", methods={"GET"})
     * @param FigureRepository $repository
     * @return Response
     */
    public function index(FigureRepository $repository): Response
    {
        return $this->render('figure/index.html.twig', [
            'figures' => $repository->paginateFigure(1)
        ]);
    }

    /**
     * @Route("/figure/new", name="figure_store")
     * @param Figure|null $figure
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function store(Figure $figure = null,Request $request, EntityManagerInterface $manager): Response
    {

        if(!$figure){
            $figure = new Figure();
        }

        $form = $this->createForm(FigureType::class, $figure);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            !$figure->getId()
                ? $figure->setCreatedAt(new \DateTime())->setAuthor($manager->getRepository(User::class)->find($user)->id)
                : null;

            $images = $form->get('images')->getData();
            $videos =  $form->get('videos')->getData();

            // On boucle sur les images
            if(sizeof($images) > 0) {
                $mc = new  MediaController();
                foreach($images as $image) {
                    $mc->storeMediaToFigure($figure, $image, 'photo');
                }
            } else {
                $media = new Media();
                $media->setLink('default.jpeg');
                $media->setType('photo');
                $media->setAddedAt(new \DateTime());
                $media->setFavorite(true);
                $figure->addMedium($media);
            }
            $mc = new  MediaController();
            foreach($videos as $video){
                $mc->storeMediaToFigure($figure,$video, 'video');
            }

            $manager->persist($figure);
            $manager->flush();

            return $this->redirectToRoute('figure_show', [
                'id' => $figure->getId(),
            ]);

        }

        return $this->render('figure/store.html.twig', [
            'formFigure' => $form->createView(),
            'editMode' => $figure->getId() !== null,
        ]);

    }

    /**
     * @Route("/figure/{id}", name="figure_show", methods={"GET"})
     * @param Figure $figure
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function show(Figure $figure, CommentRepository $commentRepository): Response
    {

        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_post', ['id' => $figure->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('figure/show.html.twig', [
            'figure' => $figure,
            'comments' => $commentRepository->paginateComments(1, $figure->getId()),
            'formComment' => $formComment->createView(),
        ]);
    }

    /**
     * @Route("/figure/{id}/edit", name="figure_update")
     * @param Figure $figure
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function update(Figure $figure, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(FigureType::class, $figure)
            ->remove('images')
            ->remove('videos')
            ->remove('name');

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $figure->setUpdatedAt(new  \DateTime());
            $manager->persist($figure);
            $manager->flush();

        }

        return $this->render('figure/update.html.twig', [
            'figure' => $figure,
            'formFigure' => $form->createView(),
            'formUpdateMedia' => $this->createForm(UpdateMediaType::class),
        ]);
    }

    /**
     * @Route("/figure/{id}/delete", name="figure_delete")
     * @param Figure $figure
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Figure $figure, EntityManagerInterface $manager): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $medias = $figure->getMedia();
        foreach ($medias as $media){
            if($media->getType() === 'photo' && $media->getLink() !== 'default.jpeg'){
                unlink($this->getParameter('images_directory').'/'. $media->getLink());
            }
        }

        $manager->remove($figure);
        $manager->flush();
        return $this->redirectToRoute('figure_index',  ['figure' => $manager->getRepository(Figure::class)]);
    }

    /**
     * @Route("/figure/load_more", name="figure_load_more")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function loadMore(Request $request, EntityManagerInterface $manager): Response
    {
        $page = $request->request->get('page');

        $repo = $manager->getRepository(Figure::class);
        $figures = $repo->paginateFigure($page);
        $lastPage = $repo->lastPage();

        return new JsonResponse(                    [
            'nextFigure' =>  $this->render('figure/load_more.html.twig', ['figures'=> $figures])->getContent(),
            'pages' => $page < $lastPage ? $lastPage : false
        ], 200);

    }

}
