<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filters;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Article;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * More advanced crud with filters
 */
class ArticleController extends Crud
{

    public function getEntity(): string
    {
        return Article::class;
    }

    public function getRoute(): string
    {
        return 'article-filters';
    }

    protected function getFilters(): Filters
    {
        return parent::getFilters()
            ->add('name')
            ->add('createdAt')
            ->add('published')
        ;
    }

}
