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
//            $mapping->geoPoint('location');
//
//            // and analysis configuration
//            $settings->analysis([
//                'analysis' => [
//                    'char_filter' => [
//                        'zero_width_spaces' => [
//                            'type'     => 'mapping',
//                            'mappings' => ['\\u200C=> '],
//                        ],
//                    ],
//                    'filter' => [
//                        'persian_stop' => [
//                            'type'      => 'stop',
//                            'stopwords' => '_persian_',
//                        ],
//                    ],
//                    'analyzer' => [
//                        'persian' => [
//                            'tokenizer'   => 'standard',
//                            'char_filter' => ['zero_width_spaces'],
//                            'filter'      => [
//                                'lowercase',
//                                'arabic_normalization',
//                                'persian_normalization',
//                                'persian_stop',
//                            ],
//                        ],
//                    ],
//                ],
//            ]);
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
