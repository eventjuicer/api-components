<?php 

namespace Eventjuicer\Contracts;

interface CascadedData {

//function __construct(Request $request, Config $config);
	public function all();

	public function get($key = "", $replacement = null, array $options = []);

	public function current($name, array $options = []);
}


