<?php

declare(strict_types=1);

namespace App\Domain\Permissions;

use App\Domain\User\UserId;
use App\Domain\Permissions\Event\Realm\RoleAssigned;
use App\Domain\Permissions\Event\Realm\RoleRevoked;
use App\Domain\Shared\Exception\AggregateConsistencyException;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class Realm implements AggregateRoot
{
    use AggregateRootBehaviour;

    /**
     * $assignments[userId] = [roles].
     */
    private array $assignments = [];

    public static function create(RealmId $realmId): self
    {
        return new self($realmId);
    }

    // Key point: all links are always by-id, especially in crossdomain cases
    public function assignRole(UserId $userId, string $roleId): void
    {
        if ($this->userHasRole($userId, $roleId)) {
            // They went with this :shrug:
            // throw new AggregateConsistencyException(sprintf(
            //     'Admin user with id \'%s\' already has role \'%s\'',
            //     $userId->toString(),
            //     $roleId,
            // ));

            // ?? Should have been just a `return;`? Why is this so? Shouldn't we protect the invariant?
            // ?? How is this different to fund withdrawal, for example?
            //
            // - This operation is idempotent by itself
            // - There is no such thing as a business rule that disallows assigning a role twice.
            // - If you were assistant regional manager, you'll stay it unless it's revoked
            // - In case of fund withdrawals you are actually allowed to overdraw. Banks love this.
            return;
        }

        $this->recordThat(new RoleAssigned($userId, $roleId));
    }

    // Although this is such a simple domain, we could
    public function applyRoleAssigned(RoleAssigned $event): void
    {
        $role = $event->roleId();
        $userId = $event->userId()->toString();

        if (!\array_key_exists($userId, $this->assignments)) {
            $this->assignments[$userId] = [];
        }

        if (!\in_array($role, $this->assignments[$userId], true)) {
            $this->assignments[$userId][] = $role;
        }
    }

    public function revokeRole(UserId $userId, string $roleId): void
    {
        if (!$this->userHasRole($userId, $roleId)) {
            // throw new AggregateConsistencyException(sprintf(
            //     'Admin user with id \'%s\' hasn\'t role \'%s\' yet',
            //     $userId->toString(),
            //     $roleId,
            // ));
            return;
        }
        $this->recordThat(new RoleRevoked($userId, $roleId));
    }

    public function applyRoleRevoked(RoleRevoked $event): void
    {
        $revokedRoleId = $event->roleId();
        $userId = $event->userId()->toString();

        if (\array_key_exists($userId, $this->assignments)) {
            $this->assignments[$userId] = array_filter(
                $this->assignments[$userId],
                static function ($roleId) use ($revokedRoleId): bool {
                    return $roleId !== $revokedRoleId;
                }
            );
        }
    }

    // Public just for tests?
    // `public readonly` FTW: https://wiki.php.net/rfc/readonly_properties
    public function userHasRole(UserId $userId, string $roleId): bool
    {
        $key = $userId->toString();

        return \array_key_exists($key, $this->assignments)
            && \in_array($roleId, $this->assignments[$key], true);
    }
}
