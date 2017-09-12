<?php

namespace ValueObjects;

class MarkdownContent {
	
	protected $clearable = ["@image", "@setting", "@widget","@text"];

	private $original;
	private $content;
	private $transformed;

	function __construct($content = "")
	{
		$this->original = $this->compiled = trim($content);
	}


	public function hasMarkdown()
	{
		$simpleChecks = array("==", "--", "##", "**", "__", "](");
		$regexChecks = array(
			"/_.*_/", 
			"/\*.*\*/",
			"/^>\s/",
			"/^\*\s/m",
			"/\d\./m"
		);
		foreach($simpleChecks AS $str)
		{
			if(strpos($this->original, $str)!==false)
			{
				return true;
			}
		}

		foreach($regexChecks AS $regex)
		{
			if(preg_match($regex, $this->original))
			{
				return true;
			}
		}
		return false;
	}


	public function equals(MarkdownContent $compare)
	{
		return (string) $this == (string) $compare;
	}


	public function length()
	{
		return strlen($this->original);
	}


	public function text()
	{

	//	$str = $this->autoEmbedImages( $str );


		//$str = $this->output($str);

		$str = $this->html();


		$str = $this->fixForeignLinks( $str );
		//now we have all HTML links

		return $str;
	}



	public function lead($compiled = false)
	{
		$str = $this->clear( $this->compiled );

		return $compiled ? $this->clearCompiled(200 ) : $this->clear() ;
	}


	public function clear()
	{	

		$ext = "(" . implode("|", $this->clearable) . ")";
		
		return preg_replace_callback("/(".$ext."\((?P<params>[^\)]+)\))+/", function($matches)
		{
			return "";

		}, $this->content);
	}


	public function fixForeignLinks($str)
	{


		$str = preg_replace_callback('~<(a\s[^>]+)>~isU', function($match)
   		{
   			$link = $match[1];

   			$domain = \Context::level()->getParameter("host");

   		
		    if(strpos($link, BASEHOST)===false)
		    {
		    	return "<$link target='_blank'>"; 
		    }


		  /*  if (strpos($tag, "nofollow"))
		    {
		        return $original;
		    }
		    elseif (strpos($tag, $blog_url) && (!$my_folder || !strpos($tag, $my_folder))) {
		        return $original;
		    }
		    else {
		        return "<$tag rel='nofollow'>";
		    }
			*/


   		}, $str);


		return $str;

	}



	

	function clearCompiled($cut = 200)
	{

		$src = $this->html();

		//strip some <br/> or sth
		$src = strip_tags($src);
			
		//clear images
		$src = preg_replace(VALID_URL, "", $src);	
	
		$src = str_replace(array("\n"), " ", $src);

		if((int) $cut > 0)
		{
			$src = mb_substr($src, 0, (int) $cut, "UTF-8");
		}
		
		//$src = $this->specialchars($src);
		
		return $src;

	}


	public function autoEmbedImages($str)
	{

		$str = preg_replace_callback(VALID_URL, function($match)
		{

			if($this->isImage($match[1]))
			{
				return str_replace($match[1], '<img src="' . $match[1] . '" />', $match[0]);
			}

			return $match[0];

		}, $str);


		return $str;

	}


	function html()
	{
		if(!$this->hasMarkdown())
		{
			return $this->original;
		}

		$this->compiled = \Markdown::defaultTransform($this->original);

		return $this->compiled;

	}


	function __toString()
	{
		return (string) $this->compiled;
	}

}