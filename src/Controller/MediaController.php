<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Media;
use App\Form\StoreMediaType;
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($media->getType() === 'photo' && $media->getLink() !== 'default.jpeg'){
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(UpdateMediaType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $fichier = $form->get('video')->getData();
            if($media->getType() === 'photo'){
                $image = $form->get('image')->getData();
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                if($media->getLink() !== 'default.jpeg'){
                    unlink($this->getParameter('images_directory').'/'. $media->getLink());
                }
            }

            $media->setLink($fichier);
            $manager->persist($media);
            $manager->flush();
            return $this->redirectToRoute('figure_update',  ['id' => $media->getFigure()->getId()]);
        }
    }

    /**
     * @Route("/media/{media}/remove-favorite", name="media_remove_favorite")
     * @param Media $media
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFavorite(Media $media, EntityManagerInterface $manager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $newFav =  $manager->getRepository(Media::class)->findOneBy(['figure' => $media->getFigure()->getId(), 'favorite' => false]);
        if($newFav){
            $media->setFavorite(false);
            $newFav->setFavorite(true);
            $manager->persist($media);
            $manager->flush();
        }

        return $this->redirectToRoute('figure_update',  ['id' => $media->getFigure()->getId()]);
    }


    public function storeMediaToFigure($figure, $image, $type, $directory){

        $fichier = $image;
        if($type === 'photo') {
            // On génère un nouveau nom de fichier
            $fichier = md5(uniqid()).'.'.$image->guessExtension();
            // On copie le fichier dans le dossier uploads
            $image->move(
                $directory,
                $fichier
            );
        }

        // On crée l'image dans la base de données
        $media = new Media();
        $media->setLink($fichier);
        $media->setType($type);
        $media->setAddedAt(new \DateTime());
        $media->setFavorite(false);
        $figure->addMedium($media);
    }

    /**
     * @Route("figure/{figure}/media/store", name="media_store")
     * @param Figure $figure
     * @param Request $request
     * @param EntityManagerInterface $manager

     */
    public function store(Figure $figure,Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(StoreMediaType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $types = [
                'photo' => $form->get('images')->getData(),
                'video' => $form->get('video')->getData()
            ];

            foreach ($types as $k => $medias){
                if(isset($medias)){
                    foreach ($medias as $media){
                        if(isset($media)){
                            $this->storeMediaToFigure($figure, $media, $k, $this->getParameter('images_directory'));
                        }
                    }
                }
            }

            $manager->persist($figure);
            $manager->flush();
        }

        return $this->redirectToRoute('figure_update',  ['id' => $figure->getId()]);
    }


}
