<?php
declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateNewsIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('news', function (Mapping $mapping, Settings $settings): void {
            $mapping->keyword('id');
            $mapping->keyword('source');
            $mapping->text('title');
            $mapping->text('text');
            $mapping->text('link');
            $mapping->date('date');

            $settings->index([
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
                'refresh_interval' => -1
            ]);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('news');
    }
}
