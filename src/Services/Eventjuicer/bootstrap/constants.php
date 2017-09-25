<?php




/***********************************************************************************************
***********************************************************************************************/

$host  =  (!empty($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost" );
$altrequest = !empty($argv) ? implode("/", array_slice($argv, 1)) : "";
$document_root = !empty($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : $altrequest;
$request = trim((!empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : $altrequest), "/");

/***********************************************************************************************
***********************************************************************************************/

define("DS", DIRECTORY_SEPARATOR);  
define("NL", "<br/>");
define("PS", PATH_SEPARATOR); 

define("ONEHOUR", 3600);
define("ONEDAY", 3600*24);
define("ONEWEEK", 3600*24*7);
define("ONEMONTH", 3600*24*30);
define("ONEYEAR", 3600*24*30*12);




define("VALID_BLADE", "/(^|\s)@[a-z]\((?P<args>[^\)]*\))\b/im");




define("VALID_DOMAIN", "@([a-z0-9\-\.]+[\.]{1}[a-z]{2,10})@imu");
//define("VALID_URL", "@((http://|ftp://|https://)[a-z0-9\-\.]+[\.]{1}[a-z]{2,10}[/]?[\p{L}0-9=~_:;,/\|!#%&\^\?\.\(\)\{\}\-\+\@]*)\b@imu");

define("VALID_URL", "/(?:^|\s)(http[s]?:\/\/(?P<domain>[a-z0-9\-\.]+\.[a-z]{2,})\/[\p{L}0-9=~_:;,\|!#%&\^\?\.\(\)\{\}\-\+\@\/]+)\b/imu");





define("VALID_IMAGEURL", "@\b((http://|ftp://|https://)[a-z0-9\-\.]+\.[a-z]{2,10}/[\p{L}0-9=~_:;,/\|!#%&\?\.\-\+\@]+\.(jpeg|jpg|png|gif))@imu");


define("NON_MARKDOWN_URL", "@([^\"(](?:http://|ftp://|https://)[a-z0-9\-\.]+[\.]{1}[a-z]{2,10}[/]?[\p{L}0-9=~_:;,/\|!#%&\^\?\.\(\)\{\}\-\+\@]*)\b@imu");



define("VALID_TAG", "/#([\p{L}0-9_\-]{2,50})/siu");
define("VALID_USERNAME", "/@([\p{L}0-9_\-]{2,50})/siu");
define("VALID_EMAIL", "/[a-z0-9_\.\-]+@[a-z0-9_\.\-]+\.[a-z]{2,8}/i");
define("VALID_FIELDNAME", "@\[\[(?P<name>[a-zA-Z0-9_\-]+)(:|)(?P<cut>\?[a-zA-Z0-9_=\-]+|)\]\]@i");



define("TEMPLATE_PLUGIN", 	"#@@([a-zA-Z0-9_\-=]+)@@#i");
define("TEMPLATE_VIEW", 	"@(?<layout>[a-z0-9]+)=(?<view>[a-z0-9_\-]+)@i");
define("TEMPLATE_WIDGET", 	"@##(?<widget>(?<subtype>[a-z0-9]+)=(?<name>[a-z0-9_\-]+)(\?(?<params>[a-z0-9=_\-;,&]+)|))##@i");
define("TEMPLATE_TEXT", 	"@\{\{([a-zA-Z0-9_\-]+)\}\}@i");
define("TEMPLATE_DYNAMIC", 	"@%%(?<name>[a-zA-Z0-9_\-]+)%%@i");
define("TEMPLATE_REFERENCE", "#\$\$(?P<module>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9_\-]+)(/(?P<method>[a-zA-Z_\-]+)|)(/(?P<transform>[a-zA-Z]+)|)\$\$#i");



define("UA", isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "UNDEFINED");
define("IP", isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "127.0.0.1");
define("SERVER", isset($_SERVER["SERVER_ADDR"]) ? ($_SERVER["SERVER_ADDR"] == IP) : false);



define("PROTOCOL", !empty($_SERVER["HTTPS"]) ? "https://" : "http://");
define("HOST", $host);
define("FULLHOST", PROTOCOL . HOST . "/");
define("BASEHOST", str_ireplace(array(".local","www."), "", HOST));
define("REQUEST", $request);
define("REFERER", isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "direct");

					



/******************************************************************************************************************/
