<?php

namespace Litefyr\Migrations\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\ContentRepository\Migration\Domain\Factory\MigrationFactory;
use Neos\ContentRepository\Migration\Domain\Repository\MigrationStatusRepository;
use Neos\ContentRepository\Migration\Command\NodeCommandController;

#[Flow\Scope('singleton')]
class MigrationsCommandController extends CommandController
{
    #[Flow\Inject]
    protected MigrationFactory $migrationFactory;

    #[Flow\Inject]
    protected MigrationStatusRepository $migrationStatusRepository;

    #[Flow\Inject]
    protected NodeCommandController $nodeCommandController;

    #[Flow\InjectConfiguration('node')]
    protected $nodeMigration;

    /**
     * Run definded migrations in setting Litefyr.Migrations.node
     *
     * @param bool $dryRun If true, no changes will be made
     * @return void
     */
    public function initCommand(bool $dryRun = false): void
    {
        $availableMigrations = $this->migrationFactory->getAvailableMigrationsForCurrentConfigurationType();
        if (count($availableMigrations) === 0) {
            $this->outputLine('No migrations available.');
            $this->quit();
        }
        $this->outputLine();
        $nodeMigrations = $this->nodeMigration ?? [];
        if (!isset($nodeMigrations) || !is_array($nodeMigrations) || !count($nodeMigrations)) {
            $this->outputLine('No automatic node migrations defined');
            $this->quit();
        }

        ksort($nodeMigrations);
        $notFound = [];
        $alreadyExecuted = [];
        $toExecute = [];

        foreach ($nodeMigrations as $version => $enabled) {
            if (!$enabled) {
                continue;
            }
            try {
                $migration = $this->migrationFactory->getMigrationForVersion($version)->getUpConfiguration();
                $data = $availableMigrations[$version];
            } catch (\Throwable $th) {
                $notFound[] = $version;
                continue;
            }

            $package = $data['package']->getPackageKey();
            $date = $data['formattedVersionNumber'];
            $comment = $migration->getComments();
            $tableContent = [$version, $date, $package, wordwrap($comment, 60)];

            if (count($this->migrationStatusRepository->findByVersion($version))) {
                $alreadyExecuted[$version] = $tableContent;
                continue;
            }

            $toExecute[$version] = [
                'tableContent' => $tableContent,
                'migration' => $migration,
            ];
        }

        if (count($notFound)) {
            $this->outputFormatted('<error>Following node migrations where not found:</error>');
            foreach ($notFound as $version) {
                $this->outputLine(' - %s', [$version]);
            }
            $this->outputLine();
        }
        if (count($alreadyExecuted)) {
            $this->outputFormatted('Following node migrations where already executed:');
            $tableRows = [];
            foreach ($alreadyExecuted as $version => $tableContent) {
                $tableRows[] = $tableContent;
            }
            $this->output->outputTable($tableRows, ['Version', 'Date', 'Package', 'Description']);
            $this->outputLine();
        }
        if (count($toExecute)) {
            $this->outputFormatted('<success>Run migrations…</success>');
            $this->outputLine();
            $tableRows = [];
            foreach ($toExecute as $version => $item) {
                $tableRows[] = $item['tableContent'];
                if (!$dryRun) {
                    $this->nodeCommandController->migrateCommand($version, true);
                    $this->outputLine();
                    $this->outputLine();
                }
            }
            $this->outputFormatted('<success>Applied following migrations:</success>');
            $this->output->outputTable($tableRows, ['Version', 'Date', 'Package', 'Description']);
            $this->outputLine();
        } else {
            $this->outputFormatted('<info>No migrations to apply</info>');
            $this->outputLine();
        }

        if ($dryRun) {
            $this->outputLine();
            $this->outputFormatted('<info>Dry run, no changes where made</info>');
            $this->outputLine();
        }
    }
}
