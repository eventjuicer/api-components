<?php 

namespace Eventjuicer\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

interface Taggable {

	function __construct(Request $request, Config $config);

	function replace(Model $model, $tag = null);

}