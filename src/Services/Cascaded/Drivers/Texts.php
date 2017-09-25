<?php


namespace Eventjuicer\Services\Cascaded\Drivers;


use Eventjuicer\Services\Cascaded\Cascaded;
use Contracts\Text;
use Illuminate\Database\Eloquent\Model as Eloquent;



class Texts extends Cascaded implements Text
{



    public $levelOverwrite = false; //FALSE = merges different levels

    protected $relation = "texts";

    protected $model = "Eventjuicer\Text";




   public function lookup(array $data, array $options, $replacement = "")
   {
        $data = $data["data"];

        $lang = $this->getLang($options);

        if(isset($data[$lang]))
        {
            return $data[$lang];
        }
        else if(isset($data["*"]))
        {
            return $data["*"];
        }
        else
        {
            return $replacement;
        }
   }


    private function getLang(array $options = [])
    {
        return !empty($options["lang"]) ? $options["lang"] : $this->appcontext->lang();
    }

    
    public function beforeSave(Eloquent $model, $data, $lang)
    {
        $output = isset($model->data) ? $model->data : [];
        $output[$lang] = $data;

        if(!isset($output["*"]) && $lang != "*")
        {
            $output["*"] = $data;
        }

        return $output;

    }



}