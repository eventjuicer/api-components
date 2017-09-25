<?php

namespace Eventjuicer\Services\Context\Exceptions;

//use RuntimeException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DomainNotFoundException extends NotFoundHttpException
{
    

    public function __construct($message = null)
    {
        parent::__construct($message);
    }

    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $model;





    /**
     * Set the affected Eloquent model.
     *
     * @param  string   $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->message = "No query results for model [{$model}].";

        return $this;
    }

    /**
     * Get the affected Eloquent model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
}
