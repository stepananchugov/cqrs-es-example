<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

use App\Util\RecursiveArrayDiff;
use Go\ParserReflection;

class ApiBCBreakCheck implements CheckInterface
{
    public function getName(): string
    {
        return 'API BC breaks';
    }

    public function execute(array $paths): CheckResult
    {
        $currentState = [];

        foreach ($paths as $path) {
            $currentState += $this->checkPath($path);
        }

        $breaks = [];

        if (file_exists('.api-signatures.json')) {
            $contents = file_get_contents('.api-signatures.json');

            if (false !== $contents) {
                $previousState = json_decode($contents, true);
                $diff = RecursiveArrayDiff::create($currentState, $previousState);

                foreach ($diff as $brokenMethod => $spec) {
                    $breaks[] = 'Method '.$brokenMethod.' has changed its signature';
                }
            }
        } else {
            file_put_contents('.api-signatures.json', json_encode($currentState, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
        }

        return new CheckResult(0 === \count($breaks), $breaks);
    }

    private function checkPath(string $path): array
    {
        $apiDeclaration = [];

        if (0 === strpos($path, 'tests')) {
            return $apiDeclaration;
        }

        $parsedFile = new ParserReflection\ReflectionFile($path);

        $fileNameSpaces = $parsedFile->getFileNamespaces();

        foreach ($fileNameSpaces as $namespace) {
            $classes = $namespace->getClasses();

            foreach ($classes as $reflectionClass) {
                foreach ($reflectionClass->getMethods() as $method) {
                    if (false !== $method->getDocComment() && false !== strpos($method->getDocComment(), '@api')) {
                        $parameters = [];

                        foreach ($method->getParameters() as $position => $parameter) {
                            $paramType = $parameter->getType();

                            if (null === $paramType) {
                                $parameters[$position.'_'] = [
                                    'type' => 'mixed',
                                ];

                                continue;
                            }

                            if (!$paramType->isBuiltin()) {
                                // $this->classesToCheck[] = (string) $paramType;
                            }

                            $parameters[$position.'_'] = [
                                'type' => (string) $paramType,
                                'nullable' => $paramType->allowsNull(),
                            ];
                        }

                        $apiDeclaration[$reflectionClass->getName().'#'.$method->getName()] = $parameters;
                    }
                }
            }
        }

        return $apiDeclaration;
    }
}
