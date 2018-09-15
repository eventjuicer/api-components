<?php namespace Eventjuicer\Services\Pdf;

use setasign\Fpdi\TcpdfFpdi;

class PdfLabel
{
	
	protected $tcpdf;

	protected $from_left 	= 5;
	protected $from_top 	= 5;

	protected $center 		= 60 / 2;
	protected $middle 		= 100 / 2;

	protected $first_row 	= 0;
	protected $second_row 	= 17;
	protected $third_row 	= 30;
	protected $fourth_row 	= 52;

	protected $boxwidth 	= 60;

	protected $barcodeStyle = array(
		'border' => 0,
		'vpadding' => 0,
		'hpadding' => 0,
		'fgcolor' => array(0,0,0),
		'bgcolor' => array(255,255,255), 
		'module_width' => 1, 
		'module_height' => 1 
	);


	function __construct()
	{

		if(!defined("K_PATH_FONTS"))
		{
			define("K_PATH_FONTS", app()->basePath("resources/fonts/"));
		}


		$pdf = new TCPDF("P", "mm", array(99,60), true, 'UTF-8', false);  
		
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


	protected function addTextBox($data = [], $bold = false)
	{
		extract($data);

		$this->pdf->setXY($this->from_left + $left, $this->from_top + $top, true);	
		$this->pdf->SetFont($bold ? 'freesansb' : 'freesans', '', $fontSize);	
		$this->pdf->Cell($this->boxwidth, 0, $text, 0, 1, "C", false, "", $bold ? 2 : 1); //OR 1

		return $this;
	}


	public function addPage($template = "")
	{

		$this->pdf->addPage();

		return $this;
	}

	public function rotate()
	{

		$this->pdf->StartTransform();
		$this->pdf->Rotate(90, 50, 50);

		return $this;
		
	}

	public function unRotate()
	{
		$this->pdf->StopTransform();

		return $this;
	}

	public function make($data = [])
	{


		extract($data);

		$this->addBarcode($code, array(
				"left" => $this->center - 15, 
				"top" => 0))

		->rotate()


		->addTextBox([
			"left" 		=> 0, 
			"top" 		=> $this->first_row,
			"fontSize" 	=> 40,
			"bold"		=> true,
			"text" 		=> $first,
		], true)

		->addTextBox([
			"left" 		=> 0, 
			"top" 		=> $this->second_row,
			"fontSize" 	=> 30,
			"bold"		=> false,
			"text" 		=> $second,
		])

		->addTextBox([
			"left" 		=> 0, 
			"top" 		=> $this->third_row,
			"fontSize" 	=> 35,
			"bold"		=> false,
			"text" 		=> $third,
		], true)


		->unRotate();

		
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

	public function save($filename)
	{
		

		$this->pdf->Output($filename, 'F');

		return $filename;

		//return "https://api.eventjuicer.com".str_replace(app()->basePath("public"), "", $path);
	}


	private function addBarcode($code, $data = [])
	{
		
		extract($data);
		
		$this->pdf->write2DBarcode($code, 'QRCODE,Q', 
			$this->from_left + $left,  
			$this->from_top + $top, 
			25, 
			25, 
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


