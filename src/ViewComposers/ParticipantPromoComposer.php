<?php namespace Eventjuicer\ViewComposers;


use Illuminate\View\View;

use Eventjuicer\Services\ParticipantPromo;

class ParticipantPromoComposer {


    protected $promo;

    public function __construct(ParticipantPromo $promo)
    {
      
        $this->promo = $promo;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
       
        $view->with("participantId", $this->promo->participantId() );

        $view->with("participant", $this->promo->participant() );
        
        $view->with("participantName", $this->promo->participantName() );

       // $view->with("promoLink", $this->promo->promoLink() );


    }




}