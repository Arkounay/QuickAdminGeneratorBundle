<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CrudControllerTest extends WebTestCase
{

    public function testRoutes(): void
    {
        $client = static::createClient();
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $bufferedOutput =  new BufferedOutput();
        $input = new ArrayInput(['command' => 'debug:router']);
        $application->run($input, $bufferedOutput);
        $res = $bufferedOutput->fetch();
        self::assertStringContainsString('qag.category', $res);
        self::assertStringContainsString('qag.category_create', $res);
        self::assertStringContainsString('qag.category_edit', $res);
        self::assertStringContainsString('qag.category_delete_batch', $res);
        self::assertStringContainsString('qag.category_toggle_boolean_post', $res);

        $client->request('GET', '/admin/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.page-wrapper', 'Welcome');
        self::assertStringContainsString('Categories', $client->getResponse()->getContent());

        $client->request('GET', '/admin/category/');
        self::assertResponseIsSuccessful();
    }

    public function testMenu(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('#navbar-menu', 'Submenu');
        self::assertSelectorTextContains('#navbar-menu', 'Categories');
        self::assertSelectorExists('#navbar-menu .badge-number');
        self::assertSelectorTextContains('#navbar-menu .badge-number', '25');
        self::assertSelectorTextNotContains('#navbar-menu', 'Category disabled');
    }

    public function testCreation(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/category/create');
        self::assertResponseIsSuccessful();

        $client->submitForm('Save', [
            'form[name]' => 'New category'
        ]);

        self::assertResponseRedirects('/admin/category/');
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('table', 'New category');
    }

    public function testSearch(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/category/?search=NotFound');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/category/?search=New');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('One result', $client->getResponse()->getContent());

        $client->request('GET', '/admin/global-search?q=Lorem%200');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('<a href="/admin/article-filters/view/1/', $client->getResponse()->getContent());
        self::assertStringNotContainsString('<a href="/admin/article-filters/view/2', $client->getResponse()->getContent());
    }

    public function testEdition(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/category/edit/1/');
        self::assertResponseIsSuccessful();
        $client->submitForm('Save', [
            'form[name]' => 'Edited'
        ]);
        self::assertResponseRedirects('/admin/category/');
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextNotContains('table', 'New category');
        self::assertSelectorTextContains('table', 'Edited');

        $client->request('GET', '/admin/category/edit/2/');
        self::assertTrue($client->getResponse()->isNotFound());
    }

    public function testActions(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/category-extra-actions/create');
        self::assertResponseStatusCodeSame(401);

        $client->request('GET', '/admin/category-extra-actions/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.custom-global-action');
        self::assertStringContainsString('My custom action label', $client->getResponse()->getContent());
    }

    public function testDeletion(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/category/');
        $token = $crawler->filter('#delete-modal input[name="token"]')->attr('value');

        $client->request('POST', '/admin/category/delete/1/', [
            'token' => $token
        ]);
        self::assertResponseRedirects('/admin/category/');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('successfully deleted', $client->getResponse()->getContent());
        self::assertStringContainsString('No results', $client->getResponse()->getContent());
    }

    public function testBatchDeletion(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/article-filters/edit/5/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/article-filters/edit/6/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/article-filters/');
        $client->submitForm('Delete', [
            'batch-actions[5]' => true,
            'batch-actions[6]' => true
        ]);

        $client->request('GET', '/admin/category/edit/5/');
        self::assertTrue($client->getResponse()->isNotFound());

        $client->request('GET', '/admin/category/edit/6/');
        self::assertTrue($client->getResponse()->isNotFound());
    }


    public function testAnnotations(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/article-filters/');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Date of creation', $client->getResponse()->getContent());

        // position
        self::assertSelectorTextContains('.page-body .table-card.table-responsive table thead th:nth-child(2)', 'Name');

        $client->request('GET', '/admin/article-filters/create');
        self::assertStringContainsString('The name of the Article.', $client->getResponse()->getContent());
    }

    public function testFilters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/article-filters/filterFormAjax');
        self::assertResponseIsSuccessful();

        self::assertSelectorExists('input[name="filter[name]"]');
        self::assertSelectorExists('select[name="filter[published]"]');

        $client->request('GET', '/admin/article-filters/');
        self::assertSelectorTextContains('.table-pagination strong', 22);

        $client->request('GET', '/admin/article-filters/', ([
            'filter' => [
                'published' => 1,
                'createdAt' => [
                    "choice" => '<',
                    "date_start" => null,
                    "date_end" => null,
                    "date" => null,
                  ],
                'name' => null
            ]
        ]));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.table-pagination strong', 11);
    }


    public function testToggleBoolean(): void
    {
        $client = static::createClient();
        $client->request('POST', '/admin/article-filters/toggleBooleanPost/10/', [
            'index' => 'published',
            'checked' => true
        ]);
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/article-filters/edit/10/');
        self::assertSelectorExists('input[type="checkbox"][name="form[published]"][checked="checked"]');
    }

    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/article-filters/view/10/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/category/view/2/');
        self::assertResponseStatusCodeSame(401);
    }

    public function testRights(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/category-extra-actions/create');
        self::assertResponseStatusCodeSame(401);

        $client->request('GET', '/admin/disabled/');
        self::assertResponseStatusCodeSame(401);
    }

    public function testFetchModeManual(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/person/create');
        self::assertResponseStatusCodeSame(200);
        self::assertCount(3, $crawler->filter('.page-body input'));
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

}