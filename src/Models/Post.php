<?php 

namespace Eventjuicer\Models;


//http://tomazkovacic.com/blog/56/list-of-resources-article-text-extraction-from-html-documents/
//http://www.keyvan.net/2010/08/php-readability/
//https://www.repustate.com/docs/#sample
//http://www.diffbot.com/our-apis/article/ - token 16262ea347d0cead33c443e682685ae9
//http://viewtext.org/



use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Services\AbleTrait;


use Contracts\Context;

//sync Topics 

use Eventjuicer\Services\Syncs\SyncTopicsTrait;
use Eventjuicer\Services\Syncs\SyncTagsTrait;
use Eventjuicer\Services\Syncs\SyncCommentsTrait;

use Eventjuicer\ValueObjects\UTCDateTime;


use Carbon\Carbon;


use Eventjuicer\Models\Traits\AbleTrait;


class Post extends Model 
{

	use AbleTrait;



    use SyncTopicsTrait;
    use SyncTagsTrait;
    use SyncCommentsTrait;


    public static $taggable_pivot_model   = 'Models\PostTags';
    public static $taggable_table 	= 'editorapp_post_tag';
	public static $taggable_table_sync = ['group_id', 'organizer_id', 'published_at', "is_published"];
	
    public static $topicable_table = "eventjuicer_post_topic";
    public static $topicable_table_sync = ["organizer_id", "group_id", "published_at", "is_published"];


    public static $commentable_table_sync = ["organizer_id", "group_id", "event_id"];







	static $visible_posts = array();

    protected $table = "editorapp_posts";


	protected $fillable = ['group_id', 
                            'admin_id',                             
                            'published_at', 
                            'is_promoted',
                            'is_sticky'
                        ];


	//protected $dates = ["published_at"];





    public function shouldBePublished()
    {
        return strtotime($this->getOriginal("published_at")) < strtotime(Carbon::now('UTC')->toDateTimeString());
    }



    function oldtaggings()
    {
         return $this->hasMany('Models\MigrateTagging', "object_id", "id");
    }







 	public function stats()
    {
    	return $this->hasOne("Models\PostStat", "post_id");
    }

    public function meta()
    {
    	return $this->hasOne("Models\PostMeta", "post_id");
    }



    public function author()
    {
    	return $this->hasOne("Models\User", "id", "admin_id");
    }

    public function editor()
    {
        return $this->hasOne("Models\User", "id", "editor_id");
    }



    public function group()
    {
    	return $this->hasOne("Models\Group", "id", "group_id");
    }
  	
  	public function portal()
    {
    	return $this->hasOne("Models\Group", "id", "group_id");
    }


	public function host()
    {
    	return $this->hasOne("Models\Host", "group_id", "group_id");
    }


 

	
	




}