<?php 

namespace Eventjuicer\Contracts;


use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

interface Commentable {

	function __construct(Request $request, Config $config);


}