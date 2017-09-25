<?php

namespace Eventjuicer\Services;


//https://developers.facebook.com/docs/graph-api/reference/v2.2/insights


class Insights
{
	private $fb;

	function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$this->fb = $fb;

		

		$request = new FacebookRequest(
		$session,
		'GET',
		'/{object-id}/insights/{metric-name}'
		);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();


	}



}