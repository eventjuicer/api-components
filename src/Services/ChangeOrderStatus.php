<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;

use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Purchase;
use Eventjuicer\Models\ParticipantTicket;

use Eventjuicer\Events\PurchaseStatusChanged;
use Eventjuicer\Events\AdminActionPerformed;
use Carbon\Carbon;

class ChangeOrderStatus {


	protected $request;
	protected $statuses = ["new","confirmed","hold","ok","cancelled"];

	function __construct(Request $request){
		$this->request = $request;
	}

	public function cancel(int $id){

		$participant = Participant::find($id);

		if(is_null($participant)){
			throw new \Exception("Participant not found");
		}

		foreach($participant->purchases as $purchase){

			$this->purchase($purchase->id, "cancelled");
		}

		return $participant->fresh();
	}

	public function owner(int $id, array $owners){

		$purchase = Purchase::find($id);


		if(!in_array($purchase->participant_id, $owners)){
			throw new \Exception("Access denied!");
		}

		return $this->purchase($id, "cancelled");
	}

	public function purchase(int $id, string $newStatus){


		$purchase = Purchase::find($id);

		if(is_null($purchase)){
			throw new \Exception("Purchase not found");
		}


		if(!in_array($newStatus, $this->statuses)){
			throw new \Exception("Bad newStatus");
		}

		if($purchase->status === $newStatus){
			return $purchase; //nothing to do!
		}

		$purchase->status = $newStatus;

		$purchase->paid = (int) ($newStatus === "ok");

		$purchase->status_source = "manual";

		$purchase->updatedon = (string) Carbon::now();

		$purchase->save();

		$purchase->fresh();


		event(new PurchaseStatusChanged($purchase, $newStatus));

		//UPDATE TICKETPIVOT

		$sold = (int) ($newStatus !== "cancelled");
		/*
			When issuing a mass update via Eloquent, the saved and updated model events will not be fired for the updated models. This is because the models are never actually retrieved when issuing a mass update.
		*/

		$tickets = ParticipantTicket::where("purchase_id", $id)->update(compact("sold"));

		//event(new AdminActionPerformed ());

        return $purchase;

	}
	

}