<?php


namespace Eventjuicer\Services\Cascaded;

use Eventjuicer\Services\Cascaded\Exceptions\KeyNotFoundException;
use Eventjuicer\Services\Cascaded\Exceptions\BadKeyException;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use Log;

use Contracts\Context;
use Illuminate\Config\Repository AS Config;
use Illuminate\Contracts\Cache\Repository AS Cache;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App;



abstract class Cascaded 
{

    protected $levels = array();
    protected $merged = array();
    protected $onpage = array();

    protected $context, $usercontext, $appcontext;
    protected $config = array();
    protected $models = array();

    protected $cache;

    


	final function __construct(Cache $cache, Context $context, Config $config)
	{

        $this->cache        = $cache;

        $this->context      = $context->level();
        $this->usercontext  = $context->user();
        $this->appcontext   = $context->app();

        $this->config       = $config["cascaded"];

	}

    /**

    This is called by EventServiceProvider as we must wait for route matching and correct context...
    **/

    public function make()
    {

        $this->models       = $this->context->contextModels();

        // dd($this->context->current()); array 
        // dd($this->context->model()); topmost model


        //CAHC

        //cachekey - getDriverName(); //settings

       // return;

       
        foreach(["organizer", "group", "event"] AS $level)
        {
            if(!empty($this->models[$level]) && !is_null( $this->items($this->models[$level])))
            {
               // $this->levels[$level] = $this->items($this->models[$level])->pluck("data", "name")->toArray();   

                $this->levels[$level] = $this->items($this->models[$level])->keyBy("name")->toArray(); 

                if(isset($this->levelOverwrite) && $this->levelOverwrite)
                {
                    $this->merged = array_merge($this->merged, $this->levels[$level]);
                }
                else
                {
                    $this->merged = array_replace_recursive($this->merged, $this->levels[$level]);
                }
            }
        }


    }



    final protected function cached(Closure $closure )
    {
         return $this->cache->remember($this->cacheKey(), 10, $closure);
    }


    final protected function items(Eloquent $model)
    {
        return $model->{$this->relation};
    }

    final public function level($level_name = "")
    {
        return isset($this->levels[$level_name]) ? $this->levels[$level_name] : collect();
    }


    /*return */
    final public function model($key, $column = "id")
    {

        $key = $this->key($key);

        if(!$key OR !isset($this->merged[$key]) OR !isset($this->merged[$key][$column]))
        {
            return false;
        }

        return App::make($this->model)->find($this->merged[$key][$column]);


    }

    /*return */
    final public function currentModel($key, $column = "id")
    {

        $key = $this->key($key);

        $data = $this->currentArr($key);

       // dd($data);

        if(!$key OR empty($data) OR !isset($data[$column]))
        {
            return false;
        }

       return App::make($this->model)->find($data[$column]);

    }



	final public function all($full = false)
    {
      
       if($full)
       {
            return $this->merged;
       }

	   return collect($this->merged)->pluck("data", "name");
    }



    final public function save($key, $data, $lang = "*")
    {

        $key = $this->key($key);

        if(empty($key))
        {
            return null;
        }

        //like Organizer->text;

        $relation = call_user_func_array([$this->context->model(), $this->relation], []);

        $model = $relation->firstOrNew(array("name" => $key));

        $model->name     = $key; //if we deal with new object

        $model->user_id  = $this->usercontext->id();

        $data    = method_exists($this, "beforeSave") ? $this->beforeSave($model, $data, $lang) : $data;
        
        //PHP7 json_decode!! new lines fuckup!
        //https://wiki.php.net/rfc/jsond

        if (is_array($data) && version_compare(phpversion(), '6.0', '>'))
        {
                 $data = array_map(function($v){

                    return str_replace(array("\r\n", "\r"), "\n", $v);

                }, $data);
        }
        else
        {
            $data = trim($data);
        }

        $model->data = $data;

        $model->save();
        
        return $key;

        //we should also update lower contexts!!!!

    }


    public function get($key = "", $replacement = null, array $options = [])
    {

        $key = $this->key($key);

        if(!$key)
        {
            throw new BadKeyException($key);
        }

        if(!isset($this->merged[$key]))
        {

            if(is_null($replacement))
            {

                //return Log::notice("cannot find setting {$key}");
               // throw new KeyNotFoundException($key);
            }

            return $replacement;
        }


        //$this->onpage[$key] = $replacement;

        return method_exists($this, "lookup") ? call_user_func_array($this->lookup, [$this->merged[$key], $options, $replacement]) : $this->merged[$key]["data"];

    }



    /*   
        check key for top most context and all sub options...like langs
    */


    public function currentArr($name)
    {
        $name = $this->key($name);

        $level = $this->context->modelName(); //organizer, group, event

        if(!empty($this->levels[$level][$name]))
        {
            return $this->levels[$level][$name];
        }

        return false;
    }


    /*
    
        check key for top most context and specific suboption
    */


    public function current($name, array $options = [])
    {

        $name = $this->key($name);

        $data = $this->currentArr($name);

        if(empty($data))
        {
            return null;
        }
        else
        {
            return method_exists($this, "lookup") ? call_user_func_array($this->lookup, [$data, $options]) : $data["data"];
        }

    }


    protected function key($name)
    {

        //str_slug converts "." to ""!!!

        if(preg_match("/^[a-z0-9\.]+$/", $name))
        {
            return $name;
        }

        return preg_replace("/[^a-z0-9\.]/", "", strtolower($name));

    }


    final protected function getDriverName()
    {
        return strtolower( (new \ReflectionClass($this))->getShortName() ); 
    }




}