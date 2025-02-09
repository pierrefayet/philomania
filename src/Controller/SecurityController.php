<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function loginForm(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface  $userAuthenticator,
        LoginFormAuthenticator $authenticator,
        RefreshTokenManagerInterface $refreshTokenManager
    ): Response
    {
        $form = $this->createForm(ConnexionFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $plainPassword = $form->get('password')->getData();

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user || !$passwordHasher->isPasswordValid($user, $plainPassword)) {
                $this->addFlash('error', 'Invalid credentials.');
                return $this->redirectToRoute('app_login');
            }

            if (!$user->isVerified()) {
                $this->addFlash('error', 'Please verify your email before logging in.');
                return $this->redirectToRoute('app_login');
            }

            // ✅ Création du refresh token dans le bon bloc
            $refreshToken = $refreshTokenManager->create();
            $refreshToken->setRefreshToken(bin2hex(random_bytes(40))); // Génère un token aléatoire
            $refreshToken->setUsername($user->getUserIdentifier()); // Associe l'utilisateur
            $refreshToken->setValid((new \DateTime())->modify('+7 days'));
            $entityManager->persist($refreshToken);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

// ⛔ Erreur si le formulaire n'est pas valide ou pas soumis
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
        $formData = json_decode($request->getContent(), true);
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
        $cookie = Cookie::create('BEARER')
            ->withValue($token)
            ->withExpires(time() + 3600)
            ->withSecure($_ENV['APP_ENV'] !== 'dev')
            ->withHttpOnly(true)
            ->withSameSite('Lax');

        $response = new JsonResponse(['token' => $token], Response::HTTP_OK);
        $response->headers->setCookie($cookie);

        return $response;
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): void
    {
        $response = new Response();
        $response->headers->clearCookie('BEARER');
        $request->getSession()->invalidate();
        $response->headers->set('Location', '/login');
    }
}
