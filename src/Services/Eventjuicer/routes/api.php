<?php



if(\App::environment('local'))
{
    $tld = config("app.local_environment_suffix");
}



Route::group(

[
	'namespace'=>'Controllers', 'prefix' => 'api', 'middleware' => ['api']
], function() use ($tld) {



Route::group(['domain'=>'eventjuicer.com'. $tld], function()
{
	

	Route::auth();

 	Route::group(['namespace'=>'Superadmin', 'prefix' => 'superadmin', 'middleware' => ['auth', "acl"], "is" => "superadministrator" ], function ()
	{
		Route::get('/', "OrganizerController@index");
		Route::resource('organizers', "OrganizerController");
	});


 	Route::get("admin", function($host = "")
	{	
		die("Wait! You cannot admin this host!");
	});

	

	Route::get('tour', 		'WelcomeController@tour');
	Route::get('pricing', 	'WelcomeController@pricing');
	Route::get('contact', 	'WelcomeController@contact');
	//Route::get('/', 		'WelcomeController@index');

	Route::get('/', 		function(){

		return "not yet ready :/";

	});

});






//Route::group(['domain' => '{account}.eventjuicer.com' . $tld, 'middleware' => ['context','guest']], function()


Route::group(['domain' => 'api.eventjuicer.com' . $tld, 'middleware' => ['api']], function()
{

	//TO BE DONE

});






//Route::group(['domain' => '{account}.eventjuicer.com' . $tld, 'middleware' => ['context','guest']], function()

Route::group(['domain' => '{account}.eventjuicer.com' . $tld, 'middleware' => []], function()
{

/*

	Route::group(['namespace'=>'Admin', 'prefix' => 'admin', 'middleware' => ['auth', "permissions:admin"] ], function ()
	{



*/

	

 	//, 'is' => 'administrator' 'can' => costam

	Route::group(['namespace'=>'Admin', 'prefix' => 'admin', 'middleware' => [] ], function ()
	{






		Route::group(["prefix"=> 'sender'], function()
		{
		    // allegro@polak20.pl Biedronka1


			Route::get("/", 							"SenderDashboardController@index");

			Route::get('emails/optins', 				'SenderEmailController@optins');
			Route::get('emails/optouts', 				'SenderEmailController@optouts');
			Route::get("search", 						"SenderEmailController@search");


			Route::get('campaigns/{id}/test', 			'SenderCampaignDeliveryController@test');
			Route::get('campaigns/{id}/hold', 			'SenderCampaignDeliveryController@hold');
			Route::get('campaigns/{id}/start', 			'SenderCampaignDeliveryController@start');


			Route::resource('emails', 					'SenderEmailController');

			Route::resource('imports', 					'SenderImportController');

			Route::resource('newsletters', 				'SenderNewsletterController');

			Route::resource('newsletters.images', 		'SenderNewsletterImageController');


			Route::resource('campaigns', 				'SenderCampaignController');

			Route::resource('campaigns.deliveries', 		'SenderCampaignDeliveryController');



		});






		/**
		Admin can read and edit 
		=============================
		1. all regs (free) + all (paid)
		2. all posts 
		3. organizer-level settings
		4. organizer-level texts 
		5. list of contexts
		6. media
		**/

		Route::resource("images", 			"ImageController");
		Route::resource("comments", 		"CommentController");
		Route::resource("participants", "ParticipantController");
		Route::resource("purchases", 	"PurchaseController");
	




		Route::group(['middleware' => []], function()
		{

			Route::resource("eventgroups", 	"EventGroupController");
			Route::resource("portals", 		"PortalController");

		//	Route::resource("settings", 	"SettingController");
			Route::resource("texts", 		"TextController");
			Route::resource("contexts", 	"ContextController");
			Route::resource("users", 		"UserController");

			Route::get("organizer", 		"OrganizerController@show");
			Route::get("organizer/edit", 	"OrganizerController@edit");
			Route::put("organizer", 		"OrganizerController@update");

		});



		Route::get('/', "DashboardController@index");







		Route::group(['prefix' => '{project}'], function ()
		{	
			Route::get('/', "DashboardDispatcherController@index");

		});



		Route::group(['prefix' => 'me', 'namespace'=>'Me'], function()
		{

			Route::get('/', "DashboardController@index");

		});

		
		Route::group(['prefix' => '{project}', 'namespace'=>'Group'], function ()
		{
			//Route::get('/', 'GroupController@index');
			Route::resource('purchases', "PurchasesController");
			Route::resource('participants', "ParticipantsController");


			Route::group(['prefix' => '{event_id}', 'namespace'=>'Event'], function ()
			{
			
				Route::resource('tickets', "TicketsController");
				Route::resource('purchases', "PurchasesController");
				Route::resource('participants', "ParticipantsController");
				Route::resource('settings', "SettingsController");
				Route::resource('texts', "TextsController");
			//	Route::resource('widgets', "WidgetsController");
			//	Route::resource('domains', "WidgetsController");


				//TBC Route::resource('preview', "PreviewController");


				Route::get('/', "DashboardController@index");

			});


		});

		
	

	

	});

	Route::get('/', "Admin\PublicController@index");

	//Route::get("/", function($account)
	//{
	//	return \Redirect::to("admin");
	//});
	

});







});





