<?php

declare(strict_types=1);

namespace App\Infrastructure\Permissions;

use App\Domain\Permissions\Permission;
use App\Domain\Permissions\Role;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 *     App\Infrastructure\Permissions\Configuration:
 *         arguments:
 *             - !tagged_locator permissions.consumer
 *             - roles:
 *                 ROLE_ADMIN: "Administrator"
 *                 ROLE_TRANSLATOR:  "Translator"
 *                 ...
 */
final class Configuration
{
    /**
     * @var Role[]
     */
    private array $roles;

    /**
     * @var Permission[]
     */
    private array $permissions;

    public function __construct(ServiceLocator $locator, array $configuration)
    {
        if (!\array_key_exists('roles', $configuration) || !\is_array($configuration['roles'])) {
            throw InvalidArgumentException::invalidConfiguration('Roles are misconfigured');
        }

        foreach ($configuration['roles'] as $roleId => $roleName) {
            $this->addRole(new Role(
                $roleId,
                $roleName,
            ));
        }

        foreach ($locator->getProvidedServices() as $service) {
            $this->addPermission(Permission::fromClassname($service));
        }
    }

    public function roles(): array
    {
        return $this->roles;
    }

    #[Pure]
    public function roleNames(): array
    {
        $result = [];

        foreach ($this->roles as $role) {
            $result[$role->id()] = $role->name();
        }

        return $result;
    }

    public function permissions(): array
    {
        return $this->permissions;
    }

    #[Pure]
    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission->name() === $permissionName) {
                return true;
            }
        }

        return false;
    }

    #[Pure]
    public function hasRole(string $roleId): bool
    {
        foreach ($this->roles as $role) {
            if ($role->id() === $roleId) {
                return true;
            }
        }

        return false;
    }

    private function addPermission(Permission $permission): void
    {
        $this->permissions[$permission->name()] = $permission;
    }

    private function addRole(Role $role): void
    {
        $this->roles[$role->id()] = $role;
    }
}
