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
        $client = self::createClient();
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
        self::assertSelectorTextContains('.content', 'Welcome');
        self::assertStringContainsString('Categories', $client->getResponse()->getContent());

        $client->request('GET', '/admin/category/');
        self::assertResponseIsSuccessful();
    }

    public function testCreation(): void
    {
        $client = self::createClient();

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
        $client = self::createClient();

        $client->request('GET', '/admin/category/?search=NotFound');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/category/?search=New');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('One result', $client->getResponse()->getContent());

        $client->request('GET', '/admin/global-search?q=Lorem%200');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('<a href="/admin/article-filters/edit/1/', $client->getResponse()->getContent());
        self::assertStringNotContainsString('<a href="/admin/article-filters/edit/2', $client->getResponse()->getContent());
    }

    public function testEdition(): void
    {
        $client = self::createClient();

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
        $client = self::createClient();

        $client->request('GET', '/admin/category-extra-actions/create');
        self::assertResponseStatusCodeSame(404);

        $client->request('GET', '/admin/category-extra-actions/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.custom-global-action');
        self::assertStringContainsString('My custom action label', $client->getResponse()->getContent());
    }

    public function testDeletion(): void
    {
        $client = self::createClient();

        $token = $client->getContainer()->get('security.csrf.token_manager')->getToken('delete');

        $client->request('POST', '/admin/category/delete/1/', [
            'token' => $token
        ]);
        self::assertResponseRedirects('/admin/category/');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('successfully deleted', $client->getResponse()->getContent());
        self::assertStringContainsString('No result', $client->getResponse()->getContent());
    }

    public function testAnnotations(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/article-filters/');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Date of creation', $client->getResponse()->getContent());
    }

    public function testFilters(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/article-filters/filterFormAjax');
        self::assertResponseIsSuccessful();

        self::assertSelectorExists('input[name="filter[name]"]');
        self::assertSelectorExists('select[name="filter[published]"]');

        $client->request('GET', '/admin/article-filters/');
        self::assertSelectorTextContains('.table-pagination strong', 24);

        $client->request('GET', '/admin/article-filters/', [
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
        ]);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.table-pagination strong', 12);
    }

    public function testRights(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/category-extra-actions/create');
        self::assertResponseStatusCodeSame(404);
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

}