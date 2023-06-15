<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Twig\Environment;
use Twig\Loader\LoaderInterface;

readonly class TwigLoaderService
{

    private LoaderInterface $twigLoader;

    public function __construct(Environment $environment)
    {
        $this->twigLoader = $environment->getLoader();
    }

    public function getTwigPartialByFieldType(string $type, ?string $twigName = null): string
    {
        if ($twigName !== null) {
            return Field::twigPath($twigName);
        }

        if (str_contains($type, 'datetime')) {
            $res = Field::twigPath('datetime');
        } elseif (str_contains($type, 'date')) {
            $res = Field::twigPath('date');
        } else {
            $res = Field::twigPath($type);
        }

        if ($this->twigLoader->exists($res)) {
            return $res;
        }

        return '@ArkounayQuickAdminGenerator/crud/fields/_default.html.twig';
    }

    public function guessTwigFilePath(string $route, string $name): string
    {
        $route = preg_replace('/\.{2,}/', '.', $route);

        $res = "@ArkounayQuickAdminGenerator/crud/entities/$route/$name.html.twig";

        if ($this->twigLoader->exists($res)) {
            return $res;
        }

        return "@ArkounayQuickAdminGenerator/crud/$name.html.twig";
    }
}
