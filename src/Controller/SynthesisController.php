<?php

namespace App\Controller;

use App\Entity\Synthesis;
use App\Entity\Theme;
use App\Entity\User;
use App\Form\SynthesisCreateFormType;
use App\Form\SynthesisUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SynthesisController extends AbstractController
{
    #[Route('/synthesis', name: 'synthesis_list', methods: ['GET'])]
    public function cgetSynthesis(EntityManagerInterface $entityManager): Response
    {
        $synthesis = $entityManager->getRepository(Synthesis::class)->findAll();

        return $this->render('/synthesis.html.twig', [
            'synthesis' => $synthesis
        ]);
    }

    #[Route('/synthesis/{id}', name: 'synthesis_detail', methods: ['GET'])]
    public function getSynthesis(Synthesis $synthesis): Response
    {
        return $this->render('/synthesis.html.twig', [
            'synthesis' => $synthesis,
        ]);
    }

    #[Route('/synthesis/create/{themeId}', name: 'app_synthesis_create', methods: ['POST'])]
    public function postSynthesis(Request $request, EntityManagerInterface $entityManager, int $themeId): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->find($themeId);

        if (!$theme) {
            throw $this->createNotFoundException('Theme not found.');
        }
        $synthesis = new Synthesis();
        $form = $this->createForm(SynthesisCreateFormType::class, $synthesis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $synthesis->setUser($user);
            $synthesis->setTheme($theme);

            $entityManager->persist($synthesis);
            $entityManager->flush();

            $this->addFlash('success', 'La synthèse a été créée avec succès.');

            return $this->redirectToRoute('theme', ['id' => $theme->getId()]);
        }

        return $this->render('synthesis/synthesis.html.twig', [
            'synthesisForm' => $form,
        ]);
    }

    #[Route('/synthesis/update/{id}', name: 'app_synthesis_update', methods: ['PUT', 'POST'])]
    public function putSynthesis(Request $request, EntityManagerInterface $entityManager, Synthesis $synthesis): Response
    {
        $form = $this->createForm(SynthesisUpdateFormType::class, $synthesis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $synthesis->setUser($user);
            $entityManager->flush();

            $this->addFlash('success', 'La synthèse a été mise à jour avec succès.');

            return $this->redirectToRoute('synthesis_detail', ['id' => $synthesis->getId()]);
        }

        return $this->render('synthesis/updateSynthesis.html.twig', [
            'synthesisForm' => $form,
        ]);
    }

    #[Route('/synthesis/delete/{id}', name: 'app_synthesis_delete', methods: ['DELETE'])]
    public function deleteSynthesis(Request $request, EntityManagerInterface $entityManager, Synthesis $synthesis): Response
    {
        $submittedToken = $request->request->get('_token');

        $entityManager->remove($synthesis);
        $entityManager->flush();

        $this->addFlash('success', 'La synthèse a été supprimée avec succès.');

        return $this->redirectToRoute('synthesis_list');
    }
}