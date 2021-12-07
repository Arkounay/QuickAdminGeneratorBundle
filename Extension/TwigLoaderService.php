<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Twig\Environment;
use Twig\Loader\LoaderInterface;

class TwigLoaderService
{

    /**
     * @var LoaderInterface
     */
    private $twigLoader;

    public function __construct(Environment $environment)
    {
        $this->twigLoader = $environment->getLoader();
    }

    public function getTwigPartialByFieldType(string $type, ?string $twigName = null): string
    {
        if ($twigName !== null) {
            return "@ArkounayQuickAdminGenerator/crud/fields/_$twigName.html.twig";
        }

        if (str_contains($type, 'datetime')) {
            $res = '@ArkounayQuickAdminGenerator/crud/fields/_datetime.html.twig';
        } elseif (str_contains($type, 'date')) {
            $res = '@ArkounayQuickAdminGenerator/crud/fields/_date.html.twig';
        } else {
            $res = "@ArkounayQuickAdminGenerator/crud/fields/_$type.html.twig";
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
