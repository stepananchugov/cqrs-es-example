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

final class InputObjectMaker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:ui:input-object';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a new GraphQL input object')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the input object to generate')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $argument = $input->getArgument('name');

        if (!\is_string($argument)) {
            throw new \InvalidArgumentException('Name is not a string');
        }

        $inputObjectName = trim($argument);

        $classNameDetails = $generator->createClassNameDetails(
            $inputObjectName,
            'Ui\\GraphQL\\Mapping\\InputObject',
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__.'/templates/InputObject.tpl.php',
            [
                'class_name' => $inputObjectName,
            ]
        );

        $generator->writeChanges();

        $classNameDetails = $generator->createClassNameDetails(
            $inputObjectName.'Adapter',
            'Ui\\GraphQL\\Adapter',
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__.'/templates/InputAdapter.tpl.php',
            [
                'class_name' => $inputObjectName,
                'input_object_name' => $inputObjectName,
            ]
        );

        $generator->writeChanges();
    }
}
