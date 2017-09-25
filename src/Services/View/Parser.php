<?php

namespace Eventjuicer\Services\View;

use Contracts\View\Parser as ParserInterface;

use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

use Eventjuicer\Services\View\Exceptions\InvalidBladeExtensionsHandlerException;
use Contracts\Context;
use Illuminate\Contracts\Cache\Repository as Cache;
use Eventjuicer\Services\View\HTML5\HTML5;
use Eventjuicer\Services\View\Assets;


use Blade;


class Parser implements ParserInterface
{


	protected $output;

	private $config;

	protected $parser;

	protected $cache;

	protected $assets;

	function __construct($output = "", $assets = "")
	{
		$this->output = $output;

		$this->assets = $assets;

		//$this->template = $template;

		//Request $request, array $config, Context $context, Cache $cache
			
		//$this->config = $config;
		
		//$this->cache = $cache;
	}

	public function parseDataNodes($content, $parentObject = null, $preparse = false)
	{

        if(strpos($content, "<data-")===false)
        {
            return $content;
        }

        if(strpos($content, "<body>")===false)
        {
           $content = '<!DOCTYPE html><html><body>'.$content.'</body></html>';
        }

		$parser = new HTML5([
			"encode_entities"	=> false,
			"disable_html_ns" 	=> false,
            "xmlNamespaces"     => false,
            "preparse"          => $preparse,
            "parentObject"      => $parentObject,
            "dispatcher"        => app("Contracts\View\Dispatch")
			]);

        $parsed = $parser->loadHTML($content);   

        $out = $parser->saveHTML($parsed, []);

       // $test = preg_match_all("/(?<=<html>)(.*)(?=<\/html>)/s", $out, $matches);
        
        preg_match_all("/<body>(.*)<\/body>/s", $out, $matches);

        return $matches[1][0];

	}

    public function clearPHP($str)
    {
        return implode('', array_map(function($a){
            return is_array($a) && $a[0] == T_INLINE_HTML ? $a[1] : '';
        }, token_get_all($str)));
    }


	public function parseBlade($content, array $args = array())
    {
        if(strpos($content, "@")===false)
        {
            return $content;
        }

        if(!preg_match("/@[a-z]/", $content))
        {
            return $content;
        }

        $generated = Blade::compileString($content);

        ob_start() and extract($args, EXTR_SKIP);
	
        try
        {
            eval('?>'.$generated);
        }

        catch (\Exception $e)
        {
            
            ob_get_clean();
            throw $e;
        }

        return ob_get_clean();

    }


 	function parseString($str, $model = null)
 	{

 		$str = $this->parseDataNodes($str, $model);

 		$str = $this->parseBlade($str, array());

 		return $str;

 	}	

    /**
    
        This one is used when we save articles...

    **/

    function preparseString($str, $model = null)
    {
     
        $str = $this->parseDataNodes($str, $model, true);

        $str = $this->parseBlade($str, array());

        return $str;

    }   

    function parseOutput($output)
    {
        $output = $this->parseString($output);

             //UTF-8 BOM
        $output = str_replace(array("\xEF\xBB\xBF"), array(""), $output); 

        return $output;    

    }



}