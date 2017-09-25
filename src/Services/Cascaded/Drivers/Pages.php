<?php


namespace Eventjuicer\Services\Cascaded\Drivers;

use Eventjuicer\Services\Cascaded\Cascaded;
use Contracts\Page;
use Illuminate\Database\Eloquent\Model as Eloquent;

use Eventjuicer\ValueObjects\MarkdownContent;
use Contracts\View\Parser;




class Pages extends Cascaded implements Page
{


    protected $levelOverwrite = true;

    protected $defaults = array("hidden" => 0);

    protected $parse = ["body"];

    protected $relation = "pages";

    protected $model = "Eventjuicer\Page";


    public function cacheKeys() : Array
    {
        return [];
    }

    public function cacheTags() : Array
    {
        return [];
    }


    protected function key($name) : String
    {
        if(preg_match("/^[a-z\-]+$/", $name))
        {
            return $name;
        }

        return trim(str_slug($name), "-");
    }


    public function beforeSave(Eloquent $model, $data, $lang) : Array
    {

        //preparse data!

        $parser = \App::make(Parser::class);

        $md = new MarkdownContent($data["body"]);

        $data["body_parsed"] = $parser->parseString( $md->html() );

        if(!empty($model->data))
        {
            return array_merge($this->defaults, $model->data, $data);
        }

    
        return array_merge($this->defaults, $data);
    }


}