<?php


namespace Eventjuicer\Services\Syncs;

use Eventjuicer\Services\Syncs\NoSyncTableException;
use Eventjuicer\Services\Syncs\NoSyncAttributesDefined;

trait SyncTagsTrait {


	public static function bootSyncTagsTrait()
	{

		//check for attr

		if(!isset(static::$taggable_table))
		{
			throw new NoSyncTableException('Please define static $taggable_table');
		}

		if(!isset(static::$taggable_table_sync))
		{
			throw new NoSyncAttributesDefined('Please define static $taggable_table_sync');
		}

		static::saved(function ($model)
		{
			if($model->tags->count())
			{
				$data = array_intersect_key((array) $model->getDirty(), array_flip(static::$taggable_table_sync));

				if(!empty($data))
				{
					$model->tags()->newPivotStatement()->where("xref_id", $model->id)->update($data);
				}

			}
		});



	}

	final public function taggings()
	{
		  return $this->hasMany(static::$taggable_pivot_model, "xref_id");
	}


	final public function tags()
    {
        return $this->belongsToMany('Eventjuicer\Tag', static::$taggable_table, "xref_id", "tag_id")->withPivot( static::$taggable_table_sync );
    }
    
    public function mostUsedTags()
    {
        return \Eventjuicer\Tag::join(static::$taggable_table, static::$taggable_table.'.tag_id', '=', 'bob_tags.id')->groupBy('bob_tags.id')->orderBy('tag_count', 'desc')->get(['bob_tags.id', 'bob_tags.name', \DB::raw('count(bob_tags.id) as tag_count')]);
    }

    final public function tagsToArray()
    {
        if(is_null($this->tags))
        {
            return array();
        }

    	return $this->tags->pluck("name", "id");
    }

    final public function tagsToString()
    {
    	return $this->tagsToArray()->implode(",");
    }




}