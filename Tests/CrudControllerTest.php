<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

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
        self::assertStringContainsString('qag.category                       ANY      ANY      ANY    /admin/category/', $res);
        self::assertStringContainsString('qag.category_create                ANY      ANY      ANY    /admin/category/create', $res);
        self::assertStringContainsString('qag.category_edit                  ANY      ANY      ANY    /admin/category/edit/{id}/', $res);
        self::assertStringContainsString('qag.category_delete_batch          ANY      ANY      ANY    /admin/category/deleteBatch', $res);
        self::assertStringContainsString('qag.category_toggle_boolean_post   POST     ANY      ANY    /admin/category/toggleBooleanPost/{id}/', $res);

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

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

}