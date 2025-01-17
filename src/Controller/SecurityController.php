<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionFormType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function loginForm(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $form = $this->createForm(ConnexionFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $plainPassword = $form->get('password')->getData();

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$passwordHasher->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('error', 'Invalid credentials.');
                return $this->redirectToRoute('app_login');
            }

            if (!$user) {
                $this->addFlash('error', 'Invalid credentials.');
                return $this->redirectToRoute('app_login');
            }

            if (!$user->isVerified()) {
                $this->addFlash('error', 'Please verify your email before logging in.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('success', 'You are successfully logged in.');

            // Redirection vers la page d'accueil
            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function loginWithJwt(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface    $jwtManager
    ): Response
    {
        $formData = $request->request->all('connexion_form');
        $email = $formData['email'] ?? null;
        $plainPassword = $formData['password'] ?? null;

        if (!$email || !$plainPassword) {
            return $this->json(['error' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($user, $plainPassword)) {
            return $this->json(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->isVerified()) {
            return $this->json(['error' => 'Please verify your email address before logging in.'], Response::HTTP_FORBIDDEN);
        }

        $token = $jwtManager->create($user);

        return $this->json(['token' => $token], Response::HTTP_OK);
    }


    #[
        Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
