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
    #[Route('/synthesis/create', name: 'app_synthesis_create', methods: ['GET', 'POST'])]
    public function postSynthesis(Request $request, EntityManagerInterface $entityManager): Response
    {
        $themes = $entityManager->getRepository(Theme::class)->findAll();

        if (!$themes) {
            return $this->render('theme/themeList.html.twig', [
                'error' => 'Aucun thème trouvé. Veuillez d\'abord en créer un avant de faire une synthèse.'
            ]);
        }

        $synthesis = new Synthesis();
        $form = $this->createForm(SynthesisCreateFormType::class, $synthesis, [
            "themes" => $themes
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $theme = $synthesis->getTheme();
            if (!$theme) {
                throw new \LogicException('Un thème doit être sélectionné.');
            }

            $synthesis->setUser($user);
            $synthesis->setTheme($theme);

            $entityManager->persist($synthesis);
            $entityManager->flush();

            $this->addFlash('success', 'La synthèse a été créée avec succès.');

            return $this->redirectToRoute('synthesis_list');
        }

        return $this->render('synthesis/postSynthesis.html.twig', [
            'synthesisCreateForm' => $form->createView(),
        ]);
    }

    #[Route('/synthesis', name: 'synthesis_list', methods: ['GET'])]
    public function cgetSynthesis(EntityManagerInterface $entityManager): Response
    {
        $synthesis = $entityManager->getRepository(Synthesis::class)->findAll();

        return $this->render('/synthesis/synthesisList.html.twig', [
            'synthesis' => $synthesis
        ]);
    }

    #[Route('/synthesis/{id}', name: 'synthesis_detail', methods: ['GET'])]
    public function getSynthesis(Synthesis $synthesis): Response
    {
        return $this->render('/synthesis/synthesisDetail.html.twig', [
            'synthesis' => $synthesis,
        ]);
    }

    #[Route('/synthesis/update/{id}', name: 'app_synthesis_update', methods: ['GET', 'PUT', 'POST'])]
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
            'synthesisUpdateForm' => $form,
        ]);
    }

    #[Route('/synthesis/delete/{id}', name: 'app_synthesis_delete', methods: ['POST', 'DELETE'])]
    public function deleteSynthesis(EntityManagerInterface $entityManager, int $id): Response
    {
        $synthesis = $entityManager->getRepository(Synthesis::class)->find($id);

        if (!$synthesis) {
            throw $this->createNotFoundException('Synthèse non trouvée.');
        }

        $entityManager->remove($synthesis);
        $entityManager->flush();

        $this->addFlash('success', 'La synthèse a été supprimée avec succès.');

        return $this->redirectToRoute('synthesis_list');
    }
}