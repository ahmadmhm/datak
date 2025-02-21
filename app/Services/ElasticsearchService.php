<?php

namespace App\Services;

use App\Observers\NewsObserver;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Response;

class ElasticsearchService
{
    public function __construct(protected $client = null)
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('elastic.client.connections.default.host')])
            ->build();
    }

    public function indexDocument($index, $data)
    {
        $params = [
            'index' => $index,
            'body' => array_merge($data, [
                'datetime' => now(),
                'timestamp_ts' => now()->timestamp,
            ]),
        ];
        try {
            $response = $this->client->index($params);
            app(NewsObserver::class, ['userId' => 1])->created($index);

            return $this->responseHandler($response)['success'];
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getAll($indexes, $query)
    {
        return $this->client->search([
            'index' => $indexes,
            'size' => 200,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass,
                ],
            ],
        ]);
    }

    public function search($indexes, $query)
    {
        [$size, $queryBody] = $this->applyFilters($query);

        $result = $this->client->search([
            'index' => $indexes,
            'size' => $size,
            'body' => $queryBody,
        ]);

        return $this->responseHandler($result);
    }

    protected function applyFilters($query): array
    {
        $must = ! empty($query['context'])
            ? [['match' => ['text' => $query['context']]]]
            : [['match_all' => new \stdClass]];

        $filter = [];
        if (! empty($query['dateFrom'])) {
            $dateRange['gte'] = $query['dateFrom'];
        }
        if (! empty($query['dateTo'])) {
            $dateRange['lte'] = $query['dateTo'];
        }
        if (! empty($query['source'])) {
            $filter[] = ['term' => ['source' => $query['source']]];
        }
        if (! empty($query['user_id'])) {
            $filter[] = ['term' => ['user_id' => $query['user_id']]];
        }
        if (! empty($dateRange)) {
            $rangeConditions = [
                [
                    'bool' => [
                        'must' => [
                            ['exists' => ['field' => 'date']],
                            ['range' => ['date' => $dateRange]],
                        ],
                    ],
                ],
                [
                    'bool' => [
                        'must' => [
                            ['exists' => ['field' => 'post_date']],
                            ['range' => ['post_date' => $dateRange]],
                        ],
                    ],
                ],
            ];
        }
        if (! empty($rangeConditions)) {
            $filter[] = [
                'bool' => [
                    'should' => $rangeConditions,
                ],
            ];
        }

        // Assemble the complete query.
        $queryBody = [
            'query' => [
                'bool' => [
                    'must' => $must,
                    'filter' => $filter,
                ],
            ],
        ];

        $size = $query['size'] ?? 200;

        return [$size, $queryBody];
    }

    protected function responseHandler($response): array
    {
        return match ($response->getStatusCode()) {
            Response::HTTP_OK, Response::HTTP_CREATED => [
                'success' => true,
                'data' => $response->asString() != '' ? ($response->asArray()['hits']['hits'] ?? []) : [],
            ],
            Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR => [
                'success' => false,
                'message' => $response->getReasonPhrase(),
            ]
        };
    }
}
