<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Twig\Extension;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Twig\Runtime\QagExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class QagExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('qag_action_href', [QagExtensionRuntime::class, 'getActionHref']),
            new TwigFunction('qag_render_icon', [QagExtensionRuntime::class, 'icon'], ['is_safe' => ['html']]),
            new TwigFunction('qag', [QagExtensionRuntime::class, 'getQag'])
        ];
    }

}
