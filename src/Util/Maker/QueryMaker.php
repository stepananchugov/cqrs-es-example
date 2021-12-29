<?php

declare(strict_types=1);

namespace App\Util\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class QueryMaker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:app:query';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new domain query and a handler for it')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Name of the domain to generate query in (Domain or Domain/Aggregate)')
            ->addArgument('namePrefix', InputArgument::OPTIONAL, 'Name prefix of the query to generate (e.g. ListItems)')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $namePrefix = $input->getArgument('namePrefix');

        if (!\is_string($namePrefix)) {
            throw new \InvalidArgumentException('Name prefix is not a string');
        }

        $namePrefix = rtrim(trim($namePrefix), 'Query');
        $queryName = $namePrefix.'Query';
        $handlerName = $namePrefix.'Handler';

        $domain = $input->getArgument('domain');

        if (!\is_string($domain)) {
            throw new \InvalidArgumentException('Domain is not a string');
        }

        $domain = trim($domain);

        $classNameDetails = $generator->createClassNameDetails(
            $queryName,
            'Application\\Query\\'.$domain,
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__.'/templates/Query.tpl.php',
            [
                'class_name' => $queryName,
            ]
        );

        $classNameDetails = $generator->createClassNameDetails(
            $handlerName,
            'Application\\Query\\'.$domain,
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__.'/templates/QueryHandler.tpl.php',
            [
                'class_name' => $handlerName,
                'query_class_name' => $queryName,
                'domain' => $domain,
            ]
        );

        $generator->writeChanges();
    }
}
