<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ThemeActiveController extends AbstractController
{
    #[Route('/theme/active', name: 'app_theme_active')]
    public function index(): Response
    {
        return $this->render('theme_active/index.html.twig', [
            'controller_name' => 'ThemeActiveController',
        ]);
    }
}
