<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\UpdateMediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{

    /**
     * @Route("/media/{media}/delete", name="media_delete")
     * @param Media $media
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Media $media, EntityManagerInterface $manager): RedirectResponse
    {
        if($media->getLink() !== 'default.jpeg'){
            unlink($this->getParameter('images_directory').'/'. $media->getLink());
        }
        $manager->remove($media);
        $manager->flush();
        return $this->redirectToRoute('figure_update',  ['id' => $media->getFigure()->getId()]);

    }

    /**
     * @Route("/media/{media}/favorite", name="media_switch_favorite")
     * @param Media $media
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function switchToFavorite(Media $media, EntityManagerInterface $manager): RedirectResponse
    {
        $actualFav =  $manager->getRepository(Media::class)->findOneBy(['figure' => $media->getFigure()->getId(), 'favorite' => true]);
        $actualFav->setFavorite(false);
        $media->setFavorite(true);
        $manager->flush();
        return $this->redirectToRoute('figure_update',  ['id' => $media->getFigure()->getId()]);
    }

    /**
     * @Route("/media/{media}/update", name="media_update", methods={"POST"})
     * @param Media $media
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function update(Media $media, EntityManagerInterface $manager, Request $request): RedirectResponse
    {
        $form = $this->createForm(UpdateMediaType::class, $media);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $image = $form->get('image')->getData();
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            $image->move(
                $this->getParameter('images_directory'),
                $fichier
            );

            if($media->getLink() !== 'default.jpeg'){
                unlink($this->getParameter('images_directory').'/'. $media->getLink());
            }

            $media->setLink($fichier);
            $manager->persist($media);
            $manager->flush();
            return $this->redirectToRoute('figure_update',  ['id' => $media->getFigure()->getId()]);
        }
    }

}
