<?php

declare(strict_types=1);

namespace App\Tests\Behat\Domain;

use App\Application\Query\Permissions\ListPermissionsQuery;
use App\Application\Query\Permissions\ListRolesQuery;
use Behat\Gherkin\Node\TableNode;

final class PermissionsContext extends AbstractDomainContext
{
    private array $roles = [];

    private array $permissions = [];

    /**
     * @When I request all available roles
     */
    public function iRequestAllAvailableRoles(): void
    {
        $this->roles = $this->handleMessage(new ListRolesQuery());
    }

    /**
     * @Then I get list of roles
     */
    public function iGetListOfRoles(TableNode $table): void
    {
        // Actual roles have been retrieved in the previous step
        $actualRoleIds = array_map(static function ($row) {
            return $row['id'];
        }, $this->roles);

        $actualRoleNames = array_map(static function ($row) {
            return $row['name'];
        }, $this->roles);

        // Table node contains the expectations
        $expectedRoleIds = array_map(static function ($row) {
            return $row['roleId'];
        }, $table->getHash());

        $expectedRoleNames = array_map(static function ($row) {
            return $row['roleName'];
        }, $table->getHash());

        if (\count(array_diff($actualRoleNames, $expectedRoleNames)) > 0) {
            throw new \Exception('Role names mismatch');
        }

        if (\count(array_diff($actualRoleIds, $expectedRoleIds)) > 0) {
            throw new \Exception('Role IDs mismatch');
        }
    }

    /**
     * @When I request all available permissions
     */
    public function iRequestAllAvailablePermissions(): void
    {
        $this->permissions = $this->handleMessage(new ListPermissionsQuery());
    }

    /**
     * @Then I get a list of permissions
     */
    public function iGetAListOfPermissions(): void
    {
        // These are populated dynamically based on available commands/queries
        if (0 === \count($this->permissions)) {
            throw new \Exception('Got no permissions');
        }
    }
}
