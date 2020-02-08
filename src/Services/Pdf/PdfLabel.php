<?php namespace Eventjuicer\Services\Pdf;

use TCPDF;

class PdfLabel
{
	
	protected $tcpdf;

	protected $from_left 	= 5;
	protected $from_top 	= 5;

	protected $center 		= 60 / 2;
	protected $middle 		= 100 / 2;

	protected $first_row 	= 0;
	protected $second_row 	= 15;
	protected $third_row 	= 26;
	protected $fourth_row 	= 41;
	protected $ribbon 		= 41;

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
		
		$pdf->SetCreator("eventjuicer.com");
		$pdf->SetAuthor("eventjuicer.com");	
		$pdf->SetTitle('eventjuicer.com');
		$pdf->SetSubject('eventjuicer.com');
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

	protected function blackOnWhite(){
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->SetFillColor(255,255,255);
	}

	protected function whiteOnBlack(){
		$this->pdf->SetTextColor(255,255,255);
		$this->pdf->SetFillColor(0,0,0);
	}

/**
 4976:      * Prints a cell (rectangular area) with optional borders, background color and character string. The upper-left corner of the cell corresponds to the current position. The text can be aligned or centered. After the call, the current position moves to the right or to the next line. It is possible to put a link on the text.<br />
 4977:      * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
 4978:      * @param $w (float) Cell width. If 0, the cell extends up to the right margin.
 4979:      * @param $h (float) Cell height. Default value: 0.
 4980:      * @param $txt (string) String to print. Default value: empty string.
 4981:      * @param $border (mixed) Indicates if borders must be drawn around the cell. The value can be a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul> or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul> or an array of line styles for each border group - for example: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)))
 4982:      * @param $ln (int) Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL languages)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul> Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
 4983:      * @param $align (string) Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
 4984:      * @param $fill (boolean) Indicates if the cell background must be painted (true) or transparent (false).
 4985:      * @param $link (mixed) URL or identifier returned by AddLink().
 4986:      * @param $stretch (int) font stretch mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if text is larger than cell width</li><li>2 = forced horizontal scaling to fit cell width</li><li>3 = character spacing only if text is larger than cell width</li><li>4 = forced character spacing to fit cell width</li></ul> General font stretching and scaling values will be preserved when possible.
 4987:      * @param $ignore_min_height (boolean) if true ignore automatic minimum height value.
 4988:      * @param $calign (string) cell vertical alignment relative to the specified Y value. Possible values are:<ul><li>T : cell top</li><li>C : center</li><li>B : cell bottom</li><li>A : font top</li><li>L : font baseline</li><li>D : font bottom</li></ul>
 4989:      * @param $valign (string) text vertical alignment inside the cell. Possible values are:<ul><li>T : top</li><li>C : center</li><li>B : bottom</li></ul>
 4990:      * @public
 4991:      * @since 1.0
 4992:      * @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), AddLink(), Ln(), MultiCell(), Write(), SetAutoPageBreak()
 4993:      */


	protected function addTextBox(array $data){

		$left = array_get($data, "left", 0);
		$top = array_get($data, "top", 0);
		$fontSize = array_get($data, "fontSize", 20);
		$bold = array_get($data, "bold", false);
		$text = array_get($data, "text", "");
		$stretch = array_get($data, "stretch", 1);

		$this->blackOnWhite();
	
		$this->pdf->setXY($this->from_left + $left, $this->from_top + $top, true);	
		$this->pdf->SetFont($bold ? 'freesansb' : 'freesans', '', $fontSize);	
		$this->pdf->Cell($this->boxwidth, 0, $text, 0, 1, "C", false, "", $stretch ? 2 : 1); 

		return $this;
	}


	protected function addRibbon(string $text, int $fontSize = 20)
	{
		
		$this->whiteOnBlack();
	
		$this->pdf->setXY($this->from_left + 1, $this->from_top + $this->ribbon, true);	
		$this->pdf->SetFont('freesansb', '', $fontSize);	
		$this->pdf->Cell(35, 0, $text, 0, 1, "C", true, "", 2); 

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

	public function unRotate(){
		$this->pdf->StopTransform();

		return $this;
	}

	public function make(array $data)
	{

		$first = array_get($data, "first", "");
		$second = array_get($data, "second", "");
		$third = array_get($data, "third", "");
		$ribbon = array_get($data, "ribbon", "");
		$code = array_get($data, "code", "");

		$this->addBarcode($code, array(
				"left" => $this->center - 15, 
				"top" => 0));

		$this->rotate();

		if($first){
			$this->addTextBox([
				"left" 		=> 0, 
				"top" 		=> $this->first_row,
				"fontSize" 	=> 40,
				"bold"		=> true,
				"text" 		=> $first,
				"stretch" 	=> true
			]);
		}
	
		if($second){
			$this->addTextBox([
				"left" 		=> 0, 
				"top" 		=> $this->second_row,
				"fontSize" 	=> 30,
				"bold"		=> false,
				"text" 		=> $second,
			]);
		}
		
		if($third){
			$this->addTextBox([
				"left" 		=> 0, 
				"top" 		=> $this->third_row,
				"fontSize" 	=> 35,
				"bold"		=> false,
				"text" 		=> $third,
			]);
		}


		if($ribbon){

			$this->addRibbon(strtoupper($ribbon));

		}

		$this->unRotate();

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


