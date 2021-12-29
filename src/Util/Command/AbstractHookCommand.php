<?php

declare(strict_types=1);

namespace App\Util\Command;

use App\Util\CodeQuality\CheckInterface;
use App\Util\CodeQuality\ModifiedPaths;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractHookCommand extends Command
{
    abstract public static function getChecks(): array;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConsoleOutputInterface $output */
        $paths = ModifiedPaths::createFromVCSStatus();
        $output->writeln(sprintf('<info>%s</info> modified files to check...', \count($paths)));
        $output->writeln('');
        $overallSuccess = true;
        $totalTime = 0;
        $messages = [];

        // @TODO: Switch to check registry
        foreach (static::getChecks() as $specificCheckName => $checkSpec) {
            $allowFailure = false;

            // @TODO: Implement a class for checkSpec/name logic
            if (\is_array($checkSpec)) {
                [$checkClass, $checkParameters] = $checkSpec;

                if (3 === \count($checkSpec)) {
                    $allowFailure = $checkSpec[2];
                }

                $instance = new $checkClass($checkParameters);
            } else {
                $instance = new $checkSpec();
            }

            if (!$instance instanceof CheckInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Expected a CheckInterface implementing class, got %s instead',
                    \get_class($instance)
                ));
            }

            $section = $output->section();
            $startTime = microtime(true);

            $checkName = \is_string($specificCheckName) ? $specificCheckName : $instance->getName();
            $section->writeln('<fg=white>[ ]</> '.$checkName);

            $result = $instance->execute($paths);
            $endTime = microtime(true);
            $timeElapsed = $endTime - $startTime;
            $totalTime += $timeElapsed;

            if (!$allowFailure) {
                $overallSuccess = $overallSuccess && $result->isSuccessful();
            }

            if ($result->isSuccessful()) {
                $section->overwrite('<fg=green>[+]</> '.$checkName.' <fg=cyan>'.round($timeElapsed, 2).'s</>');
                continue;
            }

            $messages[$checkName] = $result->messages();

            if ($allowFailure) {
                $section->overwrite('<bg=yellow>[!]</> '.$checkName.' <fg=cyan>'.round($timeElapsed, 2).'s</>');
                continue;
            }

            $section->overwrite('<bg=red>[!]</> '.$checkName.' <fg=cyan>'.round($timeElapsed, 2).'s</>');
        }

        $output->writeln(['', 'Total time: <options=bold>'.round($totalTime, 2).'s</>']);

        foreach ($messages as $checkName => $checkMessages) {
            $output->writeln(['', "<info>{$checkName}:</info>"]);

            foreach ($checkMessages as $file => $errors) {
                if (\is_string($file)) {
                    $output->writeln($file);
                }

                $output->writeln($errors);
            }
        }

        if (true !== $overallSuccess) {
            $output->writeln(['', '<error>Cannot proceed with commit.</error>']);

            return 1;
        }

        return 0;
    }
}
