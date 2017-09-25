<?php 

namespace Eventjuicer\Services\Presenter;

interface PresentableInterface {

    /**
     * Return a created presenter.
     *
     * @return Robbo\Presenter\Presenter
     */
    public function getPresenter();
}