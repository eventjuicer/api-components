<?php

namespace Eventjuicer\Services\View\Mappings;

use Eventjuicer\Services\Presenter\Presenter;

class Post {
	
	protected $data;

	function __construct(Presenter $data)
	{
		$this->data = $data;
	}


	public function map()
	{
		return [];

		return [

				"title" => $this->data->headline,

				"description" => "", 

				"thumbnail" => "",//$this->data->cover(),

				"og:title" => isset($this->data->meta) ? $this->data->meta->metadescription : "",

				"og:description" => isset($this->data->meta) ? $this->data->meta->metatitle : "",

			//	"og:image" => isset($this->data->meta) ? $this->data->meta->

				"link" => $this->data->friendlylink(true),

				"author" => $this->data->author()

		];
	}




}