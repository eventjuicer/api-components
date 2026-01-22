<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;

use Eventjuicer\Models\Purchase;
use Eventjuicer\Events\PurchaseDiscountChanged;
use Carbon\Carbon;

class ChangeOrderDiscount {

	protected $request;

	function __construct(Request $request){
		$this->request = $request;
	}

	public function purchase(int $id, int $discount){

		$purchase = Purchase::find($id);

		if(is_null($purchase)){
			throw new \Exception("Purchase not found");
		}

		//nothing to do!
		if(intval($purchase->discount) === 0 && (!$discount || $discount === 0 || $discount < 0)){
			return $purchase;
		}

		if($discount > $purchase->amount){
			throw new \Exception("Discount is greater than purchase amount");
		}

		$purchase->discount = $discount;

		$purchase->updatedon = (string) Carbon::now();

		$purchase->save();

		$purchase->fresh();

		event(new PurchaseDiscountChanged($purchase, $discount));

        return $purchase;

	}
	

}