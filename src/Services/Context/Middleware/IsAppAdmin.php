<?php

namespace Eventjuicer\Services\Context\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;



//custom

use Contracts\Context;



class IsAppAdmin
{
  
    private $context;
    private $usercontext;

    function __construct(Context $context)
    {
        $this->context = $context->level();
        $this->usercontext = $context->user();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    

    public function handle($request, Closure $next, $guard = null)
    {

        if(!$this->context->get("organizer_id") OR !Auth::guard($guard)->check())
        {
            abort(403);
        }

        if(!in_array($this->context->get("organizer_id"), $this->usercontext->account_ids()))
        {
            abort(403);
        }

        return $next($request);
    }
}
