<?php

namespace App\Twig;

use App\Repository\ThemeRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalThemesExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(readonly ThemeRepository $themeRepository)
    {
    }

    public function getGlobals(): array
    {
        $activeTheme = $this->themeRepository->findOneBy(['isActive' => true]);
        return [
            'theme' => $activeTheme ?? null,
        ];
    }
}