<?php namespace Eventjuicer\Services\Taggable;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;


use Eventjuicer\Tag;


use Contracts\Taggable AS ITaggable;



class Taggable implements ITaggable
{

	protected $request;
	protected $config;

	function __construct(Request $request, Config $config)
	{
		$this->request 	= $request;
		$this->config 	= $config["taggable"];
	}


	public function replace(Model $model, $tag = null)
	{
		$model->tags()->sync($this->get_tag_ids($tag));
	}

	function add(Model $model, $tag)
	{

		//get current tags?

		$tag_ids = $this->get_tag_ids($tag);
		
	}

	private function get_tag_ids($names)
	{
		//try to find tags in current request?
		if(empty($names))
		{
			$names = $this->request->input("names");
		}

		$tag_ids = array();

		if(!is_array($names))
		{
			$names = explode(",", $names);
		}

		foreach($names AS $name)
		{
			$name = trim($name);
			$hash = $this->hash($name);

			//check if tag exists

			$query = Tag::where(compact("hash"))->first();

			if(!$query)
			{
				$query = new Tag;
				$query->fill(compact("name", "hash"));
				$query->save();
			}

			$tag_ids[] = $query->id;

			//attach?
		}

		return $tag_ids;
	}


	private function hash($tag)
	{
		return md5( str_slug( trim($tag) ));
	}


}