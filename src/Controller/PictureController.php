<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/picture")
 * @IsGranted("ROLE_USER")
 */
class PictureController extends BaseController
{
    /**
     * @Route("/new", name="picture_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class,$picture, [
            'trick' => $this->get('session')->get('trick')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadMainPicture($form, "name", $picture);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', "Votre nouvelle image a été ajoutée avec succès !");

            return $this->redirectToRoute('trick_show', ["slug" => $picture->getTrick()->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="picture_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Picture $picture): Response
    {
        $form = $this->createForm(PictureType::class,$picture, [
            'trick' => $picture->getTrick()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadMainPicture($form, "name", $picture);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Les informations ont été mises à jour avec succès !");

            return $this->redirectToRoute('trick_show', ["slug" => $picture->getTrick()->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="picture_delete", methods={"POST"})
     */
    public function delete(Request $request, Picture $picture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();

            $this->addFlash('success', "La vidéo a été supprimée avec succès !");
        }

        return $this->redirectToRoute('trick_show', ["slug" => $picture->getTrick()->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
