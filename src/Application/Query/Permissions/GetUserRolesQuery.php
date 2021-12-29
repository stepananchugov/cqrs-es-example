<?php

declare(strict_types=1);

namespace App\Application\Query\Permissions;

use App\Application\AuthorisedMessageInterface;
use App\Domain\User\UserId;

final class GetUserRolesQuery implements AuthorisedMessageInterface
{
    private UserId $userId;

    public function __construct(UserId $userId)
    {
        $this->userId = $userId;
    }

    public function toPayload(): array
    {
        return ['userId' => $this->userId];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(UserId::fromString($payload['userId']));
    }

    public function userId(): UserId
    {
        return $this->userId;
    }
}
