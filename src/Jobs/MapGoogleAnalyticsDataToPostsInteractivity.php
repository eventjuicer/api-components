<?php

namespace Eventjuicer\Jobs;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


//custom

use Spatie\LaravelAnalytics\LaravelAnalytics;


class MapGoogleAnalyticsDataToPostsInteractivity extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LaravelAnalytics $ga)
    {
        //
    }
}
