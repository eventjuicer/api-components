<?php

namespace Eventjuicer\Services\Commentable;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

use Eventjuicer\Comment;

//MY COOL EXTENSIONS

use Eventjuicer\Services\Commentable\Jobs\NotifyUsers;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Eventjuicer\Services\Able;


use Contracts\Commentable AS ICommentable;


class Commentable extends Able implements ICommentable
{
		
	use DispatchesJobs;

	protected $request;
	protected $config;

	function __construct(Request $request, Config $config)
	{
		$this->request 	= $request;

		$this->config 	= $config["commentable"];
	}


	function make(array $params, $model = null, $attribute = null)
	{
		
		$params = $this->_mergeParams($params);

		if(empty($params["comment"]))
		{
			return null;
		}

        $owner = $this->_getRecipient($params, $model);

        return $this->_storeComment( $params, $owner, $attribute);

	}

	 private function _storeComment($params, $owner, $attribute)
    {   

            //CREATE NEW IMAGE
            $comment = new Comment();

            $comment->fill($params);

            $comment->organizer_id  = \Context::level()->get("organizer_id");
            $comment->group_id    	= \Context::level()->get("group_id", $owner);
            $comment->event_id    	= \Context::level()->get("event_id", $owner);
            
            $comment->user_id       = \Context::user()->id(); 
            
            //SAVE AND ATTACH
            $owner->comments()->save($comment);

            //Schema::hasColumn($model->getTable(), $column);

            if(!empty($attribute))
            {
                $owner->{$attribute} = $comment->id;
                $owner->save();
            }

            //SLACK , MAIL ETC.
            $this->dispatch(new NotifyUsers($comment));


            return $comment;
    }


   






}