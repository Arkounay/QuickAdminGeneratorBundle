<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\HideInEdition;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\HideInList;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Ignore;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Sort;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Field;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\DateFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\DateTimeFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\EntityFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\IntegerFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\StringFilter;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use function Symfony\Component\String\u;

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

        if (strpos($type, 'datetime') !== false) {
            $res = '@ArkounayQuickAdminGenerator/crud/fields/_datetime.html.twig';
        } elseif (strpos($type, 'date') !== false) {
            $res = '@ArkounayQuickAdminGenerator/crud/fields/_date.html.twig';
        } else {
            $res = "@ArkounayQuickAdminGenerator/crud/fields/_$type.html.twig";
        }

        if ($this->twigLoader->exists($res)) {
            return $res;
        }

        return '@ArkounayQuickAdminGenerator/crud/fields/_default.html.twig';
    }

    public function getTwigFormType(string $route, string $name): string
    {
        $route = preg_replace('/\.{2,}/', '.', $route);

        $res = "@ArkounayQuickAdminGenerator/crud/entities/$route/$name.html.twig";

        if ($this->twigLoader->exists($res)) {
            return $res;
        }

        return "@ArkounayQuickAdminGenerator/crud/$name.html.twig";
    }
}
