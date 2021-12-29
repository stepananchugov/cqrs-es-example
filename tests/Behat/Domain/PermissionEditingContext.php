<?php

declare(strict_types=1);

namespace App\Tests\Behat\Domain;

use App\Application\Command\AdminUser\CreateAdminUserCommand;
use App\Application\Command\Permissions\GrantPermissionCommand;
use App\Application\Command\Permissions\GrantRoleCommand;
use App\Application\Command\Permissions\RevokePermissionCommand;
use App\Application\Command\Permissions\RevokeRoleCommand;
use App\Application\Query\AdminUser\AdminUserQuery;
use App\Application\Query\Permissions\ListPermissionsQuery;
use App\Application\Query\Permissions\ListRolePermissionsQuery;
use App\Application\Query\Permissions\ListRolesQuery;
use App\Domain\AdminUser\AdminUserId;
use App\Domain\Permissions\RealmId;
use App\Tests\Behat\DatabasePurgerTrait;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\MessageBusInterface;

class PermissionEditingContext extends AbstractDomainContext
{
    use DatabasePurgerTrait;

    // If these two differ in prod/staging, you can run these tests over those stages without any damage
    // Just need to clean up afterwards
    private const REALM_ID = 'abe4a934-2a71-4f6e-a006-5684a1def52b';

    private Connection $connection;

    public function __construct(Connection $connection, MessageBusInterface $messageBus)
    {
        parent::__construct($messageBus);

        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @Given there is a permission named :permissionName
     * @TODO Move to permission context?
     */
    public function thereIsAPermissionNamed(string $permissionName): void
    {
        $permissions = $this->handleMessage(new ListPermissionsQuery());

        foreach ($permissions as $permission) {
            if ($permission['name'] === $permissionName) {
                return;
            }
        }

        throw new \Exception(sprintf('Permission %s does not exist', $permissionName));
    }

    /**
     * @Given there is a role named :roleName identified by :id
     * @TODO Move to permission context?
     */
    public function thereIsARoleNamedIdentifiedBy(string $roleName, string $id): void
    {
        $roles = $this->handleMessage(new ListRolesQuery());

        foreach ($roles as $role) {
            if ($role['name'] === $roleName && $role['id'] === $id) {
                return;
            }
        }

        throw new \Exception(sprintf('Role %s does not exist', $roleName));
    }

    /**
     * @Given there are no permissions granted to :roleId
     */
    public function thereAreNoPermissionsAssignedTo(string $roleId): void
    {
        $grants = $this->handleMessage(new ListRolePermissionsQuery($roleId));

        if (\is_array($grants) && \count($grants) > 0) {
            throw new \Exception('Excepted no permissions');
        }
    }

    /**
     * @Given admin user named :username has no roles assigned
     */
    public function adminUserNamedHasNoRolesAssigned(string $username): void
    {
        $userQuery = (new UserQuery())
            ->withUsername($username)
        ;
        $userId = $this->handleMessage($userQuery)['id'];

        // Withers in queries: easier to enrich
        $rolesQuery = (new ListRolesQuery())
            ->withUserId(UserId::fromString($userId))
        ;

        $roles = $this->handleMessage($rolesQuery);

        if (\is_array($roles) && \count($roles) > 0) {
            throw new \Exception('Excepted no roles');
        }
    }

    /**
     * @When I grant :permissionId to :roleId
     * @Given permission :permission is granted to :role
     */
    public function iGrantTo(string $permissionId, string $roleId): void
    {
        $this->handleMessage(new GrantPermissionCommand($permissionId, $roleId));
    }

    /**
     * @Then :permissionId should be granted to :roleId
     */
    public function shouldBeGrantedTo(string $permissionId, string $roleId): void
    {
        $grants = $this->handleMessage(new ListRolePermissionsQuery($roleId));

        if (!\is_array($grants) || 0 === \count($grants)) {
            throw new \Exception('No grants found');
        }

        foreach ($grants as $grant) {
            if ($grant['role'] === $roleId && $grant['name'] === $permissionId) {
                return;
            }
        }

        throw new \Exception('Permission is not granted');
    }

    /**
     * @When I revoke :permissionId from :roleId
     */
    public function iRevokeFrom(string $permissionId, string $roleId): void
    {
        $this->handleMessage(new RevokePermissionCommand($permissionId, $roleId));
    }

    /**
     * @Then :permissionId should not be granted to :roleId
     */
    public function shouldNotBeGrantedTo(string $permissionId, string $roleId): void
    {
        $grants = $this->handleMessage(new ListRolePermissionsQuery($roleId));

        if (!\is_array($grants) || 0 === \count($grants)) {
            return;
        }

        foreach ($grants as $grant) {
            if ($grant['role'] === $roleId && $grant['name'] === $permissionId) {
                throw new \Exception('Permission should not be granted, but it is');
            }
        }
    }

    /**
     * @Given there is an admin user named :username identified by :userId
     */
    public function thereIsAnAdminUserNamedIdentifiedBy(string $username, string $userId): void
    {
        $command = (new CreateAdminUserCommand($username))
            ->withId(AdminUserId::fromString($userId))
        ;

        $this->handleMessage($command);
    }

    /**
     * @When I assign :roleName to user :username
     * @Given role :roleName is granted to :username
     */
    public function iAssignToUser(string $roleName, string $username): void
    {
        $userId = $this->userIdByName($username);

        $this->handleMessage(new GrantRoleCommand(
            AdminUserId::fromString($userId),
            $roleName,
            RealmId::fromString(self::REALM_ID)
        ));
    }

    /**
     * @Then I see role ID :roleId in :username's user roles
     */
    public function iSeeInSUserRoles(string $roleId, string $username): void
    {
        $userId = $this->userIdByName($username);

        $rolesQuery = (new ListRolesQuery())
            ->withUserId(AdminUserId::fromString($userId))
        ;

        $roles = $this->handleMessage($rolesQuery);

        if (!\is_array($roles)) {
            throw new \Exception('No roles returned');
        }

        foreach ($roles as $role) {
            if ($role['id'] === $roleId) {
                return;
            }
        }

        throw new \Exception('Role not found');
    }

    /**
     * @When I revoke :roleId from user :username
     */
    public function iRevokeFromUser(string $roleId, string $username): void
    {
        $userId = $this->userIdByName($username);

        $this->handleMessage(new RevokeRoleCommand(
            AdminUserId::fromString($userId),
            $roleId,
            RealmId::fromString(self::REALM_ID)
        ));
    }

    /**
     * @When I revoke :permissionId from :roleId role
     */
    public function iRevokeFromRole(string $permissionId, string $roleId): void
    {
        $this->handleMessage(new RevokePermissionCommand(
            $permissionId,
            $roleId
        ));
    }

    /**
     * @Then I don't see :roleId in :username's user roles
     */
    public function iDontSeeInSUserRoles(string $roleId, string $username): void
    {
        $userId = $this->userIdByName($username);

        $rolesQuery = (new ListRolesQuery())
            ->withUserId(AdminUserId::fromString($userId))
        ;
        $roles = $this->handleMessage($rolesQuery);

        foreach ($roles as $role) {
            if ($role['id'] === $roleId) {
                throw new \Exception('Role found while it was not expected');
            }
        }
    }

    private function userIdByName(string $username): string
    {
        $userQuery = (new AdminUserQuery())->withUsername($username);

        return $this->handleMessage($userQuery)['id'];
    }
}
