<?php

namespace Eventjuicer\Services\Notifiers\Drivers;





//https://github.com/maknz/slack
//https://api.slack.com/docs/message-formatting#message_formatting



use Contracts\JobNotifier;

use Maknz\Slack\Client;

use Contracts\Context;
use Contracts\Setting;


class Slack implements JobNotifier
{


	protected $client;
	protected $context;
	protected $settings;

	function __construct(Context $context, Setting $settings)
	{

		$this->context = $context;
		$this->settings = $settings;		

		//do we have a context???

		$config = [
			'username' => 'editorapp',
			'channel' => '#redakcja',
			'allow_markdown	' => true,
			'link_names' => true
		];

        //$this->client =  new Client($this->settings->get("slack.endpoint"), $config);

        $this->client =  new Client("https://hooks.slack.com/services/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", $config);
	}



	function __call($method_name, $args)
	{
		return call_user_func_array(array($this->client, $method_name), $args);
	}


	function setFrom()
	{

	}

	function setTo()
	{

	}


/*



$config = [


  'endpoint' => '',

 

  'channel' => '#dev',


  'username' => 'Robot',



  'icon' => null,



  'link_names' => false,


  'unfurl_links' => false,


  'unfurl_media' => true,


  'allow_markdown' => true,


  'markdown_in_attachments' => [],

  // Allow Markdown in just the text and title fields
  // 'markdown_in_attachments' => ['text', 'title']

  // Allow Markdown in all fields
  // 'markdown_in_attachments' => ['pretext', 'text', 'title', 'fields', 'fallback']

];

*/


}

