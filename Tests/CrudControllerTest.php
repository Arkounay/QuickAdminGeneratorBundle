<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class CrudControllerTest extends WebTestCase
{

    public function test()
    {
        $client = self::createClient(['app.user' => '1']);
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);

        $input = new ArrayInput(['command' => 'doctrine:database:drop', '--no-interaction' => true, '--force' => true]);
        $application->run($input, new ConsoleOutput());

        $input = new ArrayInput(['command' => 'doctrine:database:create', '--no-interaction' => true]);
        $application->run($input, new ConsoleOutput());

        $input = new ArrayInput(['command' => 'doctrine:schema:update', '--force' => true]);
        $application->run($input, new ConsoleOutput());

        $input = new ArrayInput(['command' => 'debug:router']);
        $application->run($input, new ConsoleOutput());

        $client->request('GET', '/admin/category/');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.content', 'Welcome');
    }

    protected static function getKernelClass()
    {
        return TestKernel::class;
    }

}