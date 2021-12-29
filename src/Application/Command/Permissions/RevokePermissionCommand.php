<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\Permissions\PolicyId;
use App\Infrastructure\Permissions\Validator as Permissions;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Symfony\Component\Validator\Constraints as Assert;

class RevokePermissionCommand implements SerializablePayload
{
    /**
     * @Assert\NotBlank()
     * @Permissions\PermissionId()
     */
    private string $permissionId;

    /**
     * @Assert\NotBlank()
     * @Permissions\RoleId()
     */
    private string $roleId;

    private PolicyId $policyId;

    public function __construct(string $permissionId, string $roleId, ?PolicyId $policyId = null)
    {
        if (null === $policyId) {
            $policyId = PolicyId::defaultPolicyId();
        }

        $this->policyId = $policyId;
        $this->permissionId = $permissionId;
        $this->roleId = $roleId;
    }

    public function permissionId(): string
    {
        return $this->permissionId;
    }

    public function roleId(): string
    {
        return $this->roleId;
    }

    public function policyId(): PolicyId
    {
        return $this->policyId;
    }

    public function toPayload(): array
    {
        return [
            'permissionId' => $this->permissionId,
            'roleId' => $this->roleId,
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            $payload['permissionId'],
            $payload['roleId'],
        );
    }
}
