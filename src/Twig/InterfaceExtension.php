<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class InterfaceExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('colorear', [$this, 'colorear'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('colorear', [$this, 'colorear']),
        ];
    }

    public function colorear($value)
    {
        $badge_class=$value ? 'success' : 'danger';
        $badge_label=$value ? 'SI' : 'NO';
        return '<span class="m-badge m-badge--'.$badge_class.' m--font-boldest m-badge--wide">'.$badge_label.'</span>';
    }
}
