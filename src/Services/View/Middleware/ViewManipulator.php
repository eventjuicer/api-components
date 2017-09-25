<?php

namespace Eventjuicer\Services\View\Middleware;

use Closure;
use Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use Contracts\View\Parser;
//use Contracts\Template;
use Eventjuicer\Services\View\Assets;


class ViewManipulator
{

    protected $assets, $template;

    function __construct(Assets $assets, Parser $parser)
    {
        $this->assets = $assets;
        $this->parser = $parser;
        
      //  $this->template = $template;
    }

    public function handle($request, Closure $next, $parsable = null)
    {

        $response = $next($request);

        if($response instanceof JsonResponse || $response instanceof RedirectResponse)
        {
        	return $response;
        }

        $content = (string) $response->getOriginalContent();

       // $content =  $this->parser->parseOutput($content);

        $content = $this->assets->merge($content);

		$response->setContent($content);

        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options',        'SAMEORIGIN');
        $response->header('X-XSS-Protection',       '1; mode=block');

        return $response;
    }



}