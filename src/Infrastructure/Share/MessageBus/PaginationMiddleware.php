<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\MessageBus;

use App\Application\Query\Share\Pagination;
use App\Application\Query\Share\PaginationAware;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class PaginationMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $envelope = $stack->next()->handle($envelope, $stack);
        $message = $envelope->getMessage();

        if (!$message instanceof PaginationAware) {
            return $envelope;
        }

        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp instanceof HandledStamp) {
            throw new \RuntimeException('Pagination middleware received a message that was not handled yet. Check your middleware order so that pagination goes after handling.');
        }

        $currentOffset = 0;
        $nextOffset = 0;

        if ($message->pagination() instanceof Pagination) {
            $currentOffset = $message->pagination()->offset();
            $nextOffset = $message->pagination()->offset() + $message->pagination()->limit();
        }

        $result = [
            'edges' => array_map(static function ($row) use (&$currentOffset): array {
                return [
                    'node' => $row,
                    'cursor' => base64_encode((string) $currentOffset++),
                ];
            }, $handledStamp->getResult()),
            'pageInfo' => [
                'endCursor' => base64_encode((string) $nextOffset),
            ],
        ];

        $newStamp = new PaginatedStamp($result);

        return $envelope->with($newStamp);
    }
}
