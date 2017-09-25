<?php namespace Eventjuicer\Services\Todoist;


use Socialite;


class Api {
	
	protected $token;

	/*
	A special string, used to allow the client to perform incremental sync. Pass * to retrieve all active resource data. More details about this below.
	*/
	protected $sync_token = "*";
	
	/*
	Used to specify what resources to fetch from the server. It should be a JSON-encoded array of strings. Here is a list of avaialbe resource types: labels, projects,items, notes, filters, reminders, locations, user, live_notifications, collaborators, notification_settings. You may use all to include all the resource types.
	*/

	protected $resource_types = [];


	protected $oauth;

	function __construct()
	{
		//$this->oauth = $oauth;


		Socialite::with('bitly')->redirect();

	}

/*

$clientId = "secret";
$clientSecret = "secret";
$redirectUrl = "http://yourdomain.com/api/redirect";
$additionalProviderConfig = ['site' => 'meta.stackoverflow.com'];
$config = new \SocialiteProviders\Manager\Config($clientId, $clientSecret, $redirectUrl, $additionalProviderConfig);
return Socialite::with('bitly')->setConfig($config)->redirect();

User {#437 ▼
  +token: "sssss"
  +refreshToken: null
  +expiresIn: null
  +id: 1048258
  +nickname: "adam zygadlewicz"
  +name: "adam zygadlewicz"
  +email: "adam.zygadlewicz@gmail.com"
  +avatar: "0d9c1f616a57fabc90b99f324a2f8334"
  +user: array:40 [▼
    "restriction" => 3
    "start_page" => "_info_page"
    "features" => []
    "avatar_small" => "https://dcff1xvirvpfp.cloudfront.net/0d9c1f616a57fabc90b99f324a2f8334_small.jpg"
    "completed_today" => 0
    "is_premium" => true
    "sort_order" => 0
    "full_name" => "adam zygadlewicz"
    "auto_reminder" => 30
    "timezone" => "Europe/Warsaw"
    "avatar_s640" => "https://dcff1xvirvpfp.cloudfront.net/0d9c1f616a57fabc90b99f324a2f8334_s640.jpg"
    "join_date" => "Fri 08 Nov 2013 19:04:49 +0000"
    "id" => 1048258
    "share_limit" => 51
    "team_inbox" => 153006693
    "next_week" => 1
    "completed_count" => 41
    "tz_offset" => array:4 [▶]
    "theme" => 1
    "avatar_medium" => "https://dcff1xvirvpfp.cloudfront.net/0d9c1f616a57fabc90b99f324a2f8334_medium.jpg"
    "email" => "adam.zygadlewicz@gmail.com"
    "start_day" => 1
    "avatar_big" => "https://dcff1xvirvpfp.cloudfront.net/0d9c1f616a57fabc90b99f324a2f8334_big.jpg"
    "date_format" => 0
    "inbox_project" => 110233142
    "time_format" => 0
    "image_id" => "0d9c1f616a57fabc90b99f324a2f8334"
    "beta" => 0
    "karma_trend" => "-"
    "business_account_id" => 21118
    "mobile_number" => "+48605501601"
    "mobile_host" => null
    "has_push_reminders" => true
    "is_dummy" => 0
    "premium_until" => null
    "guide_mode" => false
    "token" => "5fb616290d95ad58e7368dc30e863cb42b9a7d2e"
    "karma" => 50.0
    "is_biz_admin" => true
    "default_reminder" => "push"
  ]
}

*/

}



