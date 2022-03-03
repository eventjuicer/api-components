<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Services\Repository;

use Eventjuicer\Post;
use Eventjuicer\PostImage;



class ImageRepository extends Repository
{
	


    public function model()
    {
        return PostImage::class;
    }

	function byId($image_id = 0)
	{
		return $this->findBy("id", $image_id);
	}


	function byPostId($post_id = 0)
	{
		return Post::find($post_id)->images;
	}




}