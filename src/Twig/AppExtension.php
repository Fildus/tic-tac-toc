<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isCurrentUrl', [$this, 'isCurrentUrl']),
        ];
    }

    public function isCurrentUrl(string $url): bool
    {
        return $this->requestStack->getCurrentRequest()->get('_route') === $url;
    }
}
