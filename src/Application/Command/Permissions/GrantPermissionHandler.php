<?php

declare(strict_types=1);

namespace App\Application\Command\Permissions;

use App\Domain\Permissions\Event\Policy\PermissionGranted;
use App\Domain\Permissions\Permission;
use App\Domain\Permissions\Policy;
use EventSauce\EventSourcing\AggregateRootRepository;

final class GrantPermissionHandler
{
    private AggregateRootRepository $policyRepository;

    public function __construct(AggregateRootRepository $policyRepository, $bus)
    {
        $this->policyRepository = $policyRepository;
    }

    public function __invoke(GrantPermissionCommand $command): void
    {
        $policy = $this->policyRepository->retrieve($command->policyId());

        if (!$policy instanceof Policy) {
            // A policy itself consists of permission grants. Makes sense to create it anyway
            // Remember, presence checks are in queries/UI layer
            $policy = Policy::create($command->policyId());
        }

        // ↓ Jump here ↓
        $policy->grant(
            new Permission($command->permissionId()),
            $command->roleId()
        );

        $this->policyRepository->persist($policy);

        // Shouldn't it be a different event?
        // $this->bus->dispatch(new PermissionGranted());
        // It must be in cases where event persistence is outside the handler layer
    }
}
