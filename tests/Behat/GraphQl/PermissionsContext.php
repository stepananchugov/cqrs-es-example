<?php

declare(strict_types=1);

namespace App\Tests\Behat\GraphQl;

use App\Tests\Behat\DatabasePurgerTrait;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class PermissionsContext extends AbstractGraphQlContext
{
    use DatabasePurgerTrait;
    private array $response;

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
     * @When I request all available roles
     */
    public function iRequestAllAvailableRoles(): void
    {
        $this->response = $this->executeQuery('{roles { id, name } }');
    }

    /**
     * @Then I get list of roles
     */
    public function iGetListOfRoles(TableNode $table): void
    {
        $actualRoleIds = array_map(static function ($row) {
            return $row['id'];
        }, $this->response['data']['roles']);

        $actualRoleNames = array_map(static function ($row) {
            return $row['name'];
        }, $this->response['data']['roles']);

        $expectedRoleIds = array_map(static function ($row) {
            return $row['roleId'];
        }, $table->getHash());

        $expectedRoleNames = array_map(static function ($row) {
            return $row['roleName'];
        }, $table->getHash());

        $idDiff = array_diff($expectedRoleIds, $actualRoleIds);

        if (\count($idDiff) > 0) {
            throw new \Exception('IDs do not match');
        }

        $nameDiff = array_diff($expectedRoleNames, $actualRoleNames);

        if (\count($nameDiff) > 0) {
            throw new \Exception('Names do not match');
        }
    }

    /**
     * @When I request all available permissions
     */
    public function iRequestAllAvailablePermissions(): void
    {
        $this->response = $this->executeQuery('{ permissions { name } }');
    }

    /**
     * @Then I get a list of permissions
     */
    public function iGetAListOfPermissions(): void
    {
        if (0 === \count($this->response['data']['permissions'])) {
            throw new \Exception('No permissions received');
        }
    }

    /**
     * @Then user :currentUser sees :roleName assigned to :username
     */
    public function userSeesRoleAssignedToUser(string $currentUser, string $roleName, string $username): void
    {
        $userId = $this->userIdByUsername($username);

        $response = $this->executeQuery(
            'query listRoles($filter: FindRolesInput!) {
                      roles(filter: $filter) {
                        id
                        name
                      }
                    }',
            ['filter' => ['userId' => $userId]],
            ['X-Auth-Username' => $currentUser, 'Authorization' => $currentUser],
        );
    }

    /**
     * @Then user :currentUser cannot see :username's roles
     */
    public function userCannotSeeUsersRoles(string $currentUser, string $username): void
    {
        throw new PendingException('Permissions and roles need to be set up before checking those');
    }

    /**
     * @Then user :username sees :roleId in his own roles
     */
    public function userSeesInHisOwnRoles(string $username, string $roleId): void
    {
        $userId = $this->userIdByUsername($username);

        $response = $this->executeQuery(
            'query listMyRoles {
                      myRoles {
                        id
                        name
                      }
                    }',
            ['filter' => ['userId' => $userId]],
            ['HTTP_X-Auth-Username' => $username]
        );

        foreach ($response['data']['myRoles'] as $role) {
            if ($role['id'] === $roleId) {
                return;
            }
        }

        throw new \Exception(sprintf(
            'Role %s expected but not found',
            $roleId
        ));
    }
}
