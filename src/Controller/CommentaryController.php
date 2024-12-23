<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Theme;
use App\Entity\User;
use App\Form\CommentaryFormType;
use App\Form\ConnexionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentaryController extends AbstractController
{
    #[Route('/commentaries', name: 'commentaries_list', methods: ['GET'])]
    public function cGetCommentary(EntityManagerInterface $entityManager): Response
    {
        $commentaries = $entityManager->getRepository(Commentary::class)->findAll();

        return $this->render('commentary/postCommentary.html.twig', [
            'commentaries' => $commentaries
        ]);
    }

    #[Route('/commentary/{id}', name: 'commentaries_detail', methods: ['GET'])]
    public function getCommentaries(Commentary $commentary): Response
    {
        return $this->render('commentary/postCommentary.html.twig', [
            'commentary' => $commentary,
        ]);
    }

    #[Route('/commentary/create', name: 'app_commentary', methods: ['POST'])]
    public function postCommentary(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
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
            $commentary->setTheme($theme);

            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été créé avec succès.');

            return $this->redirectToRoute('theme', ['id' => $theme->getId()]);
        }

        return $this->render('synthesis/synthesis.html.twig', [
            'synthesisForm' => $form,
        ]);
    }

    #[Route('/commentary/update/{id}', name: 'app_commentary_update', methods: ['PUT'])]
    public function updateCommentary(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        $commentary = new Commentary();
        $form = $this->createForm(ConnexionFormType::class, $commentary);
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

            return $this->redirectToRoute('theme', ['id' => $theme->getId()]);
        }


        return $this->render('commentary/postCommentary.html.twig', [
            'controller_name' => 'CommentaryController',
        ]);
    }

    #[Route('/commentary/delete/{id}', name: 'app_commentary_delete', methods: ['DELETE'])]
    public function deleteCommentary(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
            $submittedToken = $request->request->get('_token');

            if (!$this->isCsrfTokenValid('delete' . $theme->getId(), $submittedToken)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_theme_update');
            }

            $entityManager->remove($theme);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire  a été supprimé avec succès.');

            return $this->redirectToRoute('theme');
    }

    #[Route('/commentary/delete', name: 'app_commentary_delete_all', methods: ['DELETE'])]
    public function deleteCommentaries(Request $request, EntityManagerInterface $entityManager, Theme $theme): Response
    {
        {
            $submittedToken = $request->request->get('_token');

            if (!$this->isCsrfTokenValid('delete' . $theme->getId(), $submittedToken)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_theme_update');
            }

            $entityManager->remove($theme);
            $entityManager->flush();

            $this->addFlash('success', 'Les commentaires ont été supprimés avec succès.');

            return $this->redirectToRoute('theme');
        }
    }
}
