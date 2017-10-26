<?php 

namespace Eventjuicer\Repositories;


use Bosnadev\Repositories\Eloquent\Repository as BaseRepository;

use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;
use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;



class Repository extends BaseRepository
{
    
    protected $cache;
    protected $request;
    protected static $postsAlreadyShown = [];




    final public function __construct(Cache $cache, Request $request, App $app, Collection $collection)
    {

        parent::__construct($app, $collection);
      
        $this->cache       = $cache;
        $this->request     = $request;

        
    }

    public function model(){}  

    protected function getIDs(Collection $collection, $pk = "id")
    {

        static::$postsAlreadyShown += $collection->pluck($pk)->all();

        return $collection;
    }

    protected function filter(Collection $collection, $limit = 0)
    {

        $collection = $collection->reject(function($item)
        {
            return in_array($item->id, static::$postsAlreadyShown);
        });

        $collection = $this->getIDs($collection);

        return (int) $limit ? $collection->take($limit) : $collection;
    }

    protected function correctLimit($limit)
    {
        return $limit + count(static::$postsAlreadyShown);
    }




    public final function cached($key = "", $cachetime, Closure $closure)
    {
   
        return $this->cache->remember($this->generateCacheKey($key), $cachetime, $closure);
    }

    protected final function generateCacheKey($key = "")
    {
        return md5($this->request->path() . json_encode($this->request->all()) . $key);
    }




    protected function wrapWithContext($object)
    {
        foreach($this->context->current() AS $context_id => $context_value)
        {
            if($context_id == "portal_id")
            {
                continue;
            }

            $object->{$context_id} = $context_value;
        }

        return $object;
    }



    public function update(array $data, $id, $attribute = "id")
    {
        unset($data["_method"], $data["_token"]);

        return $this->model->where($attribute, '=', $id)->update($data);
    }


    public function updateFlags(array $data, $id)
    {

        $this->skipCriteria();

        $model = $this->findBy("id", $id);

        if(!is_null($model))
        {
            foreach($data AS $flag_name => $flag_value)
            {
                if(!isset($model->{$flag_name}))
                {
                    continue;
                }

                $model->{$flag_name} = $flag_value;
            }

            $model->save();

           
        }

        $this->resetScope();

        return $model;

    }





}