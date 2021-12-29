<?php

declare(strict_types=1);

namespace App\Ui\GraphQL;

use App\Application\Query\Share\Pagination;
use App\Application\Query\Share\PaginationAware;
use App\Infrastructure\Share\MessageBus\HandledResult;
use App\Infrastructure\Share\MessageBus\PaginatedStamp;
use App\Ui\GraphQL\Adapter\AdapterRegistry;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryResolver implements ResolverInterface
{
    private AdapterRegistry $adapterRegistry;

    private MessageBusInterface $queryBus;

    public function __construct(MessageBusInterface $queryBus, AdapterRegistry $adapterRegistry)
    {
        $this->queryBus = $queryBus;
        $this->adapterRegistry = $adapterRegistry;
    }

    /**
     * @param mixed $inputs
     *
     * @return mixed
     */
    public function __invoke($inputs)
    {
        if (!\array_key_exists('filter', $inputs) || !\array_key_exists(0, $inputs['filter'])) {
            throw new \InvalidArgumentException('Filter is not specified in given input. Check your GraphQL YAML config for the correct msarguments call');
        }

        if (0 !== strpos(\get_class($inputs['filter'][0]), 'App\\Application\\Query')) {
            $query = $this->adapterRegistry->transform($inputs['filter'][0]);
        } else {
            $query = $inputs['filter'][0];
        }

        if (\array_key_exists('pagination', $inputs) && $query instanceof PaginationAware) {
            $pagination = $this->adapterRegistry->transform($inputs['pagination'][0]);

            if (!$pagination instanceof Pagination) {
                throw new \InvalidArgumentException(sprintf('Expected pagination input to transform into %s, got %s instead', Pagination::class, \get_class($pagination)));
            }

            $query = $query->withPagination($pagination);
        }

        $envelope = $this->queryBus->dispatch($query);

        $paginatedStamp = $envelope->last(PaginatedStamp::class);

        if ($paginatedStamp instanceof PaginatedStamp) {
            return $paginatedStamp->result();
        }

        return (new HandledResult($envelope))->getResult();
    }
}
