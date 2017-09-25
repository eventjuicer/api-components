<?php


namespace Eventjuicer\Services\CascadedSettings;

use Eventjuicer\Services\CascadedSettings\CascadedSettingsKeyNotFoundException;

use Illuminate\Support\Collection;

use Eventjuicer\Setting;



use Illuminate\Http\Request;


use Contracts\Context;

use Eventjuicer\Services\Able;



class CascadedWidgets 
{

    protected $request;
    protected $context;
    protected $config;

    protected $settings = array();

    protected $merged;

	function __construct(Request $request, Context $context, array $config)
	{

        $this->request = $request;
        $this->context = $context;
        $this->config  = $config;

        $this->resolve();
	}


    function resolve()
    {
        $this->merged = new Collection;

        foreach($this->context->contextModels() AS $src_context => $model)
        {
            if(is_null($model))
            {
                continue;
            }

            $settings = $model->settings;

            if($settings instanceof \Illuminate\Support\Collection)
            {
                $this->settings[$src_context] = $settings->keyBy("name");
                $this->merged = $this->merged->merge( $this->settings[$src_context]);
            }
        }
    }


//http://stackoverflow.com/questions/25035162/get-single-item-with-particular-value-on-hasmany-relation-laravel
//http://softonsofa.com/tweaking-eloquent-relations-how-to-get-latest-related-model/
///http://softonsofa.com/laravel-querying-any-level-far-relations-with-simple-trick/

    final public function save()
    {

        $name = str_slug(trim($this->request->input("name")));
        $data = $this->request->input("value");

        if($this->context->get_organizer()->hasSetting($name))
        {
            $setting = $this->context->get_organizer()->setting($name);
            $setting->update(compact("data"));
        }
        else
        {
            return new Settingable($this->request->all(), $this->context);
        }

    }


	final public function all()
    {
		return $this->merged;//>lists("name", "data");
    }



    public function __call($name, $args)
    {

        $replacement = isset($args[1]) ? $args[1] : null;

        if(in_array($name, array("organizer", "group", "event")))
        {
            $source = $name . "_settings";

            $settings = $this->pair( self::$$source );

            return isset($settings[$args[0]]) ? $settings[$args[0]] : $replacement;
        }
    }


    final public function get($key = "", $replacement = null)
    {

    	$key = str_slug(trim($key));

        if(!$key || (!isset($this->merged[$key]) && is_null($replacement)))
        {
            new CascadedSettingsKeyNotFoundException($key);
            return;
        }


    	return isset($this->merged[$key]) ? $this->merged[$key]->data : $replacement; 
    }


    final public function current()
    {
    	//find highest level context!
    }

    

}