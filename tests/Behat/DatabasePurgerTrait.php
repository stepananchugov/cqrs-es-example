<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use Doctrine\DBAL\Connection;

trait DatabasePurgerTrait
{
    /**
     * @BeforeScenario
     */
    public function purgeDatabase(): void
    {
        $tables = [
            'domain_event',
            'translation',
            'field_translation',
            'banner',
            'series',
            'race_event',
            'race_event_approval',
            'series_race_event',
            'admin_user',
            'platform',
            'oauth_client',
            'oauth_access_token',
            'oauth_refresh_token',
            'oauth_user_credentials',
            'role_assignments',
            'role_permissions',
            'site_series',
            'foreign_event',
            'foreign_series',
            'ticket',
            'ticket_tags',
            'foreign_ticket',
            'image',
            'image_tags',
            'page_layout',
            'language',
            'outer_frame_skin',
            'outer_frame',
            'customer',
            'carousel',
        ];

        $this->getConnection()->executeQuery('TRUNCATE TABLE '.implode(',', $tables).' CASCADE');
    }

    abstract protected function getConnection(): Connection;
}
