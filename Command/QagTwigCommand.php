<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Command;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use function Symfony\Component\String\u;

#[AsCommand(
    name: 'qag:twig',
    description: 'A helper to create twig at the right location of your project to make overriding easier',
)]
class QagTwigCommand extends Command
{

    private const ANSWER_BASE = 'Base (to extend the base layout, such as header, css, js)';
    private const ANSWER_CRUD = 'Crud (list or view for a specific entity)';
    private const ANSWER_FORM = 'Form theme';
    private const ANSWER_FIELD = 'Field (html displayed in the list\'s table or a view e.g. "firstname")';
    private const ANSWER_DASHBOARD = 'Dashboard';
    private const ANSWER_CANCEL = 'Cancel';

    public function __construct(private string $projectDir, private iterable $cruds)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = $this->projectDir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'bundles' . DIRECTORY_SEPARATOR . 'ArkounayQuickAdminGeneratorBundle' . DIRECTORY_SEPARATOR;

        $answer = $io->askQuestion(new ChoiceQuestion('Please select the twig type to create', [self::ANSWER_BASE, self::ANSWER_CRUD, self::ANSWER_FORM, self::ANSWER_FIELD, self::ANSWER_DASHBOARD,  self::ANSWER_CANCEL], self::ANSWER_CANCEL));

        switch ($answer) {
            case self::ANSWER_BASE:
                $content = <<<TWIG
                    {% extends '@!ArkounayQuickAdminGenerator/base.html.twig' %}

                    {% block css %}
                        {{ parent() }}
                        {# {{ encore_entry_link_tags('app') }} #}
                    {% endblock %}
                    
                    {% block head_js %}
                        {{ parent() }}
                        {# {{ encore_entry_script_tags('app') }} #}
                    {% endblock %}
                    TWIG;

                $this->createFile($io, $path . 'base.html.twig', $content);
                break;

            case self::ANSWER_CRUD:
                $cruds = [];
                foreach ($this->cruds as $crud) {
                    /** @var Crud $crud */
                    $class = $crud::class;
                    $cruds["{$crud->getName()} ($class)"] = $crud;
                }
                if (empty($cruds)) {
                    $io->writeln("You need at least 1 Crud Controller to be able to override its twig.");
                    return Command::INVALID;
                }
                $answer = $io->askQuestion( new ChoiceQuestion(
                    'Please select the controller',
                    array_keys($cruds),
                    null
                ));

                $selectedCrud = $cruds[$answer];
                $path .= 'crud' . DIRECTORY_SEPARATOR . 'entities' . DIRECTORY_SEPARATOR . $selectedCrud->getRoute() . DIRECTORY_SEPARATOR;

                $answer = $io->askQuestion(new ChoiceQuestion('Please select the twig type', ['list', 'form', 'view'], null));
                $path .= "$answer.html.twig";

                if ($answer === 'form') {
                    $content = <<<TWIG
                    {% extends '@!ArkounayQuickAdminGenerator/crud/form.html.twig' %}
                    
                    {% block form_content %}
                        {{ form_rest(form) }}
                    {% endblock %}
                    TWIG;
                } else {
                    $content = "{% extends '@ArkounayQuickAdminGenerator/crud/$answer.html.twig' %}";
                }

                $this->createFile($io, $path, $content);

                break;

            case self::ANSWER_FORM:
                $path .= 'form' . DIRECTORY_SEPARATOR . 'form_theme.html.twig';
                $this->createFile($io, $path, "{% extends '@!ArkounayQuickAdminGenerator/form/form_theme.html.twig' %}");
                break;

            case self::ANSWER_DASHBOARD:
                $path .= 'dashboard.html.twig';
                $content = <<<TWIG
                    {% extends '@!ArkounayQuickAdminGenerator/dashboard.html.twig' %}
                    
                    {% block content %}
                        <div class="card">
                            <div class="card-body">
                                Hi {{ app.user }}
                            </div>
                        </div>
                    {% endblock %}
                    TWIG;

                $this->createFile($io, $path, $content);
                break;

            case self::ANSWER_FIELD:
                $name = $io->ask('Please write the field type (e.g. "fullname")');
                if (!$name) {
                    $io->writeln("Invalid name. Operation canceled");
                    return Command::INVALID;
                }
                $name = u($name)->snake();
                $name = str_replace(['.html', '.twig'], '', $name); // remove .html.twig if contained in string since we'll append it
                $path .= 'crud' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . '_' . $name . '.html.twig';
                if ($this->createFile($io, $path, '{{ value }}')) {
                    $io->info("Remember to specify the twig name \"$name\" in the field's entity attributes, e.g.\n\n#[ORM\Column]\n#[QAG\Field(twigName: '$name')] // <-- add this\nprivate ?string \$$name = null;");
                }
                break;

            default:
                $io->writeln("Operation canceled");
        }

        return Command::SUCCESS;
    }

    private function createFile(SymfonyStyle $io, string $path, string $content = ''): bool
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($path)) {
            $io->writeln("The file <info>$path</info> already exists. Operation canceled.");
            return false;
        }

        if (!$io->confirm("A file will be generated at $path. Continue?")) {
            $io->writeln("File creation aborted.");
            return false;
        }

        $filesystem->dumpFile($path, $content);
        $io->success("File created at $path");

        return true;
    }
}
