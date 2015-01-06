<?php

function pdf_header($orientation = 'portrait', $logoposition = 'left', $report_title = 'This is the title', $report_subtitle = 'This is the title', $param_captions){
	
	$CI = get_instance();
	
	$CI->cezpdf->selectFont(FCPATH. 'fonts/Helvetica.afm');	
	
	$logo_image = FCPATH.'cside/images/portal/'.$CI->config->item('app_logo');
	$CI->cezpdf->setStrokeColor(0,0,0,1);
	if($orientation == 'portrait') {
		$CI->cezpdf->line(20,25,580,25);
		$CI->cezpdf->addText(50,15,8,date($CI->config->item('long_date_format'))); 
		
	$CI->cezpdf->addText(20,795,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_title')))));
	$CI->cezpdf->ezSetDy(-30);
	$CI->cezpdf->ezImage($logo_image,0,'70','none',$logoposition);
	$CI->cezpdf->addText(20,720,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_sub_title')))));
	$CI->cezpdf->ezSetDy(45);
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_title)).'</b></c:uline>',11,array('justification'=>'center'));
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_subtitle)).'</b></c:uline>',10,array('justification'=>'center'));
	$CI->cezpdf->ezSetDy(-40);
	$CI->cezpdf->setColor(0.9,0.9,0.9);
	$CI->cezpdf->filledRectangle(20,$CI->cezpdf->y,550,30);
	$CI->cezpdf->setColor(0,0,0);
	
	$param_one_pos_y = $CI->cezpdf->y + 20;
	$param_two_pos_y = $CI->cezpdf->y + 5;
	
	$CI->cezpdf->addText(30,$param_one_pos_y,9,isset($param_captions[0])?$param_captions[0]:'');
	$CI->cezpdf->addText(30,$param_two_pos_y,9,isset($param_captions[1])?$param_captions[1]:'');
	$CI->cezpdf->addText(450,$param_one_pos_y,9,isset($param_captions[2])?$param_captions[2]:'');
	$CI->cezpdf->addText(450,$param_two_pos_y,9,isset($param_captions[3])?$param_captions[3]:'');
	
	$CI->cezpdf->ezSetDy(-10);
	}
	else {
		$CI->cezpdf->line(20,25,820,25);
		$CI->cezpdf->addText(50,15,8,date($CI->config->item('long_date_format')));
		
	$CI->cezpdf->addText(20,560,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_title')))));
	$CI->cezpdf->ezSetDy(-30);
	$CI->cezpdf->ezImage($logo_image,0,'70','none',$logoposition);
	$CI->cezpdf->addText(20,470,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_sub_title')))));
	$CI->cezpdf->ezSetDy(45);
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_title)).'</b></c:uline>',11,array('justification'=>'center'));
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_subtitle)).'</b></c:uline>',10,array('justification'=>'center'));
	$CI->cezpdf->ezSetDy(-40);
	
	$CI->cezpdf->setColor(0.9,0.9,0.9);
	$CI->cezpdf->filledRectangle(20,$CI->cezpdf->y,800,30);
	$CI->cezpdf->setColor(0,0,0);
	
	$param_one_pos_y = $CI->cezpdf->y + 20;
	$param_two_pos_y = $CI->cezpdf->y + 5;
	
	$CI->cezpdf->addText(30,$param_one_pos_y,9,isset($param_captions[0])?$param_captions[0]:'');
	$CI->cezpdf->addText(30,$param_two_pos_y,9,isset($param_captions[1])?$param_captions[1]:'');
	$CI->cezpdf->addText(700,$param_one_pos_y,9,isset($param_captions[2])?$param_captions[2]:'');
	$CI->cezpdf->addText(700,$param_two_pos_y,9,isset($param_captions[3])?$param_captions[3]:'');
	
	
	$CI->cezpdf->ezSetDy(-10);
	}
	
	}

function prep_pdf($orientation = 'portrait', $logoposition = 'left', $report_title = 'This is the title', $report_subtitle = 'This is the title', $param_captions, $objct_pacement = 'all', $report_header = NULL,$report_id)
{
	$CI = get_instance();
	
	$CI->cezpdf->selectFont(FCPATH. 'fonts/Helvetica.afm');	
	
	$logo_image = FCPATH.'cside/images/portal/'.$CI->config->item('app_logo');
	
	$all = $CI->cezpdf->openObject();
	$CI->cezpdf->saveState();
	$CI->cezpdf->setStrokeColor(0,0,0,1);
	if($orientation == 'portrait') {
		$CI->cezpdf->ezStartPageNumbers(530,15,8,'','{PAGENUM} '.$CI->lang->line('report_paging_of').' {TOTALPAGENUM}',1);
		$CI->cezpdf->line(20,25,580,25);
		$CI->cezpdf->addText(50,15,8,date($CI->config->item('long_date_format')).strtoupper(utf8_decode('        Report id:').$CI->config->item('report_prefix').'_'.$report_id));
		
	if(is_null($report_header) || $report_header==''){
	$CI->cezpdf->addText(20,795,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_title')))));
	$CI->cezpdf->ezSetDy(-30);
	$CI->cezpdf->ezImage($logo_image,0,'70','none',$logoposition);
	$CI->cezpdf->addText(20,720,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_sub_title')))));
	}
	else{
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_header).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezSetDy(-45);
		}
	$CI->cezpdf->ezSetDy(45);
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_title).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_subtitle)).'</b></c:uline>',10,array('justification'=>'center'));
	$CI->cezpdf->ezSetDy(-40);
	$CI->cezpdf->setColor(0.9,0.9,0.9);
	$CI->cezpdf->filledRectangle(20,$CI->cezpdf->y,550,30);
	$CI->cezpdf->setColor(0,0,0);
	
	$param_one_pos_y = $CI->cezpdf->y + 20;
	$param_two_pos_y = $CI->cezpdf->y + 5;
	
	$CI->cezpdf->addText(30,$param_one_pos_y,9,isset($param_captions[0])?$param_captions[0]:'');
	$CI->cezpdf->addText(30,$param_two_pos_y,9,isset($param_captions[1])?$param_captions[1]:'');
	$CI->cezpdf->addText(450,$param_one_pos_y,9,isset($param_captions[2])?$param_captions[2]:'');
	$CI->cezpdf->addText(450,$param_two_pos_y,9,isset($param_captions[3])?$param_captions[3]:'');
	
	$CI->cezpdf->ezSetDy(-10);
	}
	else {
		$CI->cezpdf->ezStartPageNumbers(800,15,8,'','{PAGENUM} '.$CI->lang->line('report_paging_of').' {TOTALPAGENUM}',1);
		$CI->cezpdf->line(20,25,820,25);
		$CI->cezpdf->addText(50,15,7,date($CI->config->item('long_date_format')).strtoupper(utf8_decode('        Report id:').$CI->config->item('report_prefix').'_'.$report_id));
	if(is_null($report_header) || $report_header==''){	
	$CI->cezpdf->addText(20,560,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_title')))));
	$CI->cezpdf->ezSetDy(-30);
	$CI->cezpdf->ezImage($logo_image,0,'70','none',$logoposition);
	$CI->cezpdf->addText(20,470,10,strtoupper(utf8_decode($CI->lang->line($CI->config->item('app_sub_title')))));

	}
	else{
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_header).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezSetDy(-45);	
		}
	$CI->cezpdf->ezSetDy(45);
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_title).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_subtitle)).'</b></c:uline>',10,array('justification'=>'center'));
	$CI->cezpdf->ezSetDy(-40);
	
	$CI->cezpdf->setColor(0.9,0.9,0.9);
	$CI->cezpdf->filledRectangle(20,$CI->cezpdf->y,800,30);
	$CI->cezpdf->setColor(0,0,0);
	
	$param_one_pos_y = $CI->cezpdf->y + 20;
	$param_two_pos_y = $CI->cezpdf->y + 5;
	
	$CI->cezpdf->addText(30,$param_one_pos_y,9,isset($param_captions[0])?$param_captions[0]:'');
	$CI->cezpdf->addText(30,$param_two_pos_y,9,isset($param_captions[1])?$param_captions[1]:'');
	$CI->cezpdf->addText(700,$param_one_pos_y,9,isset($param_captions[2])?$param_captions[2]:'');
	$CI->cezpdf->addText(700,$param_two_pos_y,9,isset($param_captions[3])?$param_captions[3]:'');

	
	
	$CI->cezpdf->ezSetDy(-10);
	}
	$CI->cezpdf->restoreState();
	$CI->cezpdf->closeObject();
	$CI->cezpdf->addObject($all,$objct_pacement);
	
}

function prep_pdf_invoice_donor(
	$orientation = 'portrait', 
	$logoposition = 'left', 
	$report_title = 'This is the title', 
	$report_subtitle = 'This is the title', 
	$param_captions, 
	$objct_pacement = 'all', 
	$report_header = NULL,
	$report_logo='',
	$report_logo=''
	)
	{
	$CI = get_instance();
	
	$CI->cezpdf->selectFont(FCPATH. 'fonts/Helvetica.afm');	
	
	$logo_image = FCPATH.'cside/frontend/temp/'.$report_logo.'_med.jpg';
	
	$all = $CI->cezpdf->openObject();
	$CI->cezpdf->saveState();
	$CI->cezpdf->setStrokeColor(0,0,0,1);
	if($orientation == 'portrait') {
		$CI->cezpdf->ezStartPageNumbers(530,15,8,'','{PAGENUM} '.$CI->lang->line('report_paging_of').' {TOTALPAGENUM}',1);
		$CI->cezpdf->line(20,25,580,25);
		$CI->cezpdf->addText(50,15,8,date($CI->config->item('long_date_format')).strtoupper(utf8_decode('        Report id:').$CI->config->item('report_prefix').'_'.$report_id));
		
	if(is_null($report_header) || $report_header==''){
	$CI->cezpdf->addText(20,795,10,'');
	$CI->cezpdf->ezSetDy(-30);
	$CI->cezpdf->ezImage($logo_image,0,'70','none',$logoposition);
	$CI->cezpdf->addText(20,720,10,'');
	}
	else{
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_header).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezSetDy(-45);
		}
	$CI->cezpdf->ezSetDy(45);
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_title).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_subtitle)).'</b></c:uline>',10,array('justification'=>'center'));
	$CI->cezpdf->ezSetDy(-40);
	$CI->cezpdf->setColor(0.9,0.9,0.9);
	$CI->cezpdf->filledRectangle(20,$CI->cezpdf->y,550,30);
	$CI->cezpdf->setColor(0,0,0);
	
	$param_one_pos_y = $CI->cezpdf->y + 20;
	$param_two_pos_y = $CI->cezpdf->y + 5;
	
	$CI->cezpdf->addText(30,$param_one_pos_y,9,isset($param_captions[0])?$param_captions[0]:'');
	$CI->cezpdf->addText(30,$param_two_pos_y,9,isset($param_captions[1])?$param_captions[1]:'');
	$CI->cezpdf->addText(450,$param_one_pos_y,9,isset($param_captions[2])?$param_captions[2]:'');
	$CI->cezpdf->addText(450,$param_two_pos_y,9,isset($param_captions[3])?$param_captions[3]:'');
	
	$CI->cezpdf->ezSetDy(-10);
	}
	
	
	
	
	
	else {
		$CI->cezpdf->ezStartPageNumbers(800,15,8,'','{PAGENUM} '.$CI->lang->line('report_paging_of').' {TOTALPAGENUM}',1);
		$CI->cezpdf->line(20,25,820,25);
		$CI->cezpdf->addText(50,15,7,date($CI->config->item('long_date_format')).strtoupper(utf8_decode('        Report id:').$CI->config->item('report_prefix').'_'.$report_id));
	if(is_null($report_header) || $report_header==''){	
	$CI->cezpdf->addText(20,560,10,'');
	$CI->cezpdf->ezSetDy(-30);
	$CI->cezpdf->ezImage($logo_image,0,'70','none',$logoposition);
	$CI->cezpdf->addText(20,470,10,'');

	}
	else{
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_header).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezSetDy(-45);	
		}
		
	
	$CI->cezpdf->ezSetDy(100);
	$CI->cezpdf->ezText('<b>'.utf8_decode($report_title).'</b>',11,array('justification'=>'left'));
	$CI->cezpdf->ezText('<c:uline><b>'.strtoupper(utf8_decode($report_subtitle)).'</b></c:uline>',10,array('justification'=>'center'));
	$CI->cezpdf->ezSetDy(-100);
	
	$CI->cezpdf->setColor(0.9,0.9,0.9);
	$CI->cezpdf->filledRectangle(20,$CI->cezpdf->y,800,30);
	$CI->cezpdf->setColor(0,0,0);
	
	$param_one_pos_y = $CI->cezpdf->y + 20;
	$param_two_pos_y = $CI->cezpdf->y + 5;
	
	$CI->cezpdf->addText(30,$param_one_pos_y,9,isset($param_captions[0])?$param_captions[0]:'');
	$CI->cezpdf->addText(30,$param_two_pos_y,9,isset($param_captions[1])?$param_captions[1]:'');
	$CI->cezpdf->addText(700,$param_one_pos_y,9,isset($param_captions[2])?$param_captions[2]:'');
	$CI->cezpdf->addText(700,$param_two_pos_y,9,isset($param_captions[3])?$param_captions[3]:'');

	
	
	$CI->cezpdf->ezSetDy(-10);
	}
	$CI->cezpdf->restoreState();
	$CI->cezpdf->closeObject();
	$CI->cezpdf->addObject($all,$objct_pacement);
	
}








?>