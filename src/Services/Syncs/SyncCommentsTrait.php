<?php


namespace Eventjuicer\Services\Syncs;


trait SyncCommentsTrait {


	public static function bootSyncCommentsTrait()
	{

		static::saved(function ($model)
		{

			if($model->comments->count())
            {
                $data = array_intersect_key($model->getDirty(), array_flip(static::$commentable_table_sync));

                if(!empty($data))
                {
                    $model->comments()->where("xref_id", $model->id)->update($data);
                }
            }
		});
	}



    public function comments()
    {
        return $this->morphMany('Eventjuicer\Models\Comment', 'commentable');
    }



}