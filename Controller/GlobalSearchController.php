<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GlobalSearchController extends AbstractController
{

    public function __construct(
        /** @var iterable|Crud[] */
        private readonly iterable $cruds,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TranslatorInterface $translator
    )
    {}

    public function search(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('@ArkounayQuickAdminGenerator/crud/_ajax_global_search.html.twig', [
                'results' => $this->getSearchResults($request)
            ]);
        }

        return $this->render('@ArkounayQuickAdminGenerator/crud/global_search.html.twig', [
            'results' => $this->getSearchResults($request, 20)
        ]);
    }

    private function getSearchResults(Request $request, int $maxResults = 5): array
    {
        $query = trim($request->query->get('q', ''));

        $searchResult = [];
        foreach ($this->cruds as $crud) {
            if ($crud->isEnabled() && $crud->isSearchable()) {
                $crud->load($request);
                $queryBuilder = $crud->getListQueryBuilder();
                $crud->search($queryBuilder, $query);
                $queryBuilder->setMaxResults(10);
                $paginator = new Paginator($queryBuilder);
                $paginator->getQuery()->setFirstResult(0)->setMaxResults($maxResults);

                $count = count($paginator);

                if ($count > 0) {
                    $items = [];
                    foreach ($paginator as $entity) {
                        $item = [];
                        $event = new GenericEvent($entity);
                        $this->eventDispatcher->dispatch($event, 'qag.events.quick_search_item');
                        if ($event->hasArgument('item_override')) {
                            $item = $event->getArgument('item_override');
                        } else {
                            $actions = $crud->getActions($entity);
                            if (\count($actions) > 0) {
                                $url = null;
                                if ($crud->isViewable($entity)) {
                                    $url = $this->generateUrl("qag.{$crud->getRoute()}_view", ['id' => $entity->getId(), 'highlight' => $query]);
                                } elseif ($crud->isEditable($entity)) {
                                    $url = $this->generateUrl("qag.{$crud->getRoute()}_edit", ['id' => $entity->getId(), 'highlight' => $query]);
                                }

                                if ($url !== null) {
                                    $item = [
                                        'entity' => $entity,
                                        'url' => $url
                                    ];
                                }
                            }
                        }
                        if ($item) {
                            $items[] = $item;
                        }
                    }
                    if (!empty($items)) {
                        if ($count > \count($items)) {
                            $items[] = [
                                'entity' => $this->translator->trans('See more...'),
                                'url' => $this->generateUrl("qag.{$crud->getRoute()}", ['search' => $query])
                            ];
                        }
                        $event = new GenericEvent($crud, ['search_result' => $items, 'count' => $count]);
                        $this->eventDispatcher->dispatch($event, 'qag.events.quick_search_crud');
                        if ($event->hasArgument('search_result_override')) {
                            $searchResult[] = $event->getArgument('search_result_override');
                        } else {
                            $searchResult[] = [
                                'entity' => $crud->getPluralName(),
                                'crud_url' => $this->generateUrl("qag.{$crud->getRoute()}", ['search' => $query]),
                                'items' => $items,
                                'count' => $count,
                            ];
                        }
                    }
                }
            }
        }

        return $searchResult;
    }

}