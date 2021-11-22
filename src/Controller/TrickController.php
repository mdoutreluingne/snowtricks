<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use App\Service\ConvertUrlVideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Controller\BaseController;

/**
 * @Route("/trick")
 */
class TrickController extends BaseController
{
    /**
     * @Route("/ajax-delete-mainpicture", name="trick_delete_mainpicture", methods={"POST"})
     */
    public function ajaxNew(Request $request, TrickRepository $trickRepository): Response
    {
        // Get data
        $donnees = json_decode($request->getContent());
        dump($donnees);

        if (
            isset($donnees->trick) && !empty($donnees->trick)
        ) {
            //Init code
            $code = 200;

            $trick = $trickRepository->find($donnees->trick);

            $this->checkPictureExist($trick);

            //Set null main picture
            $trick->setMainPicture(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();

            return new Response('Ok', $code);
        }

        return new Response('Données incomplètes', 404);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadMainPicture($form, "main_picture", $trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', "Votre nouvelle figure a été ajoutée avec succès !");

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
        $this->isGranted("ROLE_USER") ? $this->get('session')->set('trick', $trick) : "";

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'pictures' => $this->pictureRepository->findBy(['trick' => $trick]),
            'videos' => $this->convertUrlVideoService->VidProviderUrl2Player($trick)
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadMainPicture($form, "main_picture", $trick);
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Les informations ont été mises à jour avec succès !");

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'pictures' => $this->pictureRepository->findBy(['trick' => $trick]),
            'videos' => $this->convertUrlVideoService->VidProviderUrl2Player($trick)
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{slug}", name="trick_delete", methods={"POST"})
     */
    public function delete(Request $request, Trick $trick): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('success', $trick->getName() . " a été supprimée avec succès !");
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}