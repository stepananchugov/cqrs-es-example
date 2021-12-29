<?php

declare(strict_types=1);

namespace App\Application\Seed;

use App\Application\Command\User\CreateUserCommand;
use App\Application\Command\OAuth\CreateUserCredentialsCommand;
use App\Application\Command\Permissions\GrantPermissionCommand;
use App\Application\Command\Permissions\GrantRoleCommand;
use App\Domain\User\UserId;
use App\Domain\Permissions\RealmId;

final class SecuritySeed implements SeedInterface
{
    private string $adminPassword;

    public function __construct(string $adminPassword)
    {
        $this->adminPassword = $adminPassword;
    }

    public function provideCommands(): \Generator
    {
        // For an event to go live, it has to be approved by three separate roles

        // Static seeds allowed us to test separate parts independently, given that the DB is seeded from an empty state
        // This could be any kind of tool, BTW. Even Chromedriver/Selenium
        $UserId = UserId::fromString('d24510a1-a22f-41ba-95fd-cf7f47cf4e47');

        /** Roles already exist. @see \App\Infrastructure\Permissions\Configuration */

        yield new GrantPermissionCommand('*', 'ROLE_ADMIN');
        // CRM style, but naming might be improved
        yield new CreateUserCommand('admin', $UserId);

        // This should be absolutely valid. Commands should be idempotent because we're not sure about the network conditions
        // and/or client errors. And we don't care.
        yield new CreateUserCommand('admin', $UserId);
        yield new CreateUserCommand('admin', $UserId);
        yield new CreateUserCommand('admin', $UserId);

        // TODO: stuff for adding perms to roles
        yield new GrantRoleCommand($UserId, 'ROLE_ADMIN', RealmId::defaultRealmId());
        // Hmm, kinda ok naming :\
        yield new CreateUserCredentialsCommand($UserId->toString(), 'admin', $this->adminPassword);



        $userId = UserId::fromString('2f271d27-1394-413c-8674-b467e86fe13d');
        yield new GrantPermissionCommand('permissions.get_user_roles', 'ROLE_EVENT_MANAGER');
        yield new GrantPermissionCommand('catalog.race_event.approve_race_event', 'ROLE_EVENT_MANAGER');
        yield new GrantPermissionCommand('catalog.race_event.unapprove_race_event', 'ROLE_EVENT_MANAGER');
        yield new CreateUserCommand('event_manager', $userId);
        yield new GrantRoleCommand($userId, 'ROLE_EVENT_MANAGER', RealmId::defaultRealmId());
        yield new CreateUserCredentialsCommand($userId->toString(), 'event_manager', $this->adminPassword);



        $userId = UserId::fromString('74b52cdc-3352-4d3a-b5f0-b3b549e3187c');
        yield new GrantPermissionCommand('permissions.get_user_roles', 'ROLE_MARKETING_MANAGER');
        yield new GrantPermissionCommand('catalog.race_event.approve_race_event', 'ROLE_MARKETING_MANAGER');
        yield new GrantPermissionCommand('catalog.race_event.unapprove_race_event', 'ROLE_MARKETING_MANAGER');
        yield new CreateUserCommand('marketing_manager', $userId);
        yield new GrantRoleCommand($userId, 'ROLE_MARKETING_MANAGER', RealmId::defaultRealmId());
        yield new CreateUserCredentialsCommand($userId->toString(), 'marketing_manager', $this->adminPassword);



        $userId = UserId::fromString('0af45299-a2a5-442b-a608-3bdaa2a46a3c');
        yield new GrantPermissionCommand('permissions.get_user_roles', 'ROLE_CIRCUIT_MANAGER');
        yield new GrantPermissionCommand('catalog.race_event.approve_race_event', 'ROLE_CIRCUIT_MANAGER');
        yield new GrantPermissionCommand('catalog.race_event.unapprove_race_event', 'ROLE_CIRCUIT_MANAGER');
        yield new CreateUserCommand('circuit_manager', $userId);
        yield new GrantRoleCommand($userId, 'ROLE_CIRCUIT_MANAGER', RealmId::defaultRealmId());
        yield new CreateUserCredentialsCommand($userId->toString(), 'circuit_manager', $this->adminPassword);

        // Hmm. Why'd they start doing it like this?
        // yield new GrantPermissionCommand('catalog.race_event.circuit_manager_unapprove_race_event', 'ROLE_CIRCUIT_MANAGER');

    }
}
