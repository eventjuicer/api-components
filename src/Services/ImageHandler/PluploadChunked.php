<?php


namespace Eventjuicer\Services\ImageHandler;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use Eventjuicer\Services\ImageHandler\Storage;



class PluploadChunked
{



    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $storage;

    private $maxFileAge = 600; //600 secondes

    protected $target_dir;

    public function __construct(Request $request, $model)
    {
        $this->request = $request;
        $this->storage = app('files');

        $this->target_dir = (string) new Storage($model);
    }

   

    /**
     * Process uploaded files.
     * 
     * @param string  $name
     * @param closure $closure
     * 
     * @return array
     */
    public function process($name, Closure $closure)
    {

        if(empty($name))
        {
            $name = "file";
        }


        if(!$this->request->hasFile($name))
        {
            //throw new \Exception("Cannot find file!");

            //SKIP closure!
            return false;
        }

        if ($this->hasChunks())
        {
            return $this->chunks($name, $closure);
        }
        else
        {
           
           return $closure( $this->doFileUpload( $this->request->file($name) ) );
        }

    }


    private function doFileUpload($file)
    {
        $target_filename = md5( time() . $file->getClientOriginalName() ) .".". $file->guessExtension();

        $file->move($this->target_dir, $target_filename);

        return $this->target_dir . "/" . $target_filename;  
    }














    /**
     * Get chuck upload path.
     * 
     * @return string
     */
    public function getChunkPath()
    {
        $path = storage_path('upload_chunks');

        if (!$this->storage->isDirectory($path))
        {
            $this->storage->makeDirectory($path, 0777, true);
        }

        return $path;
    }



    /**
     * Handle single uploaded file.
     * 
     * @param string  $name
     * @param closure $closure
     * 
     * @return mixed
     */
    public function chunks($name, Closure $closure)
    {
        $result = false;

  
        $file = $this->request->file($name);

        $chunk = (int) $this->request->get('chunk', false);
        $chunks = (int) $this->request->get('chunks', false);
        $originalName = $this->request->get('name');

        $filePath = $this->getChunkPath().'/'.$originalName.'.part';

        $this->removeOldData($filePath);
        $this->appendData($filePath, $file);

        if ($chunk == $chunks - 1) {
            $file = new UploadedFile($filePath, $originalName, 'blob', count($filePath), UPLOAD_ERR_OK, true);
            $result = $closure(  $this->doFileUpload($file) ) ;
            @unlink($filePath);
        }
        
        return $result;
    }

    /**
     * Remove old chunks.
     */
    protected function removeOldData($filePath)
    {
        if ($this->storage->exists($filePath) && ($this->storage->lastModified($filePath) < time() - $this->maxFileAge)) {
            $this->storage->delete($filePath);
        }
    }

    /**
     * Merge chunks.
     */
    protected function appendData($filePathPartial, UploadedFile $file)
    {
        if (!$out = @fopen($filePathPartial, 'ab')) {
            throw new Exception('Failed to open output stream.', 102);
        }

        if (!$in = @fopen($file->getPathname(), 'rb')) {
            throw new Exception('Failed to open input stream', 101);
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);
    }

    /**
     * Check if request has chunks.
     * 
     * @return bool
     */
    public function hasChunks()
    {
        return (bool) $this->request->get('chunks', false);
    }
}