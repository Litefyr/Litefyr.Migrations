<?php

namespace Litespeed\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename Base to Litespeed
 */
class RenameToLighspeedMigration extends AbstractMigration
{
    const OLD_NAME = 'Base';
    const NEW_NAME = 'Litespeed';

    public function up(Schema $schema): void
    {
        $this->rename(true);
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->rename(false);
    }

    /**
     * Execute the migration
     *
     * @param boolean $up
     * @return void
     */
    private function rename(bool $up): void
    {
        $newRootName = $up ? self::NEW_NAME : self::OLD_NAME;
        $oldRootName = $up ? self::OLD_NAME : self::NEW_NAME;

        $this->addSql(
            sprintf(
                "UPDATE neos_neos_domain_model_site SET siteresourcespackagekey = REPLACE(siteresourcespackagekey, '%s.', '%s.')",
                $oldRootName,
                $newRootName
            )
        );

        $this->addSql(
            sprintf(
                "UPDATE neos_contentrepository_domain_model_nodedata SET nodetype = REPLACE(nodetype, '%s.', '%s.')",
                $oldRootName,
                $newRootName
            )
        );
    }
}
