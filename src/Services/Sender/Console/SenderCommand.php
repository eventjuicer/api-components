<?php 


namespace Eventjuicer\Services\Sender\Console;

use Illuminate\Console\Command;

use Eventjuicer\SenderCampaign;

use Carbon\Carbon;

class SenderCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sender:campaigns';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Handle campaigns';
	
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{


		//lookup for campaigns that are STARTED and OLDER than NOW

		foreach(SenderCampaign::with(["includes", "excludes"])->where("scheduled_at", "<", Carbon::now()->toDateTimeString() )->where("status", 1)->get() AS $campaign)
		{



		}


		 $this->info( count( SenderCampaign::with("imports")->where("scheduled_at", "<", Carbon::now()->toDateTimeString() )->where("status", 1)->get() ));
	}

	

	
}
