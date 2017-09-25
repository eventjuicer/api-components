<?php

namespace Eventjuicer\Services\View\Widgets;

use App;
use View;
use Eventjuicer\Repositories\PortalPosts;



use Eventjuicer\Services\View\Parsers\AbstractParser;

class Posts extends AbstractParser {


	//use Datasource;

	protected $repo;

	static $reparse = true;

	function resolve()
	{

	}

	function htmlize()
	{

		$this->repo = App::make(PortalPosts::class);

		$this->posts = $this->repo->tagged($this->getAttribute("name"));

		return (string) View::make('widgets.posts', ['posts' => $this->posts, "headline" => $this->getAttribute("title")]);

	}


}