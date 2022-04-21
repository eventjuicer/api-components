<?php

namespace Eventjuicer\Services\Pdf;
use setasign\Fpdi\TcpdfFpdi as FPDI;



class PdfTicket
{
	

	protected $tcpdf;


	protected $from_left 	= 10;
	protected $from_top 	= 10;

	protected $center 		= 210 / 2;
	protected $middle 		= 297 / 2;

	protected $first_row 	= 36;
	protected $second_row 	= 53;
	protected $third_row 	= 69;
	protected $fourth_row 	= 82;

	protected $boxwidth 	= 0;

	protected $barcodeStyle = array(
		'border' => 0,
		'vpadding' => 'auto',
		'hpadding' => 'auto',
		'fgcolor' => array(0,0,0),
		'bgcolor' => array(255,255,255), 
		'module_width' => 1, 
		'module_height' => 1 
	);

	protected $formats = [

		"A4" => ["w" => 210, "h" => 297]
	];


	function __construct()
	{

		define("K_PATH_FONTS", app()->basePath("resources/fonts/"));

		$this->boxwidth = $this->center - 2 * $this->from_left;

		$pdf = new FPDI("P", "mm", "A4", true, 'UTF-8', false);  
		

		$pdf->SetCreator("eventjuicer.com ltd");
		$pdf->SetAuthor("eventjuicer.com ltd");	
		$pdf->SetTitle('Targi eHandlu / eCommerce Poland');
		$pdf->SetSubject('Targi eHandlu / eCommerce Poland');
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->setLanguageArray([
			'a_meta_charset' => 'UTF-8',
			'a_meta_dir' => 'ltr',
			'a_meta_language' => 'pl',
			'w_page' => 'strona']);

		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);

		$pdf->setFontSubsetting(false);	
		$pdf->SetMargins(0, 0, 0, true);
		
		$pdf->SetAutoPageBreak(false);
		$pdf->SetFont('freesans', '', 25);	
		$pdf->SetTextColor(0,0,0);
		
		$this->pdf = $pdf;
	}


	protected function addTextBox($data = [])
	{
		extract($data);

		$this->pdf->setXY($this->from_left + $left, $this->from_top + $top, true);	
		$this->pdf->SetFont('freesansb', '', $fontSize);	
		$this->pdf->Cell($this->boxwidth, 0, $text, 0, 1, "C", false, "", $bold ? 2 : 1); //OR 1

		return $this;
	}


	public function addPage($template = "")
	{

		$this->pdf->addPage();

		if(!empty($template))
		{
			$pagecount 	= $this->pdf->setSourceFile($template); 
			$tplidx 	= $this->pdf->importPage(1, '/MediaBox');
		
		//	$tmplSize = $this->pdf->getTemplateSize($tplidx);

/*

array(5) {
  ["width"]=>
  float(210.25555555556)
  ["height"]=>
  float(297.03888888889)
  [0]=>
  float(210.25555555556)
  [1]=>
  float(297.03888888889)
  ["orientation"]=>
  string(1) "P"
}

*/

			//$this->pdf->useTemplate($tplidx, null, null, 0, 0, true);

			$this->pdf->useTemplate($tplidx, 0, 0, null, null, true);
		}

		return $this;
	}


	public function addTicket($data = [])
	{

		extract($data);

		$this->addTextBox([
			"left" 		=> 0, 
			"top" 		=> $this->first_row,
			"fontSize" 	=> 40,
			"bold"		=> true,
			"text" 		=> $first,
		])

		->addTextBox([
			"left" 		=> 0, 
			"top" 		=> $this->second_row,
			"fontSize" 	=> 35,
			"bold"		=> false,
			"text" 		=> $second,
		])

		->addTextBox([
			"left" 		=> 0, 
			"top" 		=> $this->third_row,
			"fontSize" 	=> 30,
			"bold"		=> false,
			"text" 		=> $third,
		])

		->addBarcode($code, array(
				"left" => $this->center - 60, 
				"top" => $this->fourth_row))

		->addTextBox([
			"left" 		=> $this->center, 
			"top" 		=> $this->first_row,
			"fontSize" 	=> 40,
			"bold"		=> true,
			"text" 		=> $first,
		])

		->addTextBox([
			"left" 		=> $this->center, 
			"top" 		=> $this->second_row,
			"fontSize" 	=> 35,
			"bold"		=> false,
			"text" 		=> $second,
		])

		->addTextBox([
			"left" 		=> $this->center, 
			"top" 		=> $this->third_row,
			"fontSize" 	=> 30,
			"bold"		=> false,
			"text" 		=> $third,
		])
		
		->addBarcode($code, array(
				"left" => $this->center + 40, 
				"top" => $this->fourth_row));
	
	
		return $this;
	
	}

	public function download($filename)
	{
		$this->pdf->Output($filename, 'D');
	}


	public function inline($filename)
	{
		$this->pdf->Output($filename, 'I');
	}


	private function addBarcode($code, $data = [])
	{
		
		extract($data);
		
		$this->pdf->write2DBarcode($code, 'QRCODE,Q', 
			$this->from_left + $left,  
			$this->from_top + $top, 
			50, 
			50, 
			$this->barcodeStyle, 'N');

		return $this;
		
	}




	private function addShit()
	{
		if(!empty($faq))
 		{
			
			$pdf->SetFont('freesans', '', 8);	
			
			//$pdf->MultiCell($boxwidth,  0, "");

			$pdf->writeHTMLCell($boxwidth,  0, $from_left , $from_top + $middle, $faq);

 		}


		if(!empty($agenda))
		{

			$pdf->addPage();		
			
			$pdf->setXY($from_left, $from_top, true);		
			
			$pdf->SetFont('freesansb', '', 20);	
			
			$pdf->Cell($boxwidth, 0, "Agenda", 0, 1, "L", false, "", 1);

			$pdf->SetFont('freesans', '', 9);	

			$pdf->writeHTMLCell($boxwidth * 2 ,  0, $from_left , $from_top + 9, $agenda);

		}


		if(!empty($exhibitors))
		{

			$pdf->addPage();		
			
			$pdf->setXY($from_left, $from_top, true);		
			
			$pdf->SetFont('freesansb', '', 20);	
			
			$pdf->Cell($boxwidth, 0, "Wystawcy", 0, 1, "L", false, "", 1);

			$pdf->SetFont('freesans', '', 9);	

			//$pdf->writeHTMLCell($boxwidth * 2 ,  0, $from_left , $from_top + 9, $agenda);

		}
	}



	
}


