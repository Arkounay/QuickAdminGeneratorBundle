<?php

use Arkounay\Bundle\QuickAdminGeneratorBundle\ArkounayQuickAdminGeneratorBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

return [
    FrameworkBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    TwigExtraBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    KnpPaginatorBundle::class => ['all' => true],
    StimulusBundle::class => ['all' => true],
    ArkounayQuickAdminGeneratorBundle::class => ['all' => true],
];
