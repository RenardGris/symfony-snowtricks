<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\User;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'figures' => $repository->findAll()
        ]);
    }

    /**
     * @Route("/figure/{id}", name="figure_index", methods={"GET"})
     * @return Response
     *
     */
    public function show(): Response
    {
        return $this->render('figure/show.html.twig', [
            'controller_name' => 'FigureController',
        ]);
    }

    /**
     * @Route("/figure/{id}", name="figure_index", methods={"POST"})
     * @return Response
     *
     */
    public function update(): Response
    {
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
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
                ? $figure->setCreatedAt(new \DateTime())->setAuthor($manager->getRepository(User::class)->find(60))
                : null;

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
     * @Route("/figure/{id}", name="figure_index", methods={"DELETE"})
     * @return Response
     *
     */
    public function delete(): Response
    {
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
        ]);
    }


}
