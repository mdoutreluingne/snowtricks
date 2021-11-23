<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(UserRepository $userRepository, TrickRepository $trickRepository, CommentRepository $commentRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
            'tricks' => $trickRepository->findBy([], ['updated_at' => 'DESC']),
            'comments' => $commentRepository->findAll([], ['updated_at' => 'DESC'])
        ]);
    }
}
