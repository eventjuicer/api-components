<?php 

namespace Eventjuicer\Services;


use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
//use Endroid\QrCode\Response\QrCodeResponse;


class QrCodeGenerator {
	
	function __construct()
	{
		
	}


	public function generate($code, $text = "")
	{

		$qrCode = new QrCode($code);
		$qrCode->setSize(500);


				// Set advanced options
		$qrCode->setWriterByName('png');
		$qrCode->setMargin(20);
		$qrCode->setEncoding('UTF-8');
		$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM);
		$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
		$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);

		$qrCode->setLabel($text, 13, app()->basePath("resources/fonts/Roboto-Regular.ttf"), LabelAlignment::CENTER);
		//$qrCode->setLogoPath(__DIR__.'/../assets/images/symfony.png');
		//$qrCode->setLogoWidth(150);
		$qrCode->setRoundBlockSize(true);
		$qrCode->setValidateResult(false);


		return response(
			$qrCode->writeString(),
			200,
			['Content-Type' => $qrCode->getContentType() ]
		);


		// Save it to a file (guesses writer by file extension)
		//$qrCode->writeFile(__DIR__.'/qrcode.png');

		
	}

}