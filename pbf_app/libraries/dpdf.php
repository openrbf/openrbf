<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Dpdf {

    var $html;
    var $path;
    var $filename;
    var $paper_size;
    var $orientation;
	var $content = '<html><style type="text/css">@page { margin: 0.5cm 1cm 0.6cm 1cm; }body {font-family: sans-serif;margin: 0.5cm 0;text-align: justify; font-size:10.5px}#header,#footer {position: fixed;left: 0;right: 0;color: #000;font-size: 0.9em;}#header {top: -30;border-bottom: 0.1pt solid #aaa;}#footer {bottom: 0;border-top: 0.1pt solid #aaa;}#header table,#footer table {width: 100%;border-collapse: collapse;border: none;}#header td,#footer td {padding: 0;width: 50%;}hr {page-break-after: always;border: 0;}</style><body><script type="text/php">
      if ( isset($pdf) ) { 
        $font = Font_Metrics::get_font(\'helvetica\', \'normal\');
        $size = 6;
        $y = $pdf->get_height() - 17;
        $x = $pdf->get_width() - 35 - Font_Metrics::get_text_width(\'1 de 1\', $font, $size);
        $pdf->page_text($x, $y, \'{PAGE_NUM} de {PAGE_COUNT}\', $font, $size);
      } 
    </script>';

	var $table_tmpl = array ( 	'table_open' => '<table align="center" style="font-size:11px; border: 0.5pt solid black; border-collapse:collapse" width="100%">',
											'heading_row_start'   => '<tr repeat>',
											'heading_row_end'     => '</tr>',
											'heading_cell_start'  => '<td style="font-weight:bold; border: 0.5pt solid black;">',
											'heading_cell_end'    => '</td>',
											'row_start' => '<tr bgcolor="#dddddd">',
											'row_end' => '</tr>',
											'row_alt_start' => '<tr bgcolor="#ffffff">',
											'row_alt_end' => '</tr>',
											'cell_alt_start' => '<td style="border: 0.5pt solid black;">',
                    						'cell_alt_end' => '</td>',
											'cell_start' => '<td style="border: 0.5pt solid black;">',
                    						'cell_end' => '</td>',
											'table_close' => '</table>' );
												
	var $sign_tmpl = array ( 	'table_open' => '<table border="0" align="center" style="font-size:11px;" width="100%">',
												'heading_row_start'   => '<tr repeat>',
												'heading_row_end'     => '</tr>',
												'heading_cell_start'  => '<td style="bold">',
												'heading_cell_end'    => '</td>',
												'row_start' => '<tr bgcolor="#ffffff">',
												'row_end' => '</tr>',
												'row_alt_start' => '<tr bgcolor="#ffffff">',
												'row_alt_end' => '</tr>',
												'cell_alt_start' => '<td>',
												'cell_alt_end' => '</td>',
												'table_close' => '</table>' );
    
    /**
     * Constructor
     *
     * @access	public
     * @param	array	initialization parameters
     */	
    function dompdf($params = array())
    {
        $this->CI =& get_instance();
		
		$this->content .= '<div id="footer"><span style="font-size:9px;">'.$this->date_fr(mktime(0, 0, 0, date("m"), date("d"), date("y")),"Long")." ".date("Y. G:i").'</span></div>';
        
        if (count($params) > 0)
        {
            $this->initialize($params);
        }
    	
        log_message('debug', 'PDF Class Initialized');
    
    }

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */	
    function initialize($params)
	{
        $this->clear();
		if (count($params) > 0)
        {
            foreach ($params as $key => $value)
            {
                if (isset($this->$key))
                {
                    $this->$key = $value;
                }
            }
        }
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set html
	 *
	 * @access	public
	 * @return	void
	 */	
	function html($html = NULL)
	{
        $this->html = $html.'</body></html>';
	}
	
function date_fr($timestamp, $mode) {

// that function outputs dates in french.
// works with PHP3. made 06.30.98 by mose@amberlab.net
// $mode can be "Short", "long", "Long", or anything (default)
// -----------------------------------------------------------
$result = "";
$dval = date("w",$timestamp);
$nval = (int) date("d",$timestamp);
$mval = date("n",$timestamp)-1;

// Feel free to personalize arrays for your mothertongue :-)
// ---------------------------------------------------------
$day = array("dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi");
                                
$sday = array("lun","mar","mer","jeu","ven","sam","dim");

$month = array("janvier","fevrier","mars","avril",
                                "mai","juin","juillet","aout","septembre",
                                "octobre","novembre","decembre");
                                        
$smonth = array("jan","fev","mars",
                                "avr","mai","juin","juil","aout",
                                "sept","oct","nov","dec");
        
// outputs the date with caps or not, long or short
// ------------------------------------------------
switch ($mode) {
case "Long":
$result = ucfirst($day[$dval]).", $nval ".ucfirst($month[$mval]);
         break;         // Mardi 30 Juin
case "long":
        $result = "$day[$dval] $nval $month[$mval]";
         break;         // mardi 30 juin
case "Short":
$result = ucfirst($sday[$dval])." $nval ".ucfirst($smonth[$mval]);
         break; // Mar 30 Juin
default:
$result = "$sday[$dval] $nval $smonth[$mval]";
                 // mar 30 juin
}
return $result;
}
	
	// --------------------------------------------------------------------

	/**
	 * Set path
	 *
	 * @access	public
	 * @return	void
	 */	
	function folder($path)
	{
        $this->path = $path;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set path
	 *
	 * @access	public
	 * @return	void
	 */	
	function filename($filename)
	{
        $this->filename = $filename;
	}
	
	// --------------------------------------------------------------------


	/**
	 * Set paper
	 *
	 * @access	public
	 * @return	void
	 */	
	function paper($paper_size = NULL, $orientation = NULL)
	{
        $this->paper_size = $paper_size;
        $this->orientation = $orientation;
	}
	
	// additiona by AA, assessment in a need to make sure this is the best way and shit..........
	
	function set_header(){
		
		$header = utf8_decode('<p style="font-size:11px; font-weight:bold;">REPUBLIQUE DU BENIN<br>Ministère de la Santé<br>Secrétariat Général<br>Projet de Renforcement de la performance du Système de santé (PRPSS)</p>');
		
		return $header;
		
		}
	
	// and of AA shit
	
	// --------------------------------------------------------------------


	/**
	 * Create PDF
	 *
	 * @access	public
	 * @return	void
	 */	
	function create($mode = 'attach') // default could be 'download'
	{
	    
   		if (is_null($this->html)) {
			show_error("HTML is not set");
		}
	    
   		if (is_null($this->path)) {
			show_error("Path is not set");
		}
	    
   		if (is_null($this->paper_size)) {
			show_error("Paper size not set");
		}
		
		if (is_null($this->orientation)) {
			show_error("Orientation not set");
		}
	    
	    //Load the DOMPDF libary
	    require_once("dompdf/dompdf_config.inc.php");
	    
	    $dompdf = new DOMPDF();
	    $dompdf->load_html($this->html);
	    $dompdf->set_paper($this->paper_size, $this->orientation);
	    $dompdf->render();
	    
	    if($mode == 'save') {
    	    $this->CI->load->helper('file');
		    if(write_file($this->path.$this->filename, $dompdf->output())) {
		    	return $this->path.$this->filename;
		    } else {
				show_error("PDF could not be written to the path");
		    }
		} 
		elseif($mode == 'attach'){
			$dompdf->stream('my.pdf',array('Attachment'=>0));
			
			}
		else {
			
			if($dompdf->stream($this->filename)) {
				
				return TRUE;
			} else {
				show_error("PDF could not be streamed");
			}
	    }
	}
	function create_and_save($file){	
		
		if (is_null($this->html)) {
			show_error("HTML is not set");
		}
	    
   		if (is_null($this->path)) {
			show_error("Path is not set");
		}
	    
   		if (is_null($this->paper_size)) {
			show_error("Paper size not set");
		}
				
				
		require_once("dompdf/dompdf_config.inc.php");
	    
	    $dompdf = new DOMPDF();
		
	    $dompdf->load_html($this->html);
	    $dompdf->set_paper($this->paper_size, $this->orientation);
	    $dompdf->render();
		$output = $dompdf->output();
	    
		file_put_contents($file, $output);		
	}
	
	function create_and_save_form($file){	
		if (is_null($this->html)) {
			show_error("HTML is not set");
		}
	    
   		if (is_null($this->path)) {
			show_error("Path is not set");
		}
	    
   		if (is_null($this->paper_size)) {
			show_error("Paper size not set");
		}
		
		if (is_null($this->orientation)) {
			show_error("Orientation not set");
		}
	
	    require_once("dompdf/dompdf_config.inc.php");
	    
	    $dompdf = new DOMPDF();
		
	    $dompdf->load_html($this->html);
	    $dompdf->set_paper($this->paper_size, $this->orientation);
	    $dompdf->render();
		$output = $dompdf->output();
	    file_put_contents($file, $output);
	   
    	$link=base_url().str_replace("./", "", $file);
		
		echo'<br/><br/><br/><br/><p style="text-align:center"><a href="'.$link.'"><button 
			style="background: -moz-linear-gradient(center top , #78ba21, #499510) repeat scroll 0 0 transparent;
			border: 1px solid #3a7a0a;
			border-radius: 3px;
			color: #fff;
			cursor: pointer;
			font-family: Arial,sans-serif;
			font-size: 14px;
			font-weight: normal;
			height: 33px;
			line-height: 30px;
			margin-right: 10px;
			padding-bottom: 4px;
			padding-left: 2px;
			padding-top: 2px;
			text-shadow: 1px 1px 0 #0a5482;
			text-transform: uppercase;
			vertical-align: middle;
			width: 300px;" 
			text-align:center
			
			id="validate" type="button" name="Valider">Afficher le document</button></a></p>';
				 
	}
}