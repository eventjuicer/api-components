<?php 

namespace Eventjuicer\Services\Presenter;


use Eventjuicer\Services\Presenter\Presenter;
use ArrayAccess, IteratorAggregate;

class Decorator {

    /*
     * If this variable implements Services\Presenter\PresentableInterface then turn it into a presenter.
     *
     * @param  mixed $value
     * @return mixed $value
    */
    public function decorate($value)
    {



        if ($value instanceof PresentableInterface && $value->getPresenter() instanceof Presenter)
        {
            return $value->getPresenter(); //returns Decorator instance with Eloquent model passed to the constructor....
        }


        //Collection?
        
        if (is_array($value) or ($value instanceof IteratorAggregate and $value instanceof ArrayAccess))
        {

            //if we have collection of models than we will decorate it automagically....

            foreach ($value as $k => $v)
            {
                $value[$k] = $this->decorate($v);
            }
        }

        return $value;
    }
}