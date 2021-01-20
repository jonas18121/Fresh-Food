<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'svgIcon'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('truncate', [$this, 'truncateTwig']),
        ];
    }

    /**
     * Génère le code HTML pour une icone SVG
     */
    public function svgIcon(string $name): string
    {
        return <<<HTML
        <svg class="icon">
          <use xlink:href="/sprite.svg#{$name}"></use>
        </svg>
        HTML;
    }
    
    public function truncateTwig(string $element): string
    {
        if(strlen($element) >= 22){
            return substr($element, 0, 22) . '...';
        }
        return $element;
    }
}