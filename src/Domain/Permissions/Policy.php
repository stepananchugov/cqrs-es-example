<?php

declare(strict_types=1);

namespace App\Domain\Permissions;

use App\Domain\Permissions\Event\Policy\PermissionGranted;
use App\Domain\Permissions\Event\Policy\PermissionRevoked;
use App\Domain\Shared\Exception\AggregateConsistencyException;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class Policy implements AggregateRoot
{
    use AggregateRootBehaviour;

    /**
     * $grants[roleId] = [permissions].
     */
    private array $grants = [];

    public static function create(PolicyId $policyId): self
    {
        return new self($policyId);
    }

    //TODO: Should this return anything? Alan says it should
    public function grant(Permission $permission, string $roleId): void
    {
        // Idempotency
        if (!$this->isGranted($permission, $roleId)) {
            $this->recordThat(new PermissionGranted($permission, $roleId));
        }
    }

    public function applyPermissionGranted(PermissionGranted $event): void
    {
        $roleId = $event->roleId();
        $permission = $event->permission()->name();

        if (!\array_key_exists($roleId, $this->grants)) {
            $this->grants[$roleId] = [];
        }

        if (!\in_array($permission, $this->grants[$roleId], true)) {
            $this->grants[$roleId][] = $permission;
        }
    }

    public function revoke(Permission $permission, string $roleId): void
    {
        if (!$this->isGranted($permission, $roleId)) {
            return;
            // NO, PLEASE GOD NO:
            // throw new AggregateConsistencyException(sprintf('Cannot revoke permission \'%s\' from role \'%s\' since it has not been yet granted', $permission->name(), $roleId));
            //
            // Misconception: Aggregate can throw when it doesn't like what's done to it
            // Conception: Aggregate protects business rules
            // Here, a policy might throw for cases where a business rule is broken (e.g. cannot be both a marketing and a circuit manager, 2 pairs of eyes principle)
            // This is not the case though
        }

        $this->recordThat(new PermissionRevoked($permission, $roleId));
    }

    public function applyPermissionRevoked(PermissionRevoked $event): void
    {
        $roleId = $event->roleId();
        $revokedPermission = $event->permission()->name();

        if (\array_key_exists($roleId, $this->grants)) {
            $this->grants[$roleId] = array_filter(
                $this->grants[$roleId],
                static function ($enabledPermission) use ($revokedPermission): bool {
                    return $enabledPermission !== $revokedPermission;
                }
            );
        }
    }

    public function isGranted(Permission $permission, string $roleId): bool
    {
        $permissionName = $permission->name();

        return \array_key_exists($roleId, $this->grants)
            && \in_array($permissionName, $this->grants[$roleId], true);
    }
}
