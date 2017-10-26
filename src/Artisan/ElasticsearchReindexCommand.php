<?php

namespace Eventjuicer\Artisan;

use Eventjuicer\Models\Participant;
use Elasticsearch\Client;
use Illuminate\Console\Command;

class ElasticsearchReindexCommand extends Command
{
    protected $name = "search:reindex";
    protected $description = "Indexes all articles to elasticsearch";
    private $search;

    public function __construct(Client $search)
    {
        parent::__construct();

        $this->search = $search;
    }

    public function handle()
    {
        $this->info('Indexing all participants. Might take a while...');

        foreach (Participant::cursor() as $model)
        {
           
            $id     = $model->id;

            //lets get repository!!!
            $repo   = app()->make($model->getRepository());

            $this->search->index([
                'index' => $model->getSearchIndex(),
                'type'  => $model->getSearchType(),
                'id'    => $model->id,
                'body' => $repo->toSearchArray($id),
            ]);

            // PHPUnit-style feedback
            $this->output->write('.');
        }

        $this->info("\nDone!");
    }
}