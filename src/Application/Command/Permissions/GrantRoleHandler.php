<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\User\UserCollection;
use App\Domain\User\UserCollectionId;
use App\Domain\Permissions\Realm;
use App\Domain\Shared\Exception\AggregateConsistencyException;
use EventSauce\EventSourcing\AggregateRootRepository;

final class GrantRoleHandler
{
    private AggregateRootRepository $realmRepository;

    private AggregateRootRepository $userRepository;

    public function __construct(AggregateRootRepository $realmRepository, AggregateRootRepository $adminUserCollectionRepository)
    {
        $this->realmRepository = $realmRepository;
        $this->userRepository = $adminUserCollectionRepository;
    }

    public function __invoke(GrantRoleCommand $command): void
    {
        $realm = $this->realmRepository->retrieve($command->realmId());

        if (!$realm instanceof Realm) {
            $realm = Realm::create($command->realmId());
        }

        // OK, they ended up adding this below.
        // First, this should have been a query
        // Second, so what if we grant a role to a user that does not exist? Some cross-querying can get us rid of all unwanted stuff
        //
        // Third. For a complex case (users that did a nasty thing in the past can never become admin):
        // ?? Whose responsibility is this?
        // ?? Can this be solved in read layer? Especially if this is a regular domain query?
        // ?? Can realm know about those nasty deeds? E.g. receive outer events like `UserMarkedAsADisrespectfulPrick`?
        // Conclusion is:
        // * If this is a business (domain) rule, it should be in the domain layer.
        // * Existence checks are pointless.
        $userCollection = $this->userRepository->retrieve(UserCollectionId::defaultCollectionId());
        $user = $userCollection->getUserById($command->userId());

        if (null === $user) {
            throw new AggregateConsistencyException(sprintf('User with id \'%s\' does not exist', $command->userId()->toString()));
        }

        $realm->assignRole($command->userId(), $command->roleId());

        // This feels a bit redundant but makes sense for multi-tenant (non-singletonish) repos
        $this->realmRepository->persist($realm);
    }
}
