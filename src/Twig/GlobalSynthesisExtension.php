<?php

namespace App\Twig;


use App\Repository\SynthesisRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalSynthesisExtension extends AbstractExtension implements GlobalsInterface
{
    private SynthesisRepository $synthesisRepository;

    public function __construct(SynthesisRepository $synthesisRepository)
    {
        $this->synthesisRepository = $synthesisRepository;
    }

    public function getGlobals(): array
    {
        return [
            'syntheses' => $this->synthesisRepository->findAll()
        ];
    }
}