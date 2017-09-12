<?php

namespace ValueObjects;

use ValueObjects\Url;


class UrlImage {
	
	protected $url;
	protected $normalized;

	function __construct(Url $url)
	{
		$this->url = $url;
	}

	function __set($what, $value)
	{

	}

	function __toString()
	{
		return (string) $this->url;
	}

	/* compat with Fp20post */

	private function safeurl($source, $enc="utf-8", $cut = true)
	{

		$string = mb_strtolower($source, $enc);
		$string = preg_replace(
			array('/\s{2,}/','/ą/','/ć/','/ę/','/ł/','/ń/','/ó/','/ś/','/ż/','/ź/'), 
			array(' ','a','c','e','l','n','o','s','z','z'), $string);
		$string = html_entity_decode($string);
		if($cut){
		 	$arr = explode(" ",$string);
			$arr = array_slice($arr,0,10);
			$string = implode(" ",$arr);
		}
		$string = str_replace(array('_','..','. ',' ','/'), '-', $string);
		$string = preg_replace('/[^a-z0-9\-\.]/i', '', $string);

		$string = trim($string, " -.");
		$string = str_replace('--', '-', $string);
		$string = str_replace('--', '-', $string);
		return urlencode($string);

	}

	function ext()
	{

	}


	function __call($what, $params)
	{
		if(method_exists($this->url, $what))
		{
			return call_user_func_array(array($this->url, $what), $params);
		}
	}

	function hash($fromMime = false)
	{
		if($fromMime)
		{

			return sha1($this->safeurl( (string) $this->url )) . "." . $this->url->getExt();
		}

		return sha1($this->safeurl( (string) $this->url )) . strtolower( $this->find_image_ext( (string) $this->url ) );
		
	}/*eom*/


	function hashed()
	{
		return sha1( $this->safeurl( (string) $this->url )); 
	}

	

	public function isValid()
	{
		return $this->url->isImage(true);
	}
	

	public function cache_image_file($url = "", $path = "posts", $id = 0)
	{
	
		if(!self::$cache_images)
		{
			return false;
		}

		$id = !empty($id) ? $id : $this->id;
		
		$ext = $this->find_image_ext($url);
	
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
	}/*eom*/
		
	
	public function find_image_ext($source )
	{
		if(preg_match_all("@([^\s]+(\.(?i)(jpeg|jpg|png|gif))$)@", (string) $source, $images))
		{
			return $images[2][0];		
		}
		return false;
	}/*eom*/
	

	public function get_static_image_file($original = "")
	{
		return str_replace(ROOT, "", $original);
		
	}/*eom*/

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
		
	}/*eom*/


	function is_untidy()
	{
		return preg_match("/<[^>]+>/",$this->headline) OR preg_match("/<[^>]+>/", $this->body);
	}/*eom*/

	function checkForImages()
	{

		if(preg_match_all(VALID_URL, $body, $urls))
		{						
			foreach($urls[0] AS $url)
			{										
				//translate cloud app urls		
				if(strpos($url, "/cl.ly/")!==false)
				{
					$data =	json_decode(fetch_external_data($url), true);					
					if(isset($data["remote_url"]))
					{				
						$body = str_replace($url, $data["remote_url"], $body);	
						$this->cache_image_file($data["remote_url"]);	
					}				
		
				}
				elseif(strpos($url, "dropbox.com/s/")!==false)
				{
					//outside PUBLIC
					//https://www.dropbox.com/s/mx80aofgpgrem0n/signature.png
					//https://dl.dropbox.com/s/mx80aofgpgrem0n/signature.png?dl=1
					
					$body = str_replace("www.dropbox", "dl.dropbox", $body);	
					$this->cache_image_file(str_replace("www.dropbox", "dl.dropbox", $url));
					
				}
				else
				{
					$this->cache_image_file($url);
				}
			}
		}
	}


}