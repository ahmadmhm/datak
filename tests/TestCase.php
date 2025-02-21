<?php

namespace Tests;

use App\Services\ElasticsearchService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function tearDown(): void
    {
        app(ElasticsearchService::class)->flushIndex($this->index);
    }
}
