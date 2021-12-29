<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter;

class AdapterRegistry
{
    /**
     * @var InputAdapterInterface[]
     */
    private array $adapters;

    public function __construct(iterable $adapters)
    {
        foreach ($adapters as $adapter) {
            $this->registerAdapter($adapter);
        }
    }

    public function registerAdapter(InputAdapterInterface $inputAdapter): void
    {
        $this->adapters[$inputAdapter->getInputClass()] = $inputAdapter;
    }

    public function transform(object $input): object
    {
        $inputClass = \get_class($input);

        if (!\array_key_exists($inputClass, $this->adapters)) {
            throw new \InvalidArgumentException(sprintf(
                'No adapter registered for input class %s',
                $inputClass
            ));
        }

        return $this->adapters[$inputClass]->transform($input);
    }
}
