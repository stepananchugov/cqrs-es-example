<?php

declare(strict_types=1);

namespace App\Tests\Behat\GraphQl;

use App\Tests\Behat\DatabasePurgerTrait;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class PermissionEditingContext extends AbstractGraphQlContext
{
    use DatabasePurgerTrait;

    private Connection $connection;

    public function __construct(Connection $connection, KernelBrowser $kernelBrowser)
    {
        parent::__construct($kernelBrowser);
        $this->connection = $connection;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @Given there is a permission named :name
     */
    public function thereIsAPermissionNamed(string $name): void
    {
        $response = $this->executeQuery('{ permissions { name } }');

        foreach ($response['data']['permissions'] as $permission) {
            if ($permission['name'] === $name) {
                return;
            }
        }

        throw new \Exception(sprintf(
            'Permission %s does not exist',
            $name
        ));
    }

    /**
     * @Given there is a role named :name identified by :id
     */
    public function thereIsARoleNamedIdentifiedBy(string $name, string $id): void
    {
        $response = $this->executeQuery('{ roles { id, name } }');

        foreach ($response['data']['roles'] as $role) {
            if ($role['id'] === $id && $role['name'] === $name) {
                return;
            }
        }

        throw new \Exception(sprintf(
            'Role %s identified by %s does not exist',
            $name,
            $id
        ));
    }

    /**
     * @Given there are no permissions granted to :roleId
     */
    public function thereAreNoPermissionsGrantedTo(string $roleId): void
    {
        $response = $this->executeQuery(sprintf(
            '{ permissions(filter: {roleId: "%s"}) { name } }',
            $roleId
        ));

        $count = \count($response['data']['permissions']);

        if (0 !== $count) {
            throw new \Exception(sprintf(
                'Expected 0 permissions, found %s',
                $count
            ));
        }
    }

    /**
     * @When I grant :permission to :roleId
     */
    public function iGrantPermissionTo(string $permission, string $roleId): void
    {
        $this->executeQuery(
            'mutation grantPermission($input: GrantPermissionInput!) {
                      grantPermission(input: $input) {
                          success
                      }
                  }',
            ['input' => ['permissionId' => $permission, 'roleId' => $roleId]]
        );
    }

    /**
     * @Then :permission should be granted to :roleId
     */
    public function shouldBeGrantedTo(string $permission, string $roleId): void
    {
        $response = $this->executeQuery(
            'query listGrants($filter: FindRolesInput) {
                  roles(filter: $filter) {
                    permissions {
                      name
                    }
                  }
                }',
            ['filterId' => ['roleId' => $roleId]]
        );

        foreach ($response['data']['roles'] as $role) {
            foreach ($role['permissions'] as $foundPermission) {
                if ($foundPermission['name'] === $permission) {
                    return;
                }
            }
        }

        throw new \Exception(sprintf(
            'Permission %s was not granted to %s',
            $permission,
            $roleId
        ));
    }

    /**
     * @Given permission :permission is granted to :roleId
     */
    public function permissionIsGrantedTo(string $permission, string $roleId): void
    {
        $this->executeQuery(
            'mutation grantPermission($input: GrantPermissionInput!) {
                      grantPermission(input: $input) {
                          success
                      }
                  }',
            ['input' => ['permissionId' => $permission, 'roleId' => $roleId]]
        );
    }

    /**
     * @When I revoke :permission from :roleId role
     */
    public function iRevokeFrom(string $permission, string $roleId): void
    {
        $this->executeQuery(
            'mutation revokePermission($input: RevokePermissionInput!) {
                      revokePermission(input: $input) {
                          success
                      }
                  }',
            ['input' => ['permissionId' => $permission, 'roleId' => $roleId]]
        );
    }

    /**
     * @Then :permission should not be granted to :roleId
     */
    public function permissionShouldNotBeGrantedTo(string $permission, string $roleId): void
    {
        $response = $this->executeQuery(
            'query listGrants($filter: FindRolesInput) {
                  roles(filter: $filter) {
                    permissions {
                      name
                    }
                  }
                }',
            ['filterId' => ['roleId' => $roleId]]
        );

        foreach ($response['data']['roles'] as $role) {
            foreach ($role['permissions'] as $foundPermission) {
                if (\array_key_exists('name', $foundPermission) && $foundPermission['name'] === $permission) {
                    throw new \Exception(sprintf(
                        'Permission %s was granted to %s while not expected to',
                        $permission,
                        $roleId
                    ));
                }
            }
        }
    }

    /**
     * @Given admin user named :username has no roles assigned
     */
    public function adminUserNamedHasNoRolesAssigned(string $username): void
    {
        $userId = $this->userIdByUsername($username);

        $response = $this->executeQuery(
            'query listRoles($filter: FindRolesInput) {
                  roles(filter: $filter) {
                    id
                  }
                }',
            ['filter' => ['userId' => $userId]]
        );

        if (\count($response['data']['roles']) > 0) {
            throw new \Exception('Expected to find no roles');
        }
    }

    /**
     * @Given role :roleId is granted to :username
     * @When I assign :roleId to user :username
     */
    public function iAssignToUser(string $roleId, string $username): void
    {
        $userId = $this->userIdByUsername($username);

        $this->executeQuery('mutation grantRole($input: GrantRoleInput!) {
  grantRole(input: $input) {
      success
  }
}', ['input' => ['roleId' => $roleId, 'userId' => $userId]]);
    }

    /**
     * @Then I see role ID :roleId in :username's user roles
     */
    public function iSeeRoleIdInSUserRoles(string $roleId, string $username): void
    {
        $userId = $this->userIdByUsername($username);

        $response = $this->executeQuery(
            'query listRoles($filter: FindRolesInput) {
              roles(filter: $filter) {
                id
              }
            }',
            ['filter' => ['userId' => $userId]]
        );

        foreach ($response['data']['roles'] as $role) {
            if ($role['id'] === $roleId) {
                return;
            }
        }

        throw new \Exception(sprintf('Role %s expected but not found', $roleId));
    }

    /**
     * @When I revoke :roleId from user :username
     */
    public function iRevokeFromUser(string $roleId, string $username): void
    {
        $userId = $this->userIdByUsername($username);
        $this->executeQuery('mutation revokeRole($input: RevokeRoleInput!) {
  revokeRole(input: $input) {
      success
  }
}', ['input' => ['roleId' => $roleId, 'userId' => $userId]]);
    }

    /**
     * @Then I don't see :roleId in :username's user roles
     */
    public function iDontSeeInSUserRoles(string $roleId, string $username): void
    {
        $userId = $this->userIdByUsername($username);

        $response = $this->executeQuery(
            'query listRoles($filter: FindRolesInput!) {
              roles(filter: $filter) {
                id
              }
            }',
            ['filter' => ['userId' => $userId]]
        );

        foreach ($response['data']['roles'] as $role) {
            if ($role['id'] === $roleId) {
                throw new \Exception(sprintf(
                    'Role %s was not expected but still found in %s`s roles',
                    $roleId,
                    $username
                ));
            }
        }
    }
}
