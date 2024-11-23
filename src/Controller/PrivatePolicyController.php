<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrivatePolicyController extends AbstractController
{
    #[Route('/charte', name: 'charte')]
    public function charte(): Response
    {
       return $this->render('privatePolicy.html.twig');
    }
}