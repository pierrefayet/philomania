<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ModerationController extends AbstractController
{
    #[Route('/moderation/user/ban/{id}', name: 'ban_user', methods: ['POST', 'PATCH'])]
    public function banUser(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $this->addFlash('error', 'Vous ne pouvez pas bannir un administrateur.');

            return $this->redirectToRoute('dashboard');
        }

        $user->setRoles(['ROLE_BANNED']);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('warning', 'Utilisateur banni.');

        return $this->redirectToRoute('moderation_dashboard');
    }

    #[Route('/moderation', name: 'dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les commentaires
        $comments = $entityManager->getRepository(Commentary::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('moderation/moderation.html.twig', [
            'comments' => $comments,
            'users' => $users
        ]);
    }

    #[Route('/moderation/comment/delete/{id}', name: 'delete_comment', methods: ['POST', 'DELETE'])]
    public function deleteComment(Commentary $commentary, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager->remove($commentary);
        $entityManager->flush();
        $this->addFlash('success', 'Commentaire supprimé.');

        return $this->redirectToRoute('moderation_dashboard');
    }
}
