<?php

namespace App\Twig;

use App\Repository\ThemeRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalThemesExtension extends AbstractExtension implements GlobalsInterface
{
    private ThemeRepository $themeRepository;

    public function __construct(ThemeRepository $themeRepository)
    {
        $themes = $this->themeRepository = $themeRepository;
    }

    public function getGlobals(): array
    {
        return [
            'themes' => $this->themeRepository->findAll()
        ];
    }
}