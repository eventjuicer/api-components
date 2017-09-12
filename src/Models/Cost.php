<?php 


namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\AbleTrait;

use Services\Syncs\SyncTagsTrait;
use Services\Syncs\SyncCommentsTrait;


//http://tomazkovacic.com/blog/56/list-of-resources-article-text-extraction-from-html-documents/
//http://www.keyvan.net/2010/08/php-readability/
//https://www.repustate.com/docs/#sample
//http://www.diffbot.com/our-apis/article/ - token 16262ea347d0cead33c443e682685ae9
//http://viewtext.org/


use Eventjuicer\ValueObjects\Amount;

//use Sofa\Revisionable\Laravel\Revisionable;


class Cost extends Model 
{

	private $amount;


	use AbleTrait;
   // use Revisionable;
    use SyncTagsTrait;
    use SyncCommentsTrait;

    
	protected $fillable = ['originated_at', 'party', 'party_description', 'description', 'amount', 'created_at', 'updated_at', 'currency'];


    protected $table            = "costapp_documents";

  	public static $taggable_table 	= 'costapp_document_tags';
	public static $taggable_table_sync = ['organizer_id', 'group_id', 'event_id', 'originated_at', 'created_at'];
    public static $commentable_table_sync = ["organizer_id", "group_id", "event_id"];


    protected $revisionPresenter = 'Presenters\Revisions\Cost';

    protected $appends = ["amount_original"];



    public function getActivityDescriptionForEvent($eventName)
    {
        if ($eventName == 'created')
        {
            return 'Article "' . $this->party . '" was created';
        }

        if ($eventName == 'updated')
        {
         return 'Article "' . $this->party . '" was updated';
        }

        if ($eventName == 'deleted')
        {
         return 'Article "' . $this->party . '" was deleted';
        }

        return '';
    }



    function taggings()
    {
         return $this->hasMany('Models\MigrateTagging', "object_id", "id");
    }




    public function setPartyAttribute($value)
    {

        $this->attributes['party'] = strtoupper(preg_replace("/[^a-zA-Z0-9]/", "", $value));
    }




    public function setAmountAttribute($value)
    {
    	$value = str_replace(",", ".", $value);

        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value)
    {
            
        return number_format($value / 100,  2, ",", "");
    }


    public function getAmountOriginalAttribute()
    {
        return $this->getOriginal("amount");
    }





   
}
