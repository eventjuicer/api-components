<?php


namespace Eventjuicer\Services\ImageHandler;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Image;

use Eventjuicer\Services\ImageHandler\Storage;


class Upload
{

    protected $file;
    protected $image;
    protected $full_target_path;

    public function __construct($key, array $params, $model, $overwrite)
    {

        if(empty($key))
        {
            $key = "file";
        }

        if(isset($params[$key]) AND $params[$key] instanceof UploadedFile)
        {

            $this->file = $params[$key];

            $this->full_target_path = (string) new Storage($model, $this->file->getClientOriginalName(), $overwrite);

            $this->image = Image::make($this->file);
        }
    
    }


    public function apply(Closure $func)
    {
        if($this->image)
        {
            $this->image = $func($this->image);
        }  
    }


    function __toString()
    {
        if($this->image)
        {
            $this->image->save($this->full_target_path, 85); //conversion 
            return (string) $this->full_target_path;
        }
        
        return "";
       
    }


/*  

guessExtension()
Returns the extension based on the mime type. / from File

string|null getMimeType()
Returns the mime type of the file. / from File

string  getExtension()
Returns the extension of the file. / from File

File    move(string $directory, string $name = null)
Moves the file to a new location.

string|null getClientOriginalName()
Returns the original file name.

string  getClientOriginalExtension()
Returns the original file extension

string|null getClientMimeType()
Returns the file mime type.

string|null guessClientExtension()
Returns the extension based on the client mime type.

integer|null    getClientSize()
Returns the file size.

integer getError()
Returns the upload error.

Boolean isValid()
Returns whether the file was uploaded successfully.

static int  getMaxFilesize()
Returns the maximum size of an uploaded file as configured in php.ini

*/



}