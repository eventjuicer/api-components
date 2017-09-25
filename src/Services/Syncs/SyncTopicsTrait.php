<?php 

namespace Eventjuicer\Services\Syncs;


use Eventjuicer\Services\Syncs\NoSyncTableException;
use Eventjuicer\Services\Syncs\NoSyncAttributesDefined;


trait SyncTopicsTrait {
	
/*
Yourmodel::saving(function($model)
{
    foreach($model->getDirty() as $attribute => $value){
        $original= $model->getOriginal($attribute);
        echo "Changed $attribute from '$original' to '$value'<br/>\r\n";
    }
    return true; //if false the model wont save! 
});

*/
	public static function bootSyncTopicsTrait()
	{

		//check for attr

		if(!isset(static::$topicable_table))
		{
			throw new NoSyncTableException('Please define static $topicable_table');
		}

		if(!isset(static::$topicable_table_sync))
		{
			throw new NoSyncAttributesDefined('Please define static $topicable_table_sync');
		}
/*
		static::saving(function($model){

			

		});
	
*/
		static::saved(function ($model)
		{
			
			//check for dirty attributes?

			if($model->topics->count())
			{
				$data = array_intersect_key((array) $model->getDirty(), array_flip(static::$topicable_table_sync));

				if(!empty($data))
				{
					$model->topics()->newPivotStatement()->where("xref_id", $model->id)->update($data);
				}

			}
		});


	}


	final public function topics()
    {
        return $this->belongsToMany('Eventjuicer\Topic', static::$topicable_table, "xref_id", "topic_id")->withPivot( static::$topicable_table_sync );
    }



}