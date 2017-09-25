<?php 


namespace Eventjuicer\Services\Context;



use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use Auth;

use Eventjuicer\User as UserModel;
use Eventjuicer\UserSetting;


class User {


	
	private $request;
	private $config;



	protected $settings;

	private $apps = [];

	

	function __construct(Request $request, array $config)
	{
		$this->request = $request;

		$this->config = $config;

		$this->settings = $this->id() ? UserSetting::where("user_id", $this->id())->get() : new Collection;
	}

	public function id()
	{
		return Auth::id();
	}

	public function user()
	{
		return UserModel::find($this->id());
	}


	public function accounts()
	{
		return $this->id() ? Auth::user()->organizations : null;
	}


	public function account_ids()
	{
		return count($this->accounts()) ? $this->accounts()->pluck("id")->toArray() : [];
	}


	public function account_names()
	{
		return count($this->accounts()) ? $this->accounts()->pluck("account")->toArray() : [];
	}


	

	public function account_default()
	{

		$account = "";

		if(count($this->accounts())===1)
		{

			$account = $this->accounts()->first()->account;
		}


		return $account;

	}


	public function setting($key, $data = "")
	{

		$hash = md5(str_slug(trim($key)));

		$setting = $this->settings->where("hash", $hash);

		if(empty($data) && $setting)
		{
			return $setting->data;
		}

	}






}