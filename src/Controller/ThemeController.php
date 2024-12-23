<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\User;
use App\Form\ThemePostFormType;
use App\Form\ThemeUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ThemeController extends AbstractController
{
    #[Route('/theme', name: 'theme_list', methods: ['GET'])]
    public function cgetTheme(EntityManagerInterface $entityManager): array
    {
        return $entityManager->getRepository(Theme::class)->findAll();
    }

    #[Route('/theme/{id}', name: 'theme_detail', methods: ['GET'])]
    public function getTheme(Theme $theme): Theme
    {
        return $theme;
    }

    #[Route('/daily-theme/', name: 'daily-theme', methods: ['GET'])]
    public function dailyTheme(EntityManagerInterface $entityManager): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->findOneBy(['isActive' => true]);

        return $this->render('/theme/daily_theme.html.twig', [
            'theme' => $theme,
        ]);
    }

    #[Route('/theme/create', name: 'app_theme_create', methods: ['GET', 'POST'])]
    public function postTheme(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = new Theme();
        $form = $this->createForm(ThemePostFormType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $theme->setUser($user);

            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash('success', 'Le thème a été créé avec succès.');

            return $this->redirectToRoute('theme', ['id' => $theme->getId()]);
        }

        return $this->render('theme/postTheme.html.twig', [
            'themeUpdateForm' => $form->createView(),
        ]);
    }

    #[Route('/theme/update/{id}', name: 'app_theme_update', methods: ['PATCH'])]
    public function patchTheme(Request $request, EntityManagerInterface $entityManager, $themeId): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->find($themeId);

        if (!$theme) {
            $this->addFlash('error', 'Thème introuvable.');
            return $this->redirectToRoute('app_theme');
        }

        $form = $this->createForm(ThemeUpdateFormType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash('success', 'Le thème a été mis à jour avec succès.');

            return $this->redirectToRoute('theme', ['id' => $theme->getId()]);
        }

        return $this->render('theme/updateTheme.html.twig', [
            'themeUpdateForm' => $form,
        ]);
    }

    #[Route('/theme/delete/{id}', name: 'app_theme_delete', methods: ['DELETE'])]
    public function deleteTheme(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete' . $theme->getId(), $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_theme_update');
        }

        $entityManager->remove($theme);
        $entityManager->flush();

        $this->addFlash('success', 'Le thème a été supprimé avec succès.');

        return $this->redirectToRoute('themes');
    }
}