<?php 

namespace Eventjuicer\Contracts;

use Illuminate\Http\Request;

interface SavesPaidOrder {

	function __construct(Request $request);

}