<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Controller\BaseController;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use App\Service\ConvertUrlVideoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class TrickController extends BaseController
{
    /**
     * @Route("/ajax-delete-mainpicture", name="trick_delete_mainpicture", methods={"POST"})
     */
    public function ajaxDeleteMainPicture(Request $request, TrickRepository $trickRepository, PictureRepository $pictureRepository): Response
    {
        // Get data
        $donnees = json_decode($request->getContent());

        if (isset($donnees->trick) && !empty($donnees->trick)) {
            //Init code
            $code = 200;
            
            $trick = $trickRepository->find($donnees->trick);
            $mainPicture = $pictureRepository->findBy(['trick' => $trick], ['updated_at' => 'DESC'], 1);

            //Delete main picture main picture
            if (!empty($mainPicture)) {
                //We get the fullname of the document
                $name = $mainPicture[0]->getName();
                //Delete picture in the folder
                unlink($this->getParameter("picture_tricks") . '/' . $name);

                $em = $this->getDoctrine()->getManager();
                $em->remove($mainPicture[0]);
                $em->flush();

                return new Response('Ok', $code);
            }
            return new Response('Aucune image à la une disponible', 404);
        }

        return new Response('Données incomplètes', 404);    
    }

    /**
     * @Route("/loadmore", name="trick_loadmore", methods={"POST"})
     */
    public function loadMoreTricks(Request $request, TrickRepository $trickRepository): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $data = [];
            $tricks = $trickRepository->findLoadMoreTricks($request->request->get('offset'), $trickRepository->count([]));

            foreach ($tricks as $trick) {
                $pictures = $trick->getPictures();
                if (0 != count($pictures)) {
                    //Get first picture for the trick
                    $pictureName = $pictures->first()->getName();
                } else {
                    $pictureName = '';
                }

                $data[] = [
                    'id' => $trick->getId(),
                    'imageName' => $pictureName,
                    'category' => $trick->getCategory()->getName(),
                    'name' => $trick->getName(),
                    'slug' => $trick->getSlug()
                ];
            }

            return new JsonResponse($data);
        }
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
            $trick->setUser($this->getUser());

            /* Add all the videos */
            foreach ($form->get("videos")->getData() as $video) {
                $video->setTrick($trick);
            }
            $this->uploadMainPicture($form, "picture_collection", $trick);

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
     * @Route("/{slug}", name="trick_show", methods={"GET", "POST"})
     */
    public function show(Trick $trick, Request $request, CommentRepository $commentRepository): Response
    {
        $this->setSessionTrick($trick);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', "Votre commentaire a été ajoutée avec succès !");

            return $this->redirectToRoute('trick_show', ["slug" => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'videos' => $this->convertUrlVideoService->VidProviderUrl2Player($trick),
            'comments' => $commentRepository->findCommentsLastUpdated($trick),
            'countComments' => $commentRepository->count(['trick' => $trick]),
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $this->setSessionTrick($trick);

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadMainPicture($form, "picture_collection", $trick);
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Les informations ont été mises à jour avec succès !");

            return $this->redirectToRoute('trick_show', ["slug" => $trick->getSlug()], Response::HTTP_SEE_OTHER);
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
     * @Route("/{slug}/delete", name="trick_delete")
     */
    public function delete(Request $request, Trick $trick): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', $trick->getName() . " a été supprimée avec succès !");

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * set Trick in session if user
     *
     * @param Trick $trick
     * @return void
     */
    public function setSessionTrick(Trick $trick): void
    {
        /* Save trick if is a user */
        $this->isGranted("ROLE_USER") ? $this->get('session')->set('trick', $trick) : "";
    }
}
