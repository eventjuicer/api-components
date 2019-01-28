<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Contracts\Imageable;



//http://tomazkovacic.com/blog/56/list-of-resources-article-text-extraction-from-html-documents/
//http://www.keyvan.net/2010/08/php-readability/
//https://www.repustate.com/docs/#sample
//http://www.diffbot.com/our-apis/article/ - token 16262ea347d0cead33c443e682685ae9
//http://viewtext.org/


class PostMeta extends Model
{

	protected $primaryKey = 'post_id';

    protected $table = "editorapp_post_meta";

    public $timestamps = false;

    public $incrementing = false;

	protected $fillable = ['headline', 'quote','quote_parsed','body', 'body_parsed', 'guestauthor','metatitle','metadescription'];
	
	//protected $touches = ['post'];
	
	public $preparse = ["quote", "body"];



    public function post()
    {
    	return $this->belongsTo("Models\Post", "post_id", "id");
    }


    public function images()
    {
    	return $this->post->images;
    }


  
/**
*
* FUNCTIONS
*
*/




/*

	public function friendlylink()
	{
		return str_slug($this->headline) . "," . $this->post_id;

	}



	function is_untidy()
	{
		return preg_match("/<[^>]+>/",$this->headline) OR preg_match("/<[^>]+>/", $this->body);
	} 
	

*/


}










/*


			$this->id 			= $id;
			$this->headline 	= $this->specialchars(stripslashes($this->headline));
			$this->body 		= stripslashes($this->body);
			$this->friendlylink = safeurl($this->headline) . ",". $this->id;

			$this->postlink 	= '/posts/' . $this->id;
			$this->permalink 	= 'http://' . HOST . $this->postlink;

			$this->date 		= $this->publishedon ? date("d.m.Y", $this->publishedon) : date("d.m.Y", $this->createdon);
					
			//author						
			$this->author 		= getval(Fp20::$authors, $this->admin_id); 
			$this->avatar 		= $this->avatar(); 
								
			//html versions
			$this->html 		= $this->translate_body($this->body); //adds markdown
			
			$this->quote 		= $this->clear_markdown(stripslashes($this->quote));
			
			$this->lead 		= strlen($this->quote) > 5 ? $this->quote : $this->make_lead($this->body, 300); //clear all
			
			$this->cover_url 	= $this->get_cover($this->body, true); //first image
			$this->cover		= $this->cover_url ? a($this->friendlylink, $this->embed_image($this->cover_url, ""), 'class="thumbnail" style="margin-bottom: 1em;"') : "";			
			
			
			$this->trait 		= mb_substr($this->headline, 0, 50, "UTF-8"); //used in comments, tasks reference
			
			
			//comments, tags....
			$this->object_id 	= $this->id;
						
			if(self::$handle_tags)
			{
				//tags
				$tags = array_values($this->tags());
				$this->tags = implode(", ", $tags);
			}









	function stats()
	{
		$this->stats = $this->autoload("bob_post_stats", $this->id, "post_id");
		
		//stats
		$this->interactivity = max(1, $this->comments) * max(1, $this->likes) * max(1, $this->pageviews);
		
		return $this->stats;
	} 

	
	function counter($type, $dir)
	{
		
		$val = ($dir == "added") ? "+" : "-";
		
		if($this->stats())
		{
			return Quickie_Database::query("UPDATE bob_post_stats SET {$type} = {$type}{$val}1 WHERE post_id = {$this->id}");
		}
		
		return Quickie_Database::insert("bob_post_stats", array("post_id" => $this->id, $type => 1));
	
	} 
	
	
	
	function setauthor($admin_id = 0)
	{
		
		if(Quickie_Database::update($this->ns, compact("admin_id"), array("id" => $this->id)))
		{
			$this->load($this->id);
			//return new state
			return $this->admin_id;
		}
		
	} 
	
	
	function setstate($state = "")
	{
		$allowed = array("is_published", "is_promoted", "is_sticky");
		
		if(!in_array($state, $allowed))
		{
			return false;
		}
		
	
		if(Quickie_Database::update($this->ns, array($state => !$this->{$state}), array("id" => $this->id)))
		{	
			$this->load($this->id);
		}
		
		if($state == "is_published" AND $this->{$state} AND !$this->publishedon)
		{

			$publishedon = (date("H") <  "09" OR date("H") > "21") ? (strtotime("noon") - 3600 * 3) : time();

			$this->update_base_raw(compact("publishedon"));
		}
		
		return $this->{$state};
	
	} 
	
	
	function delete()
	{
		$this->unbind_tags($this->object_name, $this->id);
		
		//unbind comments		
		//Quickie_Database::delete("bob_newsdesk_comments", array("object_name" => $this->object_name, "object_id" => $this->id));
		
		return Quickie_Database::delete($this->ns, array("id" => $this->id));
	} 
	

	public function cache_image_file($url = "", $path = "posts", $id = 0)
	{
	
		if(!self::$cache_images)
		{
			return false;
		}

		$id = !empty($id) ? $id : $this->id;
		
		//
		//$ext = $this->find_image_ext($url);
	
		if(!$ext){ return false; }
		
		$path 		= DIR_STATIC . DS . $path . DS . $id;
		$file		= sha1(safeurl($url)) . strtolower($ext);
		
		if(file_exists($path . DS . $file))
		{
			return $this->get_static_image_file($path . DS . $file); 
		}
		
		//get and save ...
		//get ...
		
		$original 	= @file_get_contents($url,

			false,
		    stream_context_create(
		        array(
		            'http' => array(
		                'ignore_errors' => true
		            )
		        )
		    )

    	);
		
		if(empty($original)){ return false; }
		
		//can save?
		
		if(!file_exists($path)){ mkdir($path); }
		
		//save
		
		if(file_put_contents($path . DS . $file, $original))
		{
			return $this->get_static_image_file($path . DS . $file); 
		}
		
		return null;
	} 
		
	
	public function find_image_ext($url = "")
	{
		if(preg_match_all("@([^\s]+(\.(?i)(jpeg|jpg|png|gif))$)@", $url, $images))
		{
			return $images[2][0];		
		}
		return false;
	} 
	

	public function get_static_image_file($original = "")
	{
		return str_replace(ROOT, "", $original);
		
	} 

	public function translate_images($str = "", $path = "posts")
	{
		
		if(preg_match_all(NON_MARKDOWN_URL, $str, $urls))
		{				
			
			foreach($urls[0] AS $i => $url)
			{	
				
				if(stripos($url, "youtube.com")!==false)
				{										
					$str = str_ireplace($url, embed_youtube($url, 770), $str);
				}
				else if(stripos($url, "slideshare")!==false)
				{
					$str = str_ireplace($url, embed_slideshare($url, 770), $str);
				}
				else if(stripos($url, "vimeo.com")!==false)
				{
					//rewrite url
					$str = str_ireplace($url, embed_vimeo($url, 770), $str);		
				}
				else
				{
					$file = $this->cache_image_file($url, $path);

					if($file)
					{		
						$str = str_ireplace($url, $this->embed_image($this->get_static_server($file)), $str);				
					}	
				}
				
							
			
			}//foreach
		}
		
		return $str;
		
	} 

	protected function get_cover($str = "", $raw = false)
	{
		if(preg_match_all(VALID_URL, $str, $urls))
		{
			foreach($urls[0] AS $i => $url)
			{
				$file = $this->cache_image_file($url);

				if($file)
				{					
					return  $raw ? $this->get_static_server($file) : a($this->friendlylink, $this->embed_image($this->get_static_server($file), ""), 'class="thumbnail"');				
				}
			}//foreach
		}
		
		return "";

	} 
	


	
	public function translate_body($src = "")
	{
		
		//$src = $this->specialchars($src);
		
		//images
		$src = $this->translate_images($src);
			 		
		//markdown
		$src = markdown($src);
		
		return $src;
	}

	
	function raw($what = "")
	{
		$src = $this->translate_body($what);
		
		$src = strip_tags($src);
		
		return $src;
	}

	

	
*/