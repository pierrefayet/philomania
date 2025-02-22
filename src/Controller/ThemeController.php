<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Theme;
use App\Entity\User;
use App\Form\CommentaryFormType;
use App\Form\ThemePostFormType;
use App\Form\ThemeUpdateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ThemeController extends AbstractController
{
    #[Route('/theme/create', name: 'theme_create', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('theme_list');
        }

        return $this->render('theme/postTheme.html.twig', [
            'themeForm' => $form->createView(),
        ]);
    }

    #[Route('/theme', name: 'theme_list', methods: ['GET'])]
    public function cGetTheme(EntityManagerInterface $entityManager): Response
    {
        $themes = $entityManager->getRepository(Theme::class)->findAll();

        return $this->render('theme/themeList.html.twig', [
            'themes' => $themes,
        ]);
    }

    #[Route('/theme/{id}', name: 'theme_detail', methods: ['GET'])]
    public function getTheme(EntityManagerInterface $entityManager, $id): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->find($id);

        if (!$theme) {
            throw $this->createNotFoundException('Thème introuvable.');
        }

        return $this->render('theme/themeDetail.html.twig', [
            'theme' => $theme,
        ]);
    }

    #[Route('/daily-theme', name: 'daily-theme', methods: ['GET', 'POST'])]
    public function dailyTheme(Request $request, EntityManagerInterface $entityManager): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->findOneBy([
            'isActive' => true
        ]);

        if (!$theme) {
            $this->addFlash('error', 'Aucun thème actif trouvé.');
            return $this->redirectToRoute('theme_list');
        }

        $commentaries = $entityManager->getRepository(Commentary::class)->findByThemeWithUser($theme->getId());
        
        $commentary = new Commentary();
        $form = $this->createForm(CommentaryFormType::class, $commentary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'Vous devez être connecté pour commenter.');
                return $this->redirectToRoute('daily-theme');
            }

            $commentary->setUser($this->getUser());
            $commentary->setTheme($theme);
            $commentary->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($commentary);
            $entityManager->flush();

            return $this->redirectToRoute('daily-theme');
        }

        return $this->render('/theme/dailyTheme.html.twig', [
            'theme' => $theme,
            'commentaries' => $commentaries,
            'form' => $form->createView()
        ]);
    }

    #[Route('/theme/update/{id}', name: 'app_theme_update', methods: ['GET', 'POST', 'PATCH'])]
    public function patchTheme(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->find($id);

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

            return $this->redirectToRoute('theme_list', ['id' => $theme->getId()]);
        }

        return $this->render('theme/updateTheme.html.twig', [
            'themeUpdateForm' => $form,
        ]);
    }

    #[Route('/theme/delete/{id}', name: 'app_theme_delete', methods: ['POST', 'DELETE'])]
    public function deleteTheme(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        $submittedToken = $request->request->get('_token');

        $entityManager->remove($theme);
        $entityManager->flush();

        $this->addFlash('success', 'Le thème a été supprimé avec succès.');

        return $this->redirectToRoute('theme_list');
    }
}