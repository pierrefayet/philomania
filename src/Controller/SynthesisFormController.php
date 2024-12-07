<?php

namespace App\Controller;

use App\Entity\Synthesis;
use App\Entity\Theme;
use App\Entity\User;
use App\Form\SynthesisFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SynthesisFormController extends AbstractController
{
    #[Route('/form/synthesis', name: 'app_synthesis_form')]
    public function synthesisForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->find(());
        $synthesis = new Synthesis();
        $form = $this->createForm(SynthesisFormType::class, $synthesis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $synthesis->setUser($user);

            $entityManager->persist($synthesis);
            $entityManager->flush();

            return $this->redirectToRoute('theme');
        }

        return $this->render('form/synthesis.html.twig', [
            'synthesisForm' => $form,
        ]);
    }
}