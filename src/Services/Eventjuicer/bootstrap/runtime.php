<?php


if (version_compare(phpversion(), '5.5.0', '<'))
{
    die("PHP 5.5 and above required");
}



if(isset($_SERVER['REQUEST_URI']))
{
	if(preg_match_all("@^(?=/(pl|en|de|it|es)/+).*$@", $_SERVER['REQUEST_URI'], $matches) OR preg_match_all("@^/(pl|en|de)[/]?$@", $_SERVER['REQUEST_URI'], $matches))
	{
	define("LANG", $matches[1][0]);

	$_SERVER['REQUEST_URI'] = "/" . trim( str_replace(LANG, "", $_SERVER['REQUEST_URI']), "/");
	}


}





/*
* runtime settings and constants
*/

@ini_set("memory_limit", "1024M");
@ini_set('default_charset', 'UTF-8');

@ini_set('allow_url_fopen', 'On');

@ini_set('post_max_size', '512M');
@ini_set('upload_max_filesize', '512M');


/*
//http://blog.natesilva.com/post/250569350/php-sessions-timeout-too-soon-no-matter-how-you-set
@ini_set("session.cache_expire", 21600);
@ini_set("session.gc_maxlifetime", 21600);
@ini_set('session.gc_probability', 1);
@ini_set('session.gc_divisor', 100);
session_set_cookie_params(21600);
*/




libxml_use_internal_errors(true); 
//setlocale(LC_ALL, "pl_PL.utf8");
setlocale(LC_ALL, "pl_PL.utf8", "pl_PL", "pl");
setlocale(LC_NUMERIC, 'C');
setlocale(LC_MONETARY, 'pl_PL');

date_default_timezone_set("UTC");

@ini_set("magic_quotes_gpc", "Off");

ini_set('sendmail_from', '');


//this is for more precise debugging
ini_set("ignore_repeated_errors", false);

			



/******************************************************************************************************************/