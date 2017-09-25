<?php

namespace Eventjuicer\Services\ImageHandler;


use Illuminate\Database\Eloquent\Model;




class Storage
{


	protected $owner;
	protected $relative_source_path;
	protected $storage;

	protected $object_type;
	protected $object_id;
	protected $target_dir;
	
	protected $full_target_path;

	function __construct(Model $model, $relative_source_path, $overwrite)
	{
		$this->owner = $model;
		$this->relative_source_path = $relative_source_path;

		$this->storage = app('files');

		$obj = new \ReflectionClass( $model );

		$this->object_type 	= str_slug($obj->getShortName());

		$this->object_id 	= $model->getKey();

		$this->target_dir = public_path('static') . DIRECTORY_SEPARATOR . str_plural( $this->object_type ) . DIRECTORY_SEPARATOR . $this->object_id;

		$this->makeDirectory();

		//generate new filename

		$this->generateFilename($overwrite);
      
	}

	private function generateFilename($overwrite = false)
	{

		//just uploaded original filename!

		if(strpos( $this->relative_source_path, DIRECTORY_SEPARATOR) === false)
		{
			$this->full_target_path = $this->target_dir  . "/" . time() ."_". md5( $this->relative_source_path  ) . ".jpg";

			return;
		}


		if($overwrite)
        {
            $this->full_target_path = self::getFullPath($this->relative_source_path);
        }
        else
        {
            $filename = pathinfo($this->relative_source_path, PATHINFO_BASENAME);  //PATHINFO_FILENAME ---> must add extension below!!!!
            $this->full_target_path = $this->target_dir  . "/" . time() . "_" . $filename;
        }
	
	}


	private function makeDirectory()
	{
  
        if(!$this->storage->isDirectory($this->target_dir))
        {

        	$parentDir = realpath(dirname($this->target_dir));

        	if(!$this->storage->isWritable(  $parentDir  ))
        	{
        		throw new \Exception($parentDir . " is not writable!");
        	}
        	
        	$this->storage->makeDirectory($this->target_dir, 0775, true);
        }
        
	}


	function __toString()
	{
		return (string) $this->full_target_path;
	}


 	public function getFullDir()
	{
		return $this->target_dir;
	}

	static public function getRelativePath( $filepath )
	{
		return str_replace(public_path(), "", $filepath);

	}

	static public function getFullPath($relative_path)
	{
		return public_path() . $relative_path;
	}




}