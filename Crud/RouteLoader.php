<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Crud;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\AdminInterface;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\DashboardController;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\GlobalSearchController;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Symfony\Bundle\FrameworkBundle\Routing\RouteLoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use function Symfony\Component\String\u;

class RouteLoader implements RouteLoaderInterface
{

    /**
     * @var iterable|Crud[]
     */
    private $cruds;

    /**
     * @var DashboardController
     */
    private $abstractAdminController;

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var array
     */
    private $config;

    public function __construct(iterable $cruds, AdminInterface $admin, array $config)
    {
        $this->cruds = $cruds;
        $this->admin = $admin;
        $this->config = $config;
    }

    public function __invoke($resource, string $type = null)
    {
        $routes = new RouteCollection();

        foreach ($this->cruds as $crud) {
            foreach ($crud->getAllActions() as $action) {
                // create routes from the entity's actions.
                $suffix = $action;
                if ($action === 'list') {
                    $suffix = '';
                }
                $isBatch = strpos($action, 'Batch') === strlen($action) - 5;
                if ($isBatch) {
                    $suffix = substr($suffix, 0, -5) . 'Batch';
                }
                $route = new Route("/{$crud->getRoute()}/{$suffix}", ['_controller' => get_class($crud) . "::{$action}Action"]);
                $routeName = "qag.{$crud->getRoute()}";
                if ($suffix) {
                    $routeName .= '_' . u($suffix)->snake();
                    $globalActions = $crud->getGlobalActions();
                    $globalActionsIndexes = [];
                    if ($globalActions !== null) {
                        foreach ($crud->getGlobalActions() as $a) {
                            /** @var Action $a */
                            $globalActionsIndexes[] = $a->getIndex();
                        }
                    }
                    if (!$isBatch) {
                        if (!in_array($action, $globalActionsIndexes, true) && !u($route->getPath())->containsAny(['Global', 'Ajax'])) {
                            $route->setPath($route->getPath().'/{id}/');
                        }
                        if (u($action)->endsWith('Post')) {
                            $route->setMethods('POST');
                        } elseif (u($action)->endsWith('Get')) {
                            $route->setMethods('GET');
                        }
                    }
                }
                $routes->add($routeName, $route);
            }
        }

        $routes->add('qag.dashboard', new Route('/', ['_controller' => get_class($this->admin) . "::dashboard"]));
        if ($this->config['global_search']) {
            $routes->add('qag.global_search', new Route('/global-search', ['_controller' => GlobalSearchController::class . "::search"]));
        }

        return $routes;
    }

}