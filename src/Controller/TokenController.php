<?php

namespace App\Controller;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    #[Route('/api/token/refresh', name: 'api_token_refresh', methods: ['POST'])]
    public function refreshToken(Request $request, RefreshTokenManagerInterface $refreshTokenManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $refreshToken = $content['refresh_token'] ?? null;

        if (!$refreshToken) {
            return $this->json(['error' => 'Refresh token is missing'], Response::HTTP_BAD_REQUEST);
        }

        $refreshTokenEntity = $refreshTokenManager->get($refreshToken);
        if (!$refreshTokenEntity) {
            return $this->json(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $refreshTokenEntity->getUsername();
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'token' => $refreshTokenEntity->getRefreshToken(),
        ]);
    }
}