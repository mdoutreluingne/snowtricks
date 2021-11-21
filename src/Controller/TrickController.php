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

/**
 * @Route("/trick")
 */
class TrickController extends BaseController
{
    /**
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
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
            $this->uploadMainPicture($form, $trick);

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
    public function show(Trick $trick, PictureRepository $pictureRepository, ConvertUrlVideoService $convertUrlVideoService): Response
    {
        //dd();
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'pictures' => $pictureRepository->findBy(['trick' => $trick]),
            'videos' => $convertUrlVideoService->VidProviderUrl2Player($trick)
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick, PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadMainPicture($form, $trick);
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Les informations ont été mises à jour avec succès !");

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'pictures' => $pictureRepository->findBy(['trick' => $trick])
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

    public function uploadMainPicture($form, $object): void
    {
        //We recover the transmitted avatar
        $avatar = $form->get('main_picture')->getData();

        //Call function for manage avatar
        $this->managePicture($avatar, $object);
    }
}
