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

    public function search($indexes, $query)
    {
        return $this->client->search([
            'index' => $indexes,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['text', 'title', 'poster_username'],
                    ],
                ],
            ],
        ]);
    }

    protected function responseHandler($response): array
    {
        return match ($response->getStatusCode()) {
            Response::HTTP_OK, Response::HTTP_CREATED => [
                'success' => true,
                'data' => $response->asString() != '' ? $response->asArray() : [],
            ],
            Response::HTTP_NOT_FOUND, Response::HTTP_INTERNAL_SERVER_ERROR => [
                'success' => false,
                'message' => $response->getReasonPhrase(),
            ]
        };
    }
}
