<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\User\UserCollection;
use App\Domain\User\UserCollectionId;
use App\Domain\Permissions\Realm;
use App\Domain\Shared\Exception\AggregateConsistencyException;
use EventSauce\EventSourcing\AggregateRootRepository;

final class RevokeRoleHandler
{
    private AggregateRootRepository $realmRepository;

    private AggregateRootRepository $adminUserCollectionRepository;

    public function __construct(AggregateRootRepository $realmRepository, AggregateRootRepository $adminUserCollectionRepository)
    {
        $this->realmRepository = $realmRepository;
        $this->adminUserCollectionRepository = $adminUserCollectionRepository;
    }

    public function __invoke(RevokeRoleCommand $command): void
    {
        $realm = $this->realmRepository->retrieve($command->realmId());

        if (!$realm instanceof Realm) {
            throw new \LogicException(sprintf(
                'Realm %s does not exist',
                $command->realmId()->toString(),
            ));
        }
        /**
         * @var UserCollection
         */
        $userCollection = $this->adminUserCollectionRepository->retrieve(UserCollectionId::motorsportTicketsId());
        $user = $userCollection->getUserById($command->userId());

        if (null === $user) {
            throw new AggregateConsistencyException(sprintf('Admin user with id \'%s\' does not exist', $command->userId()->toString()));
        }
        $realm->revokeRole(
            $command->userId(),
            $command->roleId()
        );

        $this->realmRepository->persist($realm);
    }
}
