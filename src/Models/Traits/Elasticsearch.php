<?php

namespace Eventjuicer\Models\Traits;

use Eventjuicer\Models\Observers\ElasticsearchObserver;

trait Elasticsearch
{
    public static function bootSearchable()
    {
        // This makes it easy to toggle the search feature flag
        // on and off. This is going to prove useful later on
        // when deploy the new search engine to a live app.
        //if (config('services.search.enabled')) {
            static::observe(ElasticsearchObserver::class);
        //}
    }

    public function getSearchIndex()
    {
        return str_plural( strtolower( class_basename($this) ) );


       // return $this->getTable();
    }

    public function getSearchType()
    {

        return $this->getSearchIndex();

        // if (property_exists($this, 'useSearchType')) {
        //     return $this->useSearchType;
        // }

        // return $this->getTable();
    }

    public function toSearchArray()
    {

        //we need to find correct presenter...


        if(!property_exists($this, 'presenter'))
        {
        //    throw new \Exception("No presenter defined");
        }



        // By having a custom method that transforms the model
        // to a searchable array allows us to customize the
        // data that's going to be searchable per model.
        return $this->toArray();
    }
}