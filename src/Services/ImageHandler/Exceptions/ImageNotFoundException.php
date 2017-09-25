<?php

namespace Eventjuicer\Services\ImageHandler\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageNotFoundException extends NotFoundHttpException
{
    
    public function __construct($message = null)
    {
        parent::__construct($message);
    }
}
