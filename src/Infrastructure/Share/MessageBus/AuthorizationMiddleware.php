<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\MessageBus;

use App\Application\AuthorisedMessageInterface;
use App\Application\Query\Permissions\ListPermissionsQuery;
use App\Domain\Permissions\Permission;
use App\Infrastructure\Permissions\Configuration;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class AuthorizationMiddleware implements MiddlewareInterface
{
    private MessageBusInterface $messageBus;

    private bool $isMaster = true;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        // Given that commands can subsequentially produce other commands to be processed,
        // and we're not sure whether those won't be processed asynchronously, we do this `isMaster` check:
        if ($this->isMaster) {
            $this->isMaster = false;
            $query = (new ListPermissionsQuery())->withUserId($message->userId());
            $queryResult = new HandledResult($this->messageBus->dispatch($query));

            $availablePermissions = array_map(
                static fn (array $element): string => $element['name'],
                $queryResult->getResult(),
            );

            // Essentially, classname to permission, we just unwrap the envelope to take a peek at what's inside:
            $requestedPermission = Permission::envelopeToPermission($envelope);
            $success = false;

            foreach ($availablePermissions as $permission) {
                // We treat `app.permissions.*` as wildcard
                if (Permission::PERMISSION_ALL === $permission || str_starts_with($requestedPermission, $permission)) {
                    $success = true;
                    break;
                }
            }

            if (!$success) {
                throw new \Exception(sprintf(
                    'User is not authorized for %s',
                    $requestedPermission
                ));
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
