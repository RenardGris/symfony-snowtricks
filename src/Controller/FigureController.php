<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    /**
     * @Route("/figure", name="figure_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('figure/index.html.twig', [
            'controller_name' => 'FigureController',
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
     * @Route("/figure/new", name="figure_index", methods={"POST"})
     * @return Response
     *
     */
    public function store(): Response
    {
        return $this->render('figure/store.html.twig', [
            'controller_name' => 'FigureController',
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
