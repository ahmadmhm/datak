<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class IndexSocialMediaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:index-social-media {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(protected ElasticsearchService $elasticService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $count = (int) $this->argument('count');

        $this->info("Indexing $count records for each platform...");

        for ($i = 1; $i <= $count; $i++) {
            $this->indexNews($i);
            $this->indexInstagram($i);
            $this->indexTwitter($i);
        }

        $this->info("Finished indexing $count records for each platform.");
    }

    protected function indexNews($id)
    {
        $data = [
            'id' => $id,
            'title' => fake()->sentence,
            'date' => fake()->date,
            'source' => fake()->randomElement(['isna', 'irib']),
            'text' => fake()->paragraph,
            'link' => fake()->url,
        ];

        $this->elasticService->indexDocument('news', time(), $data);
    }

    protected function indexInstagram($id)
    {
        $data = [
            'id' => $id,
            'post_date' => fake()->date(),
            'text' => fake()->sentence,
            'type' => fake()->randomElement(['image', 'video', 'slide']),
            'poster_username' => fake()->userName,
            'post_link' => fake()->url,
        ];

        $this->elasticService->indexDocument('instagram', $id, $data);
    }

    protected function indexTwitter($id)
    {
        $data = [
            'id' => $id,
            'post_date' => fake()->date(),
            'text' => fake()->sentence,
            'poster_username' => fake()->userName,
            'post_link' => fake()->url,
        ];

        $this->elasticService->indexDocument('twitter', $id, $data);
    }
}
