Feature: Permissions & roles
  Background:
  As an administrator
  I should be able to edit permissions granted to roles
  I should be able to grant and revoke roles from users

  A permission is granted to a role
  A role is granted to a user
  A user can have several roles, and several sets of permissions, consequently
  Fine-grained stuff should play well with just adding roles on a per-case basis
  Although that would signify that the system is broken and our concept was wrong

  Scenario: Viewing roles
    When I request all available roles
    Then I get list of roles
      | roleId                 | roleName          |
      | ROLE_ADMIN             | Administrator     |
      | ROLE_TRANSLATOR        | Translator        |
      | ROLE_EVENT_MANAGER     | Event manager     |
      | ROLE_MARKETING_MANAGER | Marketing manager |
      | ROLE_CIRCUIT_MANAGER   | Circuit manager   |

  Scenario: Viewing permissions
    When I request all available permissions
    Then I get a list of permissions

  Scenario: Viewing my roles
    Given there is an admin user named "event-manager"
    And there is a role named "Event manager" identified by "ROLE_EVENT_MANAGER"
    And role "ROLE_EVENT_MANAGER" is granted to "event-manager"
    Then user "event-manager" sees "ROLE_EVENT_MANAGER" assigned to "event-manager"
    And user "event-manager" sees "ROLE_EVENT_MANAGER" in his own roles

  Scenario: Viewing other user's roles as admin
    Given there is an admin user named "event-manager"
    And there is an admin user named "admin"
    And role "ROLE_ADMIN" is granted to "admin"
    And role "ROLE_EVENT_MANAGER" is granted to "event-manager"
    Then user "admin" sees "ROLE_EVENT_MANAGER" assigned to "event-manager"

  Scenario: Regular users cannot see other users' roles
    Given there is an admin user named "event-manager"
    And there is an admin user named "admin"
    And role "ROLE_ADMIN" is granted to "admin"
    And role "ROLE_EVENT_MANAGER" is granted to "event-manager"
    Then user "event-manager" cannot see "admin"'s roles

  ## !! Start from here !!
  ## Connection to event sourcing?
  ## What's the profit from CQRS/ES? Optimistic UI
  ## Two states on frontend, UX affected
  ## Taxi orders and bad connections
  ## Editing perms
  Scenario: Granting a permission to a role
    # Create race event should have been "enroll race event" or smth :(
    Given there is a permission named "catalog.race_event.create_race_event"
    And there is a role named "Event manager" identified by "ROLE_EVENT_MANAGER"
    And there are no permissions granted to "ROLE_EVENT_MANAGER"
    When I grant "catalog.race_event.create_race_event" to "ROLE_EVENT_MANAGER"
    Then "catalog.race_event.create_race_event" should be granted to "ROLE_EVENT_MANAGER"

  Scenario: Removing a permission from a role
    Given there is a permission named "catalog.race_event.create_race_event"
    And there is a role named "Event manager" identified by "ROLE_EVENT_MANAGER"
    And permission "catalog.race_event.create_race_event" is granted to "ROLE_EVENT_MANAGER"
    When I revoke "catalog.race_event.create_race_event" from "ROLE_EVENT_MANAGER" role
    Then "catalog.race_event.create_race_event" should not be granted to "ROLE_EVENT_MANAGER"

  Scenario: Assigning a role to a user
    Given there is an admin user named "event-manager" identified by "2ec4ad17-2f9a-499e-bc45-5bd07330eaea"
    And there is a role named "Event manager" identified by "ROLE_EVENT_MANAGER"
    And admin user named "event-manager" has no roles assigned
    When I assign "ROLE_EVENT_MANAGER" to user "event-manager"
    Then I see role ID "ROLE_EVENT_MANAGER" in "event-manager"'s user roles

  Scenario: Revoking a role from a user
    Given there is an admin user named "event-manager" identified by "2ec4ad17-2f9a-499e-bc45-5bd07330eaea"
    And there is a role named "Event manager" identified by "ROLE_EVENT_MANAGER"
    And role "ROLE_EVENT_MANAGER" is granted to "event-manager"
    When I revoke "ROLE_EVENT_MANAGER" from user "event-manager"
    Then I don't see "ROLE_EVENT_MANAGER" in "event-manager"'s user roles
