<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Theme;
use App\Entity\User;
use App\Form\CommentaryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentaryController extends AbstractController
{
    #[Route('/commentary/create/{themeId}', name: 'app_commentary_create', methods: ['POST', 'GET'])]
    public function postCommentary(Request $request, EntityManagerInterface $entityManager, int $themeId): Response
    {
        $theme = $entityManager->getRepository(Theme::class)->find($themeId);

        if (!$theme) {
            throw $this->createNotFoundException('Theme not found.');
        }

        $commentary = new Commentary();
        $form = $this->createForm(CommentaryFormType::class, $commentary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $commentary->setUser($user);
            $commentary->setTheme($theme);

            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été créé avec succès.');

            return $this->redirectToRoute('daily-theme');
        }

        return $this->render('commentary/moderation.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commentaries', name: 'commentaries_list', methods: ['GET'])]
    public function cgetCommentary(EntityManagerInterface $entityManager): Response
    {
        $commentaries = $entityManager->getRepository(Commentary::class)->findAll();

        return $this->render('commentary/moderation.html.twig', [
            'commentaries' => $commentaries
        ]);
    }

    #[Route('/commentary/{id}', name: 'commentaries_detail', methods: ['GET'])]
    public function getCommentaries(Commentary $commentary): Response
    {
        return $this->render('commentary/moderation.html.twig', [
            'commentary' => $commentary,
        ]);
    }

    #[Route('/commentary/update/{id}', name: 'app_commentary_update', methods: ['PUT'])]
    public function updateCommentary(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        $commentary = new Commentary();
        $form = $this->createForm(CommentaryFormType::class, $commentary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw new \LogicException('Expected instance of App\Entity\User. Got ' . get_debug_type($user));
            }

            $commentary->setUserId($user->getId());

            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été mise à jour avec succès.');

            return $this->redirectToRoute('daily-theme');
        }


        return $this->render('commentary/moderation.html.twig', [
            'controller_name' => 'CommentaryController',
        ]);
    }

    #[Route('/commentary/delete/{id}', name: 'app_commentary_delete', methods: ['DELETE'])]
    public function deleteCommentary(Request $request, EntityManagerInterface $entityManager, Commentary $commentary): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->getUser() !== $commentary->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce commentaire.');
        }

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete' . $commentary->getId(), $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('daily-theme');
        }

        $entityManager->remove($commentary);
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');

        return $this->redirectToRoute('daily-theme');
    }
}
