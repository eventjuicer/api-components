<?php

namespace Eventjuicer\ValueObjects;

use Eventjuicer\ValueObjects\Url;

use Closure;

class RichContent {
	
	const URL = "/(?:^|\s)(http[s]?:\/\/(?P<domain>[a-z0-9\-\.]+\.[a-z]{2,})\/[\p{L}0-9=~_:;,\|!#%&\^\?\.\(\)\{\}\-\+\@\/]+)\b/imu";

	protected $text;

	protected $isTidy = false; 

	protected $isDirty = false;

	protected $deadLinks = [];

	function __construct($text = "")
	{
		$this->text = $text;
	}


	public function setDirty($val = true)
	{
		$this->isDirty = (bool) $val;
	}

	public function isDirty()
	{
		return $this->isDirty;
	}

	public function setDeadLink($link)
	{
		$this->deadLinks[] = $link;
	}

	public function getDeadLinks()
	{
		return $this->deadLinks;
	}





	public function equals(RichContent $compare)
	{
		return (string) $this == (string) $compare;
	}

	public function cleanup()
	{

		$text =  preg_replace_callback("/<(?P<url>http[^>]+)>/", function($matches){

			$title = strlen($matches["url"])>50 ? substr($matches["url"], 0, 40) . "..." : $matches["url"];

			return "[".$title."](".$matches["url"].")";

		}, $this->text);


		$text = str_replace(

			array("<3", ":>"), 

			array("[smile]", "[smile]"), $text);

		//any modifiers must return new instance!

		return new self($text); 
	}

	public function hasHtmlTags()
	{
		//body must not contain any standard html tags 

		preg_match_all("/<(?P<tag>[a-z0-9\-]+)(?P<attributes>[^>]*)>/im", $this->text, $matches);
		/*
			some pseudo TAGS are allowed! 
			<link> - markdown self titled link
			<data-xxxx> - internal syntax
		*/
		return array_filter(array_unique($matches["tag"]), function($tag)
		{
			return strpos($tag, "data-")===false && ($tag != "http");
		});
	}

	public function hasHtmlTag($search)
	{
		foreach($this->hasHtmlTags() AS $tag)
		{
			if(stripos($tag, $search)===0)
			{
				return true;
			}
		}
		return false;
	}

	public function hasOnlyHtmlTag($filter)
	{
		return ! (bool) count( array_diff($this->hasHtmlTags(), (array) $filter) ) ;
	}


	public function containsHighMarkdownHeadings()
	{
		return preg_match("/^(?P<markdown>\h*#{1,2}\h*)[^#]*$/m", $this->text);

	}

	public function fixMarkdown(Closure $closure)
	{

		$newBody = preg_replace_callback("/^(?P<markdown>\h*#{1,}\h*)[^#]*$/m", function($match) use ($closure)
        {
        	return str_replace($match["markdown"], $closure($match["markdown"]), $match[0]);

        }, $this->text);

		return $newBody === $this->text ? $this : new self($newBody);
	}


	public function isValid($return = false)
	{

		if(!$return && ($this->isTidy || count($this->hasHtmlTags()) === 0))
		{
			return true;
		}

		if($this->hasHtmlTag("http"))
		{
			throw new \Exception("Duplicate object with cleanup() to fix down markdown <http...>");
		}

		//before using DOM manipulator we must check whether we have valid HTML!
		
		//http://tidy.sourceforge.net/docs/quickref.html#new-blocklevel-tags

		$options = [
			"new-blocklevel-tags" 			=> "data-image,data-text,data-widget",
			"new-empty-tags" 				=> "data-image,data-text,data-widget",
			"char-encoding" 				=> "utf8",
			"input-encoding" 				=> "utf8",
			"output-bom" 					=> false,
			"output-encoding"				=> "utf8",
			"show-body-only"				=> true,
			"newline"						=> "LF",
			"merge-divs"					=> false,
			"drop-proprietary-attributes"	=> false,
			"alt-text"						=> "article image",
			"quote-nbsp" 					=> false
			
		];

		$tidy = new \Tidy();

		$tidy->parseString($this->text, $options);

		$tidy->cleanRepair();

		//HTML may be discovered as UNTIDY when contains markdown <http://...>

		if($tidy->errorBuffer && strpos($tidy->errorBuffer, "Error")) 
		{
			$this->isTidy = false;
		}
		else
		{
			$this->isTidy = true;
		}

		return ($return && $this->isTidy) ? (string) $tidy : $this->isTidy;
	}




	public function repair()
	{
		$repaired = $this->isValid(true);

		return $repaired ? new self($repaired) : $this;
	}


	// public function replaceTags($search, Closure $closure)
	// {

	// 	$parser = new HtmlDomParser();

	// 	/*
	// 	function str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
	// 	*/
	// 	$DOM = $parser->str_get_html("<html><head></head><body>". $this->text .  "</body></html>", true, false, DEFAULT_TARGET_CHARSET, false);

	// 	foreach($DOM->find($search) AS $tag)
	// 	{
	// 		$closure($tag);
	// 	}

	// 	$modified = str_replace(array("<html><head></head><body>", "</body></html>"), "", (string) $DOM);

	// 	$DOM->save();

	// 	return new self( $modified );

	// }


	public function rawUrls()
	{
		preg_match_all(self::URL, $this->text, $matches);

		return isset($matches[1]) ? array_map(function($url){ return new Url($url); }, $matches[1])  : [];
	}

	function __toString()
	{
		return (string) $this->text;
	}




}