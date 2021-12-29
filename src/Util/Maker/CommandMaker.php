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

final class CommandMaker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:app:command';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new domain command and a handler for it')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Name of the domain to generate command in (Domain or Domain/Aggregate)')
            ->addArgument('namePrefix', InputArgument::OPTIONAL, 'Name prefix of the command to generate (e.g. CreateItem)')
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

        $commandName = trim($namePrefix).'Command';
        $handlerName = trim($namePrefix).'Handler';

        $domain = $input->getArgument('domain');

        if (!\is_string($domain)) {
            throw new \InvalidArgumentException('Domain is not a string');
        }

        $domain = trim($domain);

        $classNameDetails = $generator->createClassNameDetails(
            $commandName,
            'Application\\Command\\'.$domain,
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__.'/templates/Command.tpl.php',
            [
                'class_name' => $commandName,
            ]
        );

        $classNameDetails = $generator->createClassNameDetails(
            $handlerName,
            'Application\\Command\\'.$domain,
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__.'/templates/CommandHandler.tpl.php',
            [
                'class_name' => $handlerName,
                'command_name' => $commandName,
                'domain' => $domain,
            ]
        );

        $generator->writeChanges();
    }
}
