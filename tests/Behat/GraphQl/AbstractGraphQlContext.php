<?php

declare(strict_types=1);

namespace App\Tests\Behat\GraphQl;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Testwork\Tester\Result\TestResult;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class AbstractGraphQlContext implements Context
{
    private bool $skipErrors = false;
    private KernelBrowser $browser;
    private array $errors = [];
    private ?string $lastQuery = null;
    private string $uri = '';

    public function __construct(KernelBrowser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @BeforeScenario @negative
     */
    public function skipErrors(): void
    {
        $this->skipErrors = true;
    }

    /**
     * @AfterStep
     */
    public function displayQuery(AfterStepScope $scope): void
    {
        if (null !== $this->lastQuery
            && TestResult::FAILED === $scope->getTestResult()->getResultCode()) {
            echo str_replace('\\n', "\n", $this->lastQuery);
        }
    }

    protected function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    protected function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    protected function errors(): array
    {
        return $this->errors;
    }

    protected function executeQuery(string $query, array $variables = null, array $server = []): array
    {
        $requestBody = ['query' => $query];

        if (null !== $variables) {
            $requestBody['variables'] = json_encode($variables, JSON_THROW_ON_ERROR);
        }

        $this->lastQuery = json_encode([
            'query' => $query,
            'variables' => $variables,
        ], JSON_PRETTY_PRINT);

        $this->browser->request('GET', $this->uri, $requestBody, [], $server);

        $response = json_decode($this->browser->getResponse()->getContent(), true);

        if (!\is_array($response)) {
            throw new \Exception('Expected a response from GraphQL, got nothing');
        }

        if (\array_key_exists('errors', $response)) {
            $errors = implode(
                "\n",
                array_map(static function (array $error) {
                    if (\array_key_exists('debugMessage', $error)) {
                        return $error['message'].': '.$error['debugMessage'];
                    }

                    return $error['message'];
                }, $response['errors'])
            );

            $this->errors = $response['errors'];

            if (!$this->skipErrors) {
                throw new \Exception("Request was not successful: \n".$errors);
            }
        }

        return $response;
    }

    protected function userIdByUsername(string $username): string
    {
        $result = $this->executeQuery('query findUserByUsername($filter: AdminUsersInput) { 
  adminUsers(filter: $filter) {
    id
    username
  }
}', ['filter' => ['username' => $username]]);

        if (!\array_key_exists('adminUsers', $result['data'])
            || !\is_array($result['data']['adminUsers'])
            || 0 === \count($result['data']['adminUsers'])) {
            throw new \Exception(sprintf('User `%s` not found', $username));
        }

        if (\count($result['data']['adminUsers']) > 1) {
            var_dump($result['data']);
            throw new \Exception(sprintf(
                'More than one user found for username `%s`',
                $username
            ));
        }

        return $result['data']['adminUsers'][0]['id'];
    }
}
