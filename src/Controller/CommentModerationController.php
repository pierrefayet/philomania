<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentModerationController extends AbstractController
{
    #[Route('/comment/moderation', name: 'app_comment_moderation')]
    public function index(): Response
    {
        return $this->render('comment_moderation/moderation.html.twig', [
            'controller_name' => 'CommentModerationController',
        ]);
    }
}
