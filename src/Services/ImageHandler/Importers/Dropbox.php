<?php

namespace Eventjuicer\Services\ImageHandler\Importers;


class Dropbox {

	function parse()
	{

		$body = str_replace("www.dropbox", "dl.dropbox", $body);	
		$this->cache_image_file(str_replace("www.dropbox", "dl.dropbox", $url));


	}

	
}

