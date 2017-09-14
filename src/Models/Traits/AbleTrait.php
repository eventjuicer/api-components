<?php

namespace Eventjuicer\Models\Traits;

//http://softonsofa.com/laravel-custom-pivot-model-in-eloquent/

//https://laracasts.com/discuss/channels/general-discussion/updating-a-recorde-on-a-pivot-table


use Eventjuicer\Models\Flag;
use Eventjuicer\Models\PostImage;
use Eventjuicer\Models\Counter;
use Eventjuicer\Models\Text;
use Eventjuicer\Models\Setting;
use Eventjuicer\Models\Widget;
use Eventjuicer\Models\Page;


trait AbleTrait 
{

    public function flags()
    {
        return $this->morphMany(Flag::class, 'flaggable');

    }

    public function images()
    {
        return $this->morphMany(PostImage::class, 'imageable');

    }

    public function counters()
    {
        return $this->morphMany(Counter::class, 'counterable');

    }

    public function texts()
    {
        return $this->morphMany(Text::class, 'textable');
    }

 	public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

	public function widgets()
    {
        return $this->morphMany(Widget::class, 'widgetable');
    }

    public function pages()
    {
        return $this->morphMany(Page::class, 'pageable');
    }






}