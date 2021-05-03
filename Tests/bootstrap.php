<?php

use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\TestKernel;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies using Composer to run the test suite.');
}
$autoload = require $file;

$application = new Application(new TestKernel('test', true));
$application->setAutoExit(false);

$nullOutput = new NullOutput();
$input = new ArrayInput(['command' => 'doctrine:database:drop', '--no-interaction' => true, '--force' => true]);
$application->run($input, $nullOutput);

$input = new ArrayInput(['command' => 'doctrine:database:create', '--no-interaction' => true]);
$application->run($input, $nullOutput);

$input = new ArrayInput(['command' => 'doctrine:schema:update', '--force' => true]);
$application->run($input, $nullOutput);

$em = $application->getKernel()->getContainer()->get('doctrine.orm.default_entity_manager');
for ($i = 0; $i < 24; $i++) {
    $article = new \Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Article();
    $article->setName('Lorem ' . $i);
    $article->setPublished($i % 2 === 0);
    $em->persist($article);
}
$em->flush();

unset($input, $nullOutput, $application, $em);