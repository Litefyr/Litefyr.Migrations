<?php

namespace Litespeed\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename Base to Litespeed
 */
class RenameToLighspeedMigration extends AbstractMigration
{
    const RENAME_VENDOR = [
        'Base' => 'Litespeed',
    ];
    const RENAME_PACKAGES = [
        'Theme' => 'Integration',
    ];

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
        foreach (self::RENAME_VENDOR as $old => $new) {
            $oldVendor = $up ? $old : $new;
            $newVendor = $up ? $new : $old;

            $this->addSql(
                sprintf(
                    "UPDATE neos_neos_domain_model_site SET siteresourcespackagekey = REPLACE(siteresourcespackagekey, '%s.', '%s.')",
                    $oldVendor,
                    $newVendor
                )
            );

            $this->addSql(
                sprintf(
                    "UPDATE neos_contentrepository_domain_model_nodedata SET nodetype = REPLACE(nodetype, '%s.', '%s.')",
                    $oldVendor,
                    $newVendor
                )
            );
        }

        foreach (self::RENAME_PACKAGES as $old => $new) {
            $oldPackage = $up ? $old : $new;
            $newPackage = $up ? $new : $old;

            $this->addSql(
                sprintf(
                    "UPDATE neos_neos_domain_model_site SET siteresourcespackagekey = REPLACE(siteresourcespackagekey, '.%s', '.%s')",
                    $oldPackage,
                    $newPackage
                )
            );

            $this->addSql(
                sprintf(
                    "UPDATE neos_contentrepository_domain_model_nodedata SET nodetype = REPLACE(nodetype, '.%s', '.%s')",
                    $oldPackage,
                    $newPackage
                )
            );
        }
    }
}
