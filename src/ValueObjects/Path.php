<?php

namespace ValueObjects;

use Storage;

class Path {

	protected $path;

	function __construct($path)
	{
		$this->path = trim($path);
	}

	public function isValid()
	{
		//must exists in DB
	}

	function __toString()
	{
		return (string) $this->path;
	}


	private function getFileUrl($key)
    {
    
        $s3 = Storage::disk('s3');
    
        $client = $s3->getDriver()->getAdapter()->getClient();
    
        $bucket = Config::get('filesystems.disks.s3.bucket');

        $command = $client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key
        ]);

        $request = $client->createPresignedRequest($command, '+30 minutes');

        return (string) $request->getUri();
    }


    public function dimensions()
    {

        $imageable = $this->app->make('Imageable');

        return $imageable->dimensions($this->path);
    }



}