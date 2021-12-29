<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\Permissions\Permission;
use App\Domain\Permissions\Policy;
use EventSauce\EventSourcing\AggregateRootRepository;

final class RevokePermissionHandler
{
    private AggregateRootRepository $policyRepository;

    public function __construct(AggregateRootRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    public function __invoke(RevokePermissionCommand $command): void
    {
        $policy = $this->policyRepository->retrieve($command->policyId());

        if (!$policy instanceof Policy) {
            $policy = Policy::create($command->policyId());
        }

        $policy->revoke(
            new Permission($command->permissionId()),
            $command->roleId()
        );

        $this->policyRepository->persist($policy);
    }
}
