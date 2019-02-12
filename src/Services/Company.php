<?php 

namespace Eventjuicer\Services;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Models\Company as CompanyEloquentModel;
use Eventjuicer\Services\CompanyData;

class Company {

	protected $modelWithCompanyId;
	protected $company;
	protected $data;

	function __construct(  ){
		
	}

	public function setDataProvider($data){
		$this->data = $data;
	}

	public function make(Model $modelWithCompanyId){

		if($this->isValid($modelWithCompanyId)){

			$this->modelWithCompanyId = $modelWithCompanyId;

			$this->company = $this->modelWithCompanyId->company;

		}else{
			//some representative without company assigned...

			$parent = $this->getParentModel($modelWithCompanyId);

			if($this->isValid($parent)){

				$this->modelWithCompanyId = $parent;

				$this->company = $this->modelWithCompanyId->company;

			}
		}

		return $this;
		
	}


	public function dataToArray() {

		//temporary
		return is_object($this->data) ? $this->data->toArray( $this->company( ) ) : false;
    }

	public function company(){
		return $this->company;
	}

	public function hasCompany(){
		return $this->isValid($this->modelWithCompanyId);
	}

	public function assignCompany($company = null){

		if($company && is_object($company) && $company instanceof CompanyEloquentModel){
			
			$this->company = $company;

			//check if we need to re-assign!

			if(isset($this->modelWithCompanyId->company_id) && $this->modelWithCompanyId->company_id != $company->id){

				$this->modelWithCompanyId->company_id = $company->id;
				$this->modelWithCompanyId->save();
				$this->modelWithCompanyId->fresh(); //reload relationships...
			}

			return true;
		}

		return false;
	}

	protected function getParentModel(Model $child){

		if(isset($child->parent_id) && $child->parent_id){

			//lets assume we do not know the relationship name (like ->parent() )
			return call_user_func( get_class($child) . "::find", $child->parent_id); 
		}

		return false;
	}

	protected function isValid($test){

		return is_object($test) && $test instanceof Model && isset($test->company_id) && $test->company instanceof CompanyEloquentModel;
	}

	public function __get($attr)
	{
		if($this->hasCompany() && isset($this->company->{$attr}) ){

			return $this->company->{$attr};

		}else if ($this->data && is_object($this->data)) {

			return $this->data->{$attr};
		}

		return null;
	}


	public function __toString(){
		return (string) $this->hasCompany() ? $this->company->id : 0;
	}


}
