<?php

declare(strict_types=1);

namespace App\Domain\Permissions\Event\Policy;

use App\Domain\Permissions\Permission;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class PermissionGranted implements SerializablePayload
{
    private Permission $permission;

    private string $roleId;

    public function __construct(Permission $permission, string $roleId)
    {
        $this->permission = $permission;
        $this->roleId = $roleId;
    }

    public function permission(): Permission
    {
        return $this->permission;
    }

    public function roleId(): string
    {
        return $this->roleId;
    }

    public function toPayload(): array
    {
        return [
            'roleId' => $this->roleId,
            'permission' => $this->permission->toPayload(),
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            Permission::fromPayload($payload['permission']),
            $payload['roleId'],
        );
    }
}
