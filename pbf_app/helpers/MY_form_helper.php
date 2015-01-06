<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Multi-select menu
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @param	string
 * @return	type
 */
if ( ! function_exists('form_cascaded_multiselect'))
{
	function form_cascaded_multiselect($name = '', $options = array(), $selected = array(), $extra = '')
	{
		if ( ! strpos($extra, 'multiple'))
		{
			$extra .= ' multiple="multiple"';
		}

		return form_cascaded_dropdown($name, $options, $selected, $extra);
	}
}


// --------------------------------------------------------------------

/**
 * Cascaded Drop-down Menu
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	string
 * @param	string
 * @return	string
 */
 if ( ! function_exists('form_cascaded_filetypes_dropdown'))
{
	function form_cascaded_filetypes_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
	{
		$_this = get_instance();
		
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0)
		{
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name]))
			{
				$selected = array($_POST[$name]);
			}
		}

		if ($extra != '') $extra = ' '.$extra;

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
		
		$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";
		
		//$form .= '<option value="" id="" class="">'.$_this->lang->line('app_form_dropdown_select')."</option>\n";
		
		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val) && ! empty($val))
			{
				foreach ($val as $opt_class => $opt_val)
				{
					$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
					
					if(is_array($opt_val) && ! empty($opt_val)){
						
						foreach($opt_val as $opt_val_key => $opt_val_val){
						
						$sel = (in_array($opt_val_key, $selected)) ? ' selected="selected"' : '';
						
					$form .= '<option value="'.$opt_val_key.'" id="'.$opt_val_key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val_val."</option>\n";
						}
						
						}
					else{

					$form .= '<option value="'.$key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val."</option>\n";
					}
				}
			}
			else
			{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

				$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
		}

		$form .= '</select>';

		return $form;
	}
}
 

if ( ! function_exists('form_cascaded_dropdown2'))
{
 function form_cascaded_dropdown2($name = '', $options = array(), $selected = array(), $extra = '')
 {

 

  $_this = get_instance();

  if ( ! is_array($selected))
  {
   $selected = array($selected);
  }

  // If no selected state was submitted we will attempt to set it automatically
  if (count($selected) === 0)
  {
   // If the form name appears in the $_POST array we have a winner!
   if (isset($_POST[$name]))
   {
    $selected = array($_POST[$name]);
   }
  }

  if ($extra != '') $extra = ' '.$extra;

  $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

  $form = '<select name="'.$name.'"'.$extra.$multiple.">\n";

  foreach ($options as $key => $val)
  {



   if (is_array($val) && ! empty($val))
   {

    foreach ($val as $opt_class => $opt_val)
    {
     	
     	
     $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
     	
     if(is_array($opt_val) && ! empty($opt_val)){

      foreach($opt_val as $opt_val_key => $opt_val_val){
       if(is_array($opt_val_val) && ! empty($opt_val_val)){
       foreach($opt_val_val as $opt_val_val_key => $opt_val_val_val){
       	
       if(!strpos($form,'<option value="" id="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select').'</option>') && strpos($extra, 'multiple') === FALSE){
        $form .= '<option value="" id="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select')."</option>\n";
        	
       }
       	

       //$form .= '<option value="'.abs($key).'" id="'.$opt_val_key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val_val."</option>\n";
       
       $form .= '<option value="'.abs($key).'" id="'.abs($key).'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val_val_val."</option>\n";
   //   echo $form;
    //  exit;
       }
       }
     }
     }
     else{
      	
      if(!strpos($form,'<option value="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select').'</option>') && strpos($extra, 'multiple') === FALSE){
       $form .= '<option value="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select')."</option>\n";
       	
      }

      $form .= '<option value="'.$key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val."</option>\n";
     }
    }

   }
   else
   {
    $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

    $form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
   }
  }

  $form .= '</select>';

 // echo $form;
 // exit;
  return $form;
 }
}

if ( ! function_exists('form_cascaded_dropdown'))
{
	function form_cascaded_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
	{


	 
	 $_this = get_instance();
		
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0)
		{
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name]))
			{
				$selected = array($_POST[$name]);
			}
		}

		if ($extra != '') $extra = ' '.$extra;

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
		
		$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";
		
		foreach ($options as $key => $val)
		{
		
	

			if (is_array($val) && ! empty($val))
			{
				
				foreach ($val as $opt_class => $opt_val)
				{
					
				 
				     $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
					
					if(is_array($opt_val) && ! empty($opt_val)){
						
						foreach($opt_val as $opt_val_key => $opt_val_val){
							
	if(!strpos($form,'<option value="" id="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select').'</option>') && strpos($extra, 'multiple') === FALSE){
		$form .= '<option value="" id="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select')."</option>\n";
			
						}
					
						//$form .= '<option value="'.$opt_val_key.'" id="'.$opt_val_key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val_val."</option>\n";
							
						 $form .= '<option value="'.abs($key).'" id="'.$opt_val_key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val_val."</option>\n";
						}
						
						}
					else{
					
	if(!strpos($form,'<option value="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select').'</option>') && strpos($extra, 'multiple') === FALSE){
		$form .= '<option value="" class="'.$opt_class.'">'.$_this->lang->line('app_form_dropdown_select')."</option>\n";
			
						}

					$form .= '<option value="'.$key.'" class="'.$opt_class.'"'.$sel.'>'.(string) $opt_val."</option>\n";
					}
				}

			}
			else
			{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

				$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
		}

		$form .= '</select>';

		return $form;
	}
}

if ( ! function_exists('form_cascaded_geozones'))
{
		function form_cascaded_geozones($active_geo_name, $reference=false)
		{
			
			$_this = get_instance();
			$geoleveles = $_this->pbf->get_geoleveles();
			$geozones = $_this->pbf->get_geozones();			
			$form = '';
			$ids='';
			
			$js='<script language="javascript"> function applyCascadingZones() {';
			
			foreach ($geoleveles as $level_key => $level_val){
				
				$_name='level_'.$level_key;
				$_selected = $_this->session->userdata('sel_parent_geozone_id');
				$_this->session->unset_userdata('sel_parent_geozone_id');
				
				if($level_val['geo_active']==1){
					$_name = $active_geo_name;
					$_selected = $_this->session->userdata('sel_geozone_id');
					$_this->session->unset_userdata('sel_geozone_id');
					}
				
				$form.='<p><label for="'.$_name.'">'.ucwords(strtolower($level_val['geo_title'])).':</label>';
				
				$ids .= $_name.',';
				
				$level = array();
				
				foreach($geozones as $zone_val){
					
					if($zone_val['geo_id']==$level_val['geo_id']){
						
						$level[$zone_val['geozone_id']] = array ($zone_val['geozone_parentid'] => $zone_val['geozone_name']);
						
						}
					
					}
								
				$form .= form_cascaded_dropdown($_name,
												$level,
												$_selected,
												'id="'.$_name.'"').'<span style="color:red;"></span></p>';
			}
			$ids = explode(',',rtrim($ids,','));
			
			for($i=0;$i<count($ids)-1; $i++){
				$js .= 'applyCascadingDropdown("'.$ids[$i].'", "'.$ids[$i+1].'");';
				}
			$js .= '}applyCascadingZones();</script>';
			return $form.$js;
			
		}
		
}

// ------------------------------------------------------------------------

/**
 * Hidden Input Field
 *
 * Generates hidden fields.  You can pass a simple key/value string or an associative
 * array with multiple values.
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_hidden'))
{
	function form_hidden($name, $value = '', $recursing = FALSE, $extra = '')
	{
		static $form;

		if ($recursing === FALSE)
		{
			$form = "\n";
		}

		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				form_hidden($key, $val, TRUE);
			}
			return $form;
		}

		if ( ! is_array($value))
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.form_prep($value, $name).'"'.$extra.' />'."\n";
		}
		else
		{
			foreach ($value as $k => $v)
			{
				$k = (is_int($k)) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}

		return $form;
	}
}

if ( ! function_exists('form_cascaded_class_type_filter'))
{
		function form_cascaded_class_type_filter($entity_class_name, $entity_type_name)
		{
			$_thiz = get_instance();
			
			$classes = array('' => $_thiz->lang->line('app_list_filter_entity_class')) + $_thiz->pbf->get_entity_classes();
			$entities = array('' => $_thiz->lang->line('app_list_filter_entity_type')) + $_thiz->pbf->get_entity_types();
			
			$forms = '';
			$ids='';
			
			$forms .= 
			form_cascaded_dropdown($entity_class_name, $classes ,'','id="'.$entity_class_name.'"').
			form_cascaded_dropdown($entity_type_name, $entities ,'','id="'.$entity_type_name.'"');
		
		
		return $forms.'<script language="javascript"> function applyCascadingClassType() {applyCascadingDropdown("'.$entity_class_name.'", "'.$entity_type_name.'");}applyCascadingClassType();</script>';	
		}

}

if ( ! function_exists('form_cascaded_geozones_entities_filter'))
{
	
	function form_cascaded_geozones_entities_filter($active_geo_name, $reference=false)
		{
					$codeigniter = get_instance();
					
					$entities = $codeigniter->pbf->get_entities_data_entry($codeigniter->session->userdata('data_entity_class'));


					$current_geozone="";
					$form_entities_dropdown = '<select id="entity_id" name="entity_id">';
					
					foreach ($entities as $entity){
						
					if($current_geozone!=$entity['entity_geozone_id']){
					$current_geozone=$entity['entity_geozone_id'];
					$form_entities_dropdown.='<option class="'.$entity['entity_geozone_id'].'" value="">'.$codeigniter->lang->line('app_form_dropdown_select').'</option>';
						}
						$selected=($entity['entity_id']==$codeigniter->session->userdata('filtered_entity_id'))?'selected="selected"':'';
						$form_entities_dropdown .='<option class="'.$entity['entity_geozone_id'].'" '.$selected.' value="'.$entity['entity_id'].'">'.$entity['entity_name'].'</option>';
						}
					
					$form_entities_dropdown .= '</select>';
					$codeigniter->session->unset_userdata('filtered_entity_id');
			
			return form_cascaded_geozones_filter($active_geo_name, $reference=false).$form_entities_dropdown.'<script language="javascript"> function applyCascadingZones() {applyCascadingDropdown("geozone_id", "entity_id");}applyCascadingZones();</script>';
		
		}
	
	}
	
if ( ! function_exists('form_cascaded_geozones_entities_filetype_filter'))
{
	
	function form_cascaded_geozones_entities_filetype_filter($active_geo_name, $reference=false)
		{
					$codeigniter = get_instance();
					
					$entities = $codeigniter->pbf->get_entities_data_entry($codeigniter->session->userdata('data_entity_class'));
					$current_geozone="";
					$form_entities_dropdown = '<select id="entity_id" name="entity_id">';
					$form_entities_dropdown.='<option class="" value="">'.$codeigniter->lang->line('app_list_filter_entity').'</option>';
					foreach ($entities as $entity){
						
						$selected=($entity['entity_id']==$codeigniter->session->userdata('filtered_entity_id'))?'selected="selected"':'';
						$form_entities_dropdown .='<option id="'.$entity['entity_id'].'" class="'.$entity['entity_geozone_id'].'" '.$selected.' value="'.$entity['entity_type_id'].'">'.$entity['entity_name'].'</option>';
						}
					
					$form_entities_dropdown .= '</select>';
					$codeigniter->session->unset_userdata('filtered_entity_id');
					
			$codeigniter->lang->load('files', $codeigniter->config->item('language'));		
			$filetypes=array('' => $codeigniter->lang->line('frm_filetypes_title'))+$codeigniter->pbf->get_filetypes__entity_type($codeigniter->session->userdata('data_entity_class')); 
			
			$filetypes =  form_cascaded_filetypes_dropdown('filetype_id', $filetypes, $codeigniter->session->userdata('filtered_filetype_id'), 'id="filetype_id"');
			
			$codeigniter->session->unset_userdata('filtered_filetype_id');
			
			return form_cascaded_geozones_filter($active_geo_name, $reference=false).$form_entities_dropdown.$filetypes.'<script language="javascript">$(function(){$("#entity_id").chained("#geozone_id");});$(function(){$("#filetype_id").chained("#entity_id");});</script>';
		
		}
	
	}

if ( ! function_exists('form_cascaded_geozones_filter'))
{
		function form_cascaded_geozones_filter($active_geo_name, $reference=false)
		{
			
			$_this = get_instance();
			$geoleveles = $_this->pbf->get_geoleveles();
			$geozones = $_this->pbf->get_geozones();			
			$form = '';
			$ids='';
			
			$js='<script language="javascript"> function applyCascadingZones() {';
			
			foreach ($geoleveles as $level_key => $level_val){
				
				$_name='level_'.$level_key;
				$_selected = $_this->session->userdata('level_0');
				$_this->session->unset_userdata('level_0');
				
				if($level_val['geo_active']==1){
					$_name = $active_geo_name;
					$_selected = $_this->session->userdata('filtered_geozone_id');
					$_this->session->unset_userdata('filtered_geozone_id');
					}
				
				$ids .= $_name.',';
				
				$level = array();
				
				foreach($geozones as $zone_val){
					
					if($zone_val['geo_id']==$level_val['geo_id']){
						
						$level[$zone_val['geozone_id']] = array ($zone_val['geozone_parentid'] => $zone_val['geozone_name']);
						
						}
					
					}
								
				$form .= form_cascaded_dropdown($_name,
												$level,
												$_selected,
												'id="'.$_name.'"');
			}
			$ids = explode(',',rtrim($ids,','));
			
			for($i=0;$i<count($ids)-1; $i++){
				$js .= 'applyCascadingDropdown("'.$ids[$i].'", "'.$ids[$i+1].'");';
				}
			$js .= '}applyCascadingZones();</script>';
			return $form.$js;
			
		}
		
}

if ( ! function_exists('form_wysiwyg'))
{

function form_wysiwyg($data)
{
    $return = '<script type="text/javascript" src="'.base_url(). $data['path'] . '/ckeditor.js"></script>';
	if(isset($data['ckfinder']['path']) && !empty($data['ckfinder']['path'])){
    $return .= '<script type="text/javascript" src="'.base_url(). $data['ckfinder']['path'] . '/ckfinder.js"></script>';
	}
	
	/*$return .= "<script type=\"text/javascript\">CKEDITOR.replace( '" . $data['id'] . "',{ enterMode : CKEDITOR.ENTER_P });</script>";
	$return .= "<script type=\"text/javascript\">CKEDITOR.replace( '" . $data['id'] . "',{ shiftEnterMode : CKEDITOR.ENTER_BR });</script>";*/

    //Adding styles values
    if(isset($data['styles'])) {
        
        $return .= "<script type=\"text/javascript\">CKEDITOR.addStylesSet( 'my_styles', [";
   
        
        foreach($data['styles'] as $k=>$v) {
            
            $return .= "{ name : '" . $k . "', element : '" . $v['element'] . "', styles : { ";

            if(isset($v['styles'])) {
                foreach($v['styles'] as $k2=>$v2) {
                    
                    $return .= "'" . $k2 . "' : '" . $v2 . "'";
                    
                    if($k2 !== end(array_keys($v['styles']))) {
                         $return .= ",";
                    }
                } 
            } 
        
            $return .= '} }';
            
            if($k !== end(array_keys($data['styles']))) {
                $return .= ',';
            }            
            

        } 
        
        $return .= ']);</script>';
    }   
    
    //Building Ckeditor
    
    $return .= "<script type=\"text/javascript\">
         CKEDITOR_BASEPATH = '" . base_url() . $data['path'] . "/';
        CKEDITOR.replace('" . $data['id'] . "', {";
    
            //Adding config values
            if(isset($data['config'])) {
                foreach($data['config'] as $k=>$v) {
                    
                    $return .= $k . " : '" . $v . "'";
                    
                    if($k !== end(array_keys($data['config']))) {
                        $return .= ",";
                    }                        
                } 
            }               
                    
    $return .= ',
  	filebrowserBrowseUrl : "'.base_url().'cside/js/ckfinder/ckfinder.html",
    filebrowserImageBrowseUrl : "'.base_url().'cside/js/ckfinder/ckfinder.html?type=Images",
    filebrowserFlashBrowseUrl : "'.base_url().'cside/js/ckfinder/ckfinder.html?type=Flash",
    filebrowserUploadUrl : "'.base_url().'cside/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files",
    filebrowserImageUploadUrl : "'.base_url().'cside/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images",
    filebrowserFlashUploadUrl : "'.base_url().'cside/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash",
  	filebrowserWindowWidth : "1000",
    filebrowserWindowHeight : "700",

 
  });'; 
  
    $return .= "CKEDITOR.config.stylesCombo_stylesSet = 'my_styles';";

   if(isset($data['ckfinder']['path']) && !empty($data['ckfinder']['path'])){
	
    //CKFINDER INTEGRATION
	$return .= "CKFinder.setupCKEditor( this, { basePath : '" .base_url(). $data['ckfinder']['path']."/', rememberLastFolder : false } ) ;";
    $return .= "if (CKEDITOR.instances['" . $data['id'] . "']) {CKEDITOR.remove(CKEDITOR.instances['" . $data['id'] . "']);}";
    //FINISH SCRIPT
   }
    $return .= "</script>";
    
    return $return;
} 	

}
