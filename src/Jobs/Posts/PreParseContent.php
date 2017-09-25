<?php

namespace Eventjuicer\Jobs\Posts;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;




use Eventjuicer\ValueObjects\MarkdownContent;
use Contracts\View\Parser;
use Illuminate\Database\Eloquent\Model;

class PreParseContent extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function preparse($str)
    {

        $str = trim($str);

        if(empty($str))
        {
            return $str;
        }

        $md = new MarkdownContent($str);

        $parser = \App::make(Parser::class);

        return $parser->preparseString( $md->html(),  $this->model );

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $model = $this->model;

        if(!property_exists($model, "preparse") OR !is_array($model->preparse))
        {
            return;
        }

        foreach($model->preparse AS $attr)
        {
            //skip if there is no target to save....
            if(is_null( $model->getAttributeValue($attr . "_parsed") ))
            {
                continue;
            }

            $target = $attr . "_parsed";

            //we should mind potential accessors, right?

            $model->{$target} = trim( $this->preparse( $model->getOriginal($attr) ));

        }

        if($model->isDirty())
        {
            if(!is_null( $model->getAttributeValue("_preparsed")))
            {
                $model->_preparsed = 1;
            }

            $model->save();
        }

        

    }

    public function failed()
    {
        //\Log::error("PreParseContent error");
    }
}
