<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\Permissions\PolicyId;
use App\Infrastructure\Permissions\Validator as Permissions;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Symfony\Component\Validator\Constraints as Assert;

class GrantPermissionCommand implements SerializablePayload
{
    /**
     * It's the responsibility of the message bus to check commands for sanity
     * @see \App\Infrastructure\Share\MessageBus\ValidationMiddleware
     *
     * @Assert\NotBlank()
     * @Permissions\ObjectId()
     */
    private string $permissionId;

    /**
     * @Assert\NotBlank()
     * @Permissions\ObjectId()
     */
    private string $roleId;

    private PolicyId $policyId;

    // All required stuff goes into the constructor
    public function __construct(string $permissionId, string $roleId)
    {
        // Note the default policy
        // This is like a "singleton aggregate", in a way
        // So I guess it might break some rules
        // But this has some wins as well. More on that later
        $this->policyId = PolicyId::defaultPolicyId();

        $this->permissionId = $permissionId;
        $this->roleId = $roleId;
    }

    // Optional stuff goes through withers
    // A good point: sometimes it's worth extracting another command
    // This was not the case, because Policy is a singleton-aggregate, and this method would have only been used in
    // tests or whitelabel cohabitant app
    public function withPolicyId(PolicyId $policyId): self
    {
        $clone = clone $this;
        $clone->policyId = $policyId;

        return $clone;
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

    // Found no better way to make sure everything can be serialized
    public function toPayload(): array
    {
        return [
            'permissionId' => $this->permissionId,
            'roleId' => $this->roleId,
            'policyId' => $this->policyId->toString(),
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        $instance = new self(
            $payload['permissionId'],
            $payload['roleId'],
        );

        if (\array_key_exists('policyId', $payload)) {
            $instance = $instance->withPolicyId(PolicyId::fromString($payload['policyId']));
        }

        return $instance;
    }
}
