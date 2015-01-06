<?php

    function fraud($params) {
    	$ckeditor= array(
            //ID of the textarea that will be replaced
            'id'     =>     'datafile_remark',     // Must match the textarea's id
            'path'    =>    'cside/js/ckeditor',    // Path to the ckeditor folder relative to index.php
            //Ckfinder's configuration
            'ckfinder' => array(
            'path'    =>    'cside/js/ckfinder',    // Path to the ckeditor folder relative to index.php
            ),
            //Optionnal values
            'config' => array(
                'toolbar'     =>     "Basic",     //Using the Full toolbar
                'width'     =>     "700",    //Setting a custom width
                'height'     =>     '100px',    //Setting a custom height
            ),
            //Replacing styles from the "Styles tool"
            'styles' => array(
                //Creating a new style named "style 1"
                'style 1' => array (
                    'name'         =>     'Blue Title',
                    'element'     =>     'h2',
                    'styles' => array(
                        'color'         =>     'Blue',
                        'font-weight'         =>     'bold'
                    )
                ),
                //Creating a new style named "style 2"
                'style 2' => array (
                    'name'         =>     'Red Title',
                    'element'     =>     'h2',
                    'styles' => array(
                        'color'         =>     'Red',
                        'font-weight'         =>     'bold',
                        'text-decoration'    =>     'underline'
                    )
                )
            )
        );
       $ci = &get_instance();
	           
	    $fraudeinfo = json_decode($params['datafile_info'],true);
		    
        $mod_title = 'SAISIE DES DONNEES - '.$params['filetype_name'];
        
        $html= '<script type="text/javascript" src="'.$ci->config->item('base_url').'cside/js/jquery.date_input.pack.js"></script><div class="block">
		<script type="text/javascript">
    $(document).ready(function() {
      	$(\'input.date_picker\').date_input();
			
    });
	
	$.extend(DateInput.DEFAULT_OPTS, {
  stringToDate: function(string) {
    var matches;
    if (matches = string.match(/^(\d{4,4})-(\d{2,2})-(\d{2,2})$/)) {
      return new Date(matches[1], matches[2] - 1, matches[3]);
    } else {
      return null;
    };
  },

  dateToString: function(date) {
    var month = (date.getMonth() + 1).toString();
    var dom = date.getDate().toString();
    if (month.length == 1) month = "0" + month;
    if (dom.length == 1) dom = "0" + dom;
    return date.getFullYear() + "-" + month + "-" + dom;
  }
  });
  
  function savefile()
{
    document.getElementById("frm_step_two").action="'.base_url().'datafiles/save";
    document.getElementById("frm_step_two").submit();
}
	</script>		
                    <div class="block_head">										
                        <h2>'.$mod_title
                            .'
                        </h2>					
                    </div>
                    <div class="block_content">
                    
                    <form action="" name="frm_step_two" id="frm_step_two" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="dataentryFraud">
                            <tr>
                                <td><strong>'.$ci->lang->line('list_entity').':</strong></td>
                                <td>'.$params['entity_name'].'</td>
                            </tr>        
                            <tr>
                                    <td><strong>'.$ci->lang->line('datafile_fraude_date').'</strong></td>
                                    <td>'.form_input(array(   'name' => 'datafile_info[]',
								'id' => 'datafile_created','class' =>'text date_picker',
								'value' => set_value('datafile_info[]',$fraudeinfo[0])
								)).'</td>
                            </tr>
							
                            <tr>
                                    <td><strong>'.$ci->lang->line('list_display_report').':</strong></td>
                                    <td>'.form_checkbox(array( 	'name' =>
    'datafile_info[]',
    'id' => 'content_published',
    'value' => 1,
    'checked' => ($fraudeinfo[1]==1)?TRUE:FALSE
  )).'</td>
                            </tr>
                            <tr>
                            	<td><strong>'.$ci->lang->line('list_commentaire').'</strong></td>
                                <td rowspan="4" valign="top">'. form_textarea(
								array( 'name' => 'datafile_remark',
										'id' => 'datafile_remark',
										'value' => isset($params['datafile_remark'])?$params['datafile_remark']:set_value('datafile_remark'),
										'rows' => 4,
										'cols' => 40)).form_wysiwyg($ckeditor).
                                '</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    
                                ';
								
			$html.=  form_hidden(array( 'datafile_id' => $params['datafile_id'])).
                        form_hidden(array( 'filetype_id' => $params['filetype_id'])).
                        form_hidden(array( 'entity_id' => $params['entity_id'])).
                        form_hidden(array( 'datafile_month' => $params['datafile_month'])).
                        form_hidden(array( 'datafile_year' => $params['datafile_year']));
                   
               $html.='</td></tr><tr><td></td></tr></table><p style="margin-top:-230px">';
               $html.=form_label($ci->lang->line('list_upload_fraud'), 'content_link');
               $html.='<span style="margin-left:-20px">'.form_upload(array(  'name' => 'fraude_upload','id' => 'fraude_uplaod'));
   			   $html.='</span></p>
                            <table>
                            <tr class="tr_button">
                                <td colspan="3">'.form_button('save', $ci->lang->line('app_form_save'), 'onClick="savefile()"').
                                    form_button('cancel',$ci->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;"').
                                '</td>
                            </tr>
                        </table>
                   </form>
                   </div>
               </div>

              ';
        return $header_scripts.$html;
    }
?>
