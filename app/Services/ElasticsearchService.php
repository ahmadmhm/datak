<?php

namespace App\Services;

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

    public function indexDocument($index, $id, $data)
    {
        $params = [
            'index' => $index,
            //            'id' => $id,
            'body' => array_merge($data, [
                'datetime' => now(),
                'timestamp_ts' => now()->timestamp,
            ]),
        ];
        $response = $this->client->index($params);

        return $this->responseHandler($response)['success'];
    }

    public function getAll($indexes, $query)
    {
        return $this->client->search([
            'index' => $indexes,
            'size' => 200,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass, // returns all documents
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
            $filter[] = [
                'term' => ['source' => $query['source']],
            ];
        }
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

        // Use a 'should' clause so that if either condition matches, the document is returned.
        $filter[] = [
            'bool' => [
                'should' => $rangeConditions,
            ],
        ];

        // Assemble the complete query.
        $queryBody = [
            'query' => [
                'bool' => [
                    'must' => $must,
                    'filter' => $filter,
                ],
            ],
        ];

        $size = $query['size'] ?? 100;

        return [$size, $queryBody];
    }

    protected function responseHandler($response): array
    {
        return match ($response->getStatusCode()) {
            Response::HTTP_OK, Response::HTTP_CREATED => [
                'success' => true,
                'data' => $response->asString() != '' ? $response->asArray()['hits']['hits'] : [],
            ],
            Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR => [
                'success' => false,
                'message' => $response->getReasonPhrase(),
            ]
        };
    }
}
