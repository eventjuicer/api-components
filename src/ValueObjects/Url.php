<?php

namespace Eventjuicer\ValueObjects;

class Url {
	
	protected $url;
	protected $mimeType;
	protected $mimeSubType;
	protected $size = 0;
	protected $headers;
	protected $parsed;
	protected $path = "";
	protected $query = [];

	function __construct($url = "")
	{

		$url = trim($url);

		if(strpos($url, "//")===0){
			$url = "https:" . $url;
		}

		$this->url = $url;

		$this->parse();

	}

	protected function parse(){

		if(empty($this->url)){
			return;
		}

		$this->parsed = parse_url($this->url);	

		$query = [];

		if($this->parsed && !empty($this->parsed["query"]) ){
			parse_str($this->parsed["query"], $query);	
			$this->query = $query;
		}
	
		if($this->parsed && !empty($this->parsed["path"]) ){
			$this->path = $this->parsed["path"];
		}

		

	}

	public function utms(){

		return array_filter($this->query, function($k){
			return strpos($k, "utm_")!==false;
		}, ARRAY_FILTER_USE_KEY);
	}

	public function path(){
		return $this->path;
	}

	
	function __set($what, $value)
	{

	}


	function __toString()
	{
		return (string) $this->url;
	}

	public function encodeURIComponent($url = "") {
		if(empty($url)){
			$url = $this->url;
		}
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($url), $revert);
	}

	public function getMimeType()
	{
		$this->parseHeaders();

		return $this->mimeType . "/" . $this->mimeSubType;
	}

	public function getSize()
	{
		$this->parseHeaders();

		return $this->size;
	}

	public function isImage($strict = false)
	{
		if($strict)
		{
			$this->parseHeaders();

			return ($this->mimeType == "image");
		}

		return in_array($this->getExt(), array("jpg","gif","jpeg","png"));

	}

	public function getMimeExt()
	{
		$this->parseHeaders();

		return str_replace(array("jpeg"), array("jpg"), $this->mimeSubType);
	}


	public function getExt($valid = ["jpg","gif","png","jpeg"])
	{
		$ext = pathinfo(parse_url($this->url, PHP_URL_PATH), PATHINFO_EXTENSION);

		return in_array($ext, $valid) ? str_replace("jpeg", "jpg", $ext) : $this->getMimeExt();
	}



	private function parseHeaders()
	{
		if(is_null($this->headers))
		{
			$this->getHeaders();

			if(empty($this->headers))
			{
				return;
			}

			foreach($this->headers as $header)
			{
				if(empty($this->mimeType) && strpos($header, "Content-Type: ")!==false)
				{
					preg_match_all("@(?P<type>[a-z]+)/(?P<subtype>[a-z\-]+)@", $header, $matches);

					$this->mimeType = !empty($matches["type"][0]) ? $matches["type"][0] : null;

					$this->mimeSubType = !empty($matches["subtype"][0]) ? $matches["subtype"][0] : null;
					
				}

				if(empty($this->size) && strpos($header, "Content-Length: ")!==false)
				{
					$this->size = substr($header, 16);
				}

			}
		}
	}



	private function getHeaders()
  	{

		/*

		if(@is_array(getimagesize($mediapath))){
		$image = true;
		} else {
		$image = false;
		}

		*/
		$params = array(
			'http' => array(
			'method' => 'HEAD'
		));

		$ctx = stream_context_create($params);
		
		$fp = @fopen($this->url, 'rb', false, $ctx);
		
		if (!$fp)
		return false;  // Problem with url

		$meta = stream_get_meta_data($fp);

		if (empty($meta)){
			fclose($fp);
			return false;  // Problem reading data from url
		}

		$this->headers = isset($meta["wrapper_data"]) && is_array($meta["wrapper_data"]) ? array_reverse($meta["wrapper_data"]) : [];

		fclose($fp);
		
	}







}