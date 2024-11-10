<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authUtils, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        dump($request->request->all());
        $error = $authUtils->getLastAuthenticationError();
        $user = new User();
        $form = $this->createForm(ConnexionFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $plainPassword = $form->get('password')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user === null) {
                $this->addFlash('error', 'Email incorrect');
                return $this->render('security/login.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error,
                ]);
            }

            if (!$passwordHasher->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('error', 'Mot de passe incorrect');
                return $this->render('security/login.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error,
                ]);
            }

           if (!$user->isVerified()) {
                $this->addFlash('error', 'Veuillez confirmer votre adresse email avant de vous connecter.');
                return $this->render('security/login.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error,
                ]);
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
