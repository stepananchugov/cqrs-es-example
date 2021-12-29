<?php

declare(strict_types=1);

namespace App\Ui\GraphQL;

use App\Ui\WebSocket\EventDispatcher\Event\TickEvent;
use GraphQL\Executor\ExecutionResult;
use Overblog\GraphQLBundle\Request\Executor;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CachedExecutor
{
    private Executor $executor;

    private LoggerInterface $logger;

    private int $cacheHits = 0;

    private int $cacheMisses = 0;

    private array $executionCache = [];

    public function __construct(Executor $executor, LoggerInterface $logger = null)
    {
        if (null === $logger) {
            $logger = new NullLogger();
        }

        $this->executor = $executor;
        $this->logger = $logger;
    }

    public function clearExecutionCache(): void
    {
        $this->logger->debug('Clearing execution cache');
        $this->executionCache = [];
    }

    public function execute(array $payload): ExecutionResult
    {
        $payloadHash = md5(serialize($payload));

        if (!\array_key_exists($payloadHash, $this->executionCache)) {
            ++$this->cacheMisses;
            $this->executionCache[$payloadHash] = $this->executor->execute(null, $payload);

            return $this->executionCache[$payloadHash];
        }

        ++$this->cacheHits;

        return $this->executionCache[$payloadHash];
    }

    public function onTick(TickEvent $event): void
    {
        $this->logger->debug('Executor cache hits: {hits}, misses: {misses}', [
            'hits' => $this->cacheHits,
            'misses' => $this->cacheMisses,
        ]);
    }

    public function cacheLength(): int
    {
        return \count($this->executionCache);
    }
}
