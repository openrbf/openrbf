<?php

    function qtty_evaluation($params) {
       $ci = &get_instance();
        
        $ci->lang->load('datafiles', 'en');
        $ci->lang->load('indicators', 'en');
        
		$permissions = $ci->session->userdata('usergroupsrules');
        $canValidate = array_search('datafiles/validate/',$permissions);
		if($params['datafile_id']){
		$file_status = $ci->datafiles_mdl->get_datafile_status($params['datafile_id']);
    	$datafile_status = $file_status['datafile_state'];
  	
		}else{
			$datafile_status =0;
		}
		
		
		
        $header_scripts = 
                '<script type="text/javascript">
function getinvoice_total(){
  	var temp=0;
	var temp_line_tot=0;
	totals = document.getElementsByName(\'indicator_montant[]\');
  	 for(x=0; x < totals.length; x++){
		  if(isNaN(totals[x].value.replace(/,/g,"")) || totals[x].value==""){
			 temp_line_tot=0;
			 }
		else{
			temp_line_tot=totals[x].value.replace(/,/g,"");
			}
     temp=parseFloat(temp)+parseFloat(temp_line_tot);
		}
	return temp;
	}

function addSeparatorsNF(nStr, inD, outD, sep,eName,line)
{
	nStr += "";
	var dpos = nStr.indexOf(inD);
	var nStrEnd = "";
	if (dpos != -1) {
		nStrEnd = outD + nStr.substring(dpos + 1, nStr.length);
		nStr = nStr.substring(0, dpos);
	}
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(nStr)) {
		nStr = nStr.replace(rgx, "$1" + sep + "$2");
	}
	returnVar = document.getElementById(eName); 

	returnVar.value=nStr + nStrEnd;

	return nStr + nStrEnd;
	
}

function Separateme(nStr, inD, outD, sep,eName)
{
	nStr += "";
	var dpos = nStr.indexOf(inD);
	var nStrEnd = "";
	if (dpos != -1) {
		nStrEnd = outD + nStr.substring(dpos + 1, nStr.length);
		nStr = nStr.substring(0, dpos);
	}
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(nStr)) {
		nStr = nStr.replace(rgx, "$1" + sep + "$2");
	}
	returnVar = document.getElementById(eName); 

	returnVar.value=nStr + nStrEnd;

	return nStr + nStrEnd;
	
}

function calculate_row(Sender,Rownbr){

document.getElementById("indicator_montant"+Rownbr).value=parseFloat(document.getElementById("indicator_tarif"+Rownbr).value.replace(/,/g,""))*parseFloat(Sender.replace(/,/g,""));
document.getElementById("montant"+Rownbr).value=parseFloat(document.getElementById("indicator_tarif"+Rownbr).value.replace(/,/g,""))*parseFloat(Sender.replace(/,/g,""));

if(isNaN(document.getElementById("indicator_montant"+Rownbr).value.replace(/,/g,""))){
	document.getElementById("indicator_montant"+Rownbr).value="";
	document.getElementById("montant"+Rownbr).value="";
	}

addSeparatorsNF(document.getElementById("indicator_claimed_value"+Rownbr).value, ".", ".", ",","indicator_claimed_value"+Rownbr,Rownbr);
addSeparatorsNF(Sender.replace(/,/g,""), ".", ".", ",","indicator_verified_value"+Rownbr,Rownbr);
addSeparatorsNF(document.getElementById("indicator_montant"+Rownbr).value, ".", ".", ",","indicator_montant"+Rownbr,Rownbr);

document.getElementById("datafile_total").value=getinvoice_total();
addSeparatorsNF(document.getElementById("datafile_total").value, ".", ".", ",","datafile_total",0);	

}

function savefile()
{
    if(parseFloat(document.getElementById("file_total").value.replace(/,/g,""))!=parseFloat(document.getElementById("datafile_total").value.replace(/,/g,"")) || parseFloat(document.getElementById("datafile_total").value.replace(/,/g,""))==0)
    {
            alert("'.$ci->lang->line("datafile_check_totals").'");
            return false;
    }

    document.getElementById("frm_step_two").action="'.base_url().'datafiles/save";
    document.getElementById("frm_step_two").submit();
}


</script>
<script>    
    $(function(){
    
        if ($("#file_total").val() == "" ) {
            
            $("input[name=\'indicator_claimed_value[]\']").each(function(){
            $(this).attr("disabled","disabled");
            });
            $("input[name=\'indicator_validated_value[]\']").each(function(){
            $(this).attr("disabled","disabled");
            });
            $("input[name=\'indicator_verified_value[]\']").each(function(){
            $(this).attr("disabled","disabled");
            });
        }
        
		
		
			var status = '.$datafile_status.';
       
        
        var fileId = '.$ci->uri->segment(2).';
        var siteUrl = "'.site_url().'datafiles/validate/'.$ci->uri->segment(2).'";
       
     
        if(status!=0) {
            
            $("input").attr("disabled","disabled");
            $("#save").hide();
            siteUrl+="/0";
        }
        
        if(fileId==0) {
            $("#validate").hide();
        }
        
        $("#validate").click(function(){
            console.log("site url"+siteUrl);
            $.ajax({
                url : siteUrl,
                success : function(){
                    window.location.href ="'.site_url('datafiles/datamngr').'";
                },
                error : function() {
                    window.location.href ="'.site_url('datafiles/datamngr').'";
                }
            });
        });
		
		
		
		
		
        $("input").on("keydown", function(e) {
	 if (e.keyCode==13) {
		if(this.id == "file_total"){
			
			$("input[name=\'indicator_verified_value[]\']").eq(0).focus();
                        $("input[name=\'indicator_claimed_value[]\']").eq(0).focus();
		}
		else{
	  		var focusable = $("input").filter(":visible:enabled");
	 		focusable.eq(focusable.index(this)+1).focus();
			}
	  return false;
	 }
	});
        
        $("#file_total").on("blur", function(e) {
		
		if($("#file_total").val() != "" && !isNaN($("#file_total").val().replace(/,/g,""))){';

				$datatype_access=json_decode($ci->session->userdata('datatype_access'));
				if(in_array('indicator_claimed_value', $datatype_access)){
			
				$header_scripts .='	$("input[name=\'indicator_claimed_value[]\']").each(function(){
		      			$(this).removeAttr("disabled");
				});';
			
			}
				
				if(in_array('indicator_validated_value', $datatype_access)){
			
				$header_scripts .='	$("input[name=\'indicator_validated_value[]\']").each(function(){
		      			$(this).removeAttr("disabled");
				});';
			
			}
				
				
				if(in_array('indicator_verified_value', $datatype_access)){
			
				$header_scripts .='	$("input[name=\'indicator_verified_value[]\']").each(function(){
		      			$(this).removeAttr("disabled");
				});';
			
			}
				
			
		$header_scripts .='$("input[name=\'indicator_verified_value[]indicator_verified_value[]\']").eq(0).focus();
			$("input[name=\'indicator_claimed_value[]\']").eq(0).focus();
			
		}
                else{
		
			$("input[name=\'indicator_claimed_value[]\']").each(function(){
      			$(this).attr("disabled","disabled");
			});
			$("input[name=\'indicator_validated_value[]\']").each(function(){
      			$(this).attr("disabled","disabled");
			});
			$("input[name=\'indicator_verified_value[]\']").each(function(){
      			$(this).attr("disabled","disabled");
			});
		
		}
	});
        
    });
</script>
';
  
        $html='';
        
        $lang=$ci->config->item('language_abbr');
        $sql = "SELECT pbf_indicators.indicator_id,pbf_datafiledetails.datafiledetail_id,
            pbf_indicators.indicator_vartype,pbf_indicators.indicator_use_indigence_bonus,pbf_indicatorstranslations.indicator_title,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,IF(pbf_datafiledetails.indicator_tarif IS NULL,IF(pbf_indicatorstarif.indicatortarif_tarif IS NULL,pbf_indicatorsfileypes.default_tarif,pbf_indicatorstarif.indicatortarif_tarif),pbf_datafiledetails.indicator_tarif) as indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_indicatorsfileypes.indicator_category_id FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id AND pbf_datafiledetails.datafile_id='".$params['datafile_id']."') LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id='".$params['entity_id']."') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id LEFT JOIN pbf_indicatorstarif ON
		  (pbf_indicatorstarif.indicatortarif_monthto>='".$params['datafile_month']."'
		    AND pbf_indicatorstarif.indicatortarif_monthfrom<='".$params['datafile_month']."'
	  AND pbf_indicatorstarif.indicatortarif_year='".$params['datafile_year']."' AND ((pbf_indicatorstarif.indicatortarif_entity_id=pbf_entities.entity_id AND pbf_indicatorstarif.indicatortarif_geozone_id=pbf_entities.entity_geozone_id) AND (pbf_indicatorstarif.indicatortarif_entity_type_id=pbf_entities.entity_type AND pbf_indicatorstarif.indicatortarif_entity_class_id=pbf_entities.entity_class)) AND pbf_indicatorstarif.indicatortarif_filetype_id=pbf_indicatorsfileypes.filetype_id)
		   WHERE pbf_indicatorsfileypes.filetype_id='".$params['filetype_id']."' AND (LAST_DAY('".$params['datafile_year']."-".$params['datafile_month']."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_indicatorstranslations.indicator_language ='".$lang."' ORDER BY pbf_indicatorsfileypes.order";
        
        //$details = $ci->datafiles_mdl->get_datafile_details($params['datafile_id'],$params['filetype_id'],$params['entity_id'],$params['datafile_month'],$params['datafile_year']);
	
        $details = $ci->db->query($sql)->result_array();
        
        if(empty($details)){//verification si il ya des indicateurs pour cette periode
			
			$ci->session->set_flashdata(array('mod_clss' => 'errormsg', 'mod_msg' => $ci->lang->line('datafile_missing_details'))); 
			//$ci->pbf->set_eventlog();
			redirect('dashboard/');
			//redirect('datafiles/datamngr/');
			
			}
        
         // only for quality files - TO DO search for quality type
        if (in_array($params['filetype_id'],array(3,4,5,6,7,8,9,20,21,22,23,24,25))){
            if (!isset($_COOKIE["info"])) {
             $data['info']=$ci->lang->line('info');

            }
        }

        $i=0;
  
        foreach ($details AS $detail){
         //	json_decode sur le champ ï¿½dataelttarif_tarifï¿½ pour avoir un array() de type dataelt_id => tarif_value


            if ($detail['indicator_tarif']!=NULL) {
                $temp = json_decode($detail['indicator_tarif'], true);
                if (is_array($temp)) {   /// json tarif, keep only the one for the correct indicator
                    $details[$i]['indicator_tarif']=$temp[$detail['indicator_id']];
                }
    
                $i++;
            }
        }
       $entity_info = $ci->entities_mdl->get_entity($params['entity_id']);
      
	   if($entity_info['entity_pbf_group_id']){
	   	 $entity_group= $entity_info['entity_pbf_group_id'] ; 
	   }else{
	   	$entity_group=1;//pas de bonus pource groupe
	   }

	  //get bonus group
	  $indigence_bonus=$ci->pbf->get_pbf_group_bonus($entity_group);
	  
	  $district_equity_bonus=$entity_info['district_equity_bonus'];
	  
	  $entity_equity_bonus=0;
	  if($entity_info['entity_use_equity_bonus']){
	  	$entity_equity_bonus=$entity_info['entity_equity_pourcentage'];
	  }
	  
	  $equity_bonus=($entity_equity_bonus+$district_equity_bonus)/100;
	  
	//  print_test($details );  exit;   
        $mod_title = 'SAISIE DES DONNEES - '.$params['filetype_name'];
        
        $html= '<div class="block">
			
                    <div class="block_head">										
                        <h2>'.$mod_title
                            .'
                        </h2>					
                    </div>
                    <div class="block_content">
                    
                    <form action="" name="frm_step_two" id="frm_step_two" method="post" accept-charset="utf-8">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="dataentry">
                            <tr>
                                <td><strong>'.$ci->lang->line('list_entity').'</strong></td>
                                <td>'.$params['entity_name'].'</td>
                                <td rowspan="3" valign="top">'. form_textarea(
								array( 'name' => 'datafile_remark',
										'id' => 'datafile_remark',
										'value' => isset($params['datafile_remark'])?$params['datafile_remark']:set_value('datafile_remark'),
										'rows' => 2,
										'cols' => 40)).
                                '</td>
                            </tr>        
                            <tr>
                                    <td><strong>'.$ci->lang->line('datafile_period').'</strong></td>
                                    <td>'.$ci->lang->line('app_month_'.$params['datafile_month']).' '.$params['datafile_year'].'</td>
                            </tr>
                            <tr>
                                    <td><strong>'.$ci->lang->line('datafile_total').'</strong></td>
                                    <td>'.form_input('file_total',isset($params['datafile_total'])?number_format($params['datafile_total'],2):set_value('datafile_total'),'class="dataentry" id="file_total" onchange="addSeparatorsNF(this.value,\'.\',\'.\',\',\',\'file_total\',\'0\');"').'</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    
                                ';
                   
                $table_tmpl = array ( 'table_open' => '<table border="0" cellpadding="4" cellspacing="0">', 'row_start' => '<tr class="even">', 'row_end' => '</tr>', 'row_alt_start' => '<tr class="odd">', 'row_alt_end' => '</tr>', 'table_close' => '</table>' );

		$ci->table->set_template($table_tmpl);
			
                $ci->table->set_heading(array(	'#',$ci->lang->line('datafile_indicator'),
                              $ci->lang->line('indicator_claimed_value'),
                             $ci->lang->line('indicator_validated_value'),
                                                            $ci->lang->line('indicator_tarif'),
                                                            'Total'));
                
                
                foreach ($details as $k=>$row) {
                	if($row['datafiledetail_id']){
                		//bonus déjà calculé
                	}else{
                		//calcul des bonus
                		if($row['indicator_use_indigence_bonus']){
                			//Ajout du bonus d'indigence
                							$row['indicator_tarif']=$row['indicator_tarif']+($row['indicator_tarif'] * ($indigence_bonus / 100));
                		}
                		
						//ajout du bonus d'equité
						$row['indicator_tarif']=$row['indicator_tarif']+($row['indicator_tarif']*$equity_bonus);
                	}
                    
                    $ci->table->add_row(	
                                        array('data' => $k+1, 'align' => 'right'),
                                        array('data' => $row['indicator_title'].form_hidden(array('indicator_id[]' => $row['indicator_id'],'datafiledetail_id[]' => $row['datafiledetail_id']))),
                                        array('data' => form_input(array( 'name' => 'indicator_claimed_value[]',
                                                                          'id' => 'indicator_claimed_value'.$k,
                                                                          'class'=>'dataentry',
                                                                          'onchange'=>'addSeparatorsNF(this.value,\'.\',\'.\',\',\',\'indicator_claimed_value'.$k.'\',\''.$k.'\')',
                                                                          'onkeypress'=>'addSeparatorsNF(this.value,\'.\',\'.\',\',\',\'indicator_claimed_value'.$k.'\',\''.$k.'\')',                                                                                                                                                                              
                                                                          'value' => (is_null($row['indicator_claimed_value']) && !is_null($row['datafiledetail_id']))?'-':(!is_null($row['indicator_claimed_value'])?number_format($row['indicator_claimed_value'],2):''))
											)),
                                        array('data' => form_input(array( 'name' => 'indicator_verified_value[]',
                                                                          'id' => 'indicator_verified_value'.$k,
                                                                          'class'=>'dataentry',
                                                                          'onchange'=>'calculate_row(this.value,'.$k.')',
                                                                          'onkeypress'=>'calculate_row(this.value,'.$k.')',
                                                                          'value' => (is_null($row['indicator_verified_value']) && !is_null($row['datafiledetail_id']))?'-':(!is_null($row['indicator_verified_value'])?number_format($row['indicator_verified_value'],2):''))
											)),
                                        array('data' => '<input type="hidden" name="indicator_tarif[]" id="indicator_tarifhidden'.$k.'" value="'.number_format($row['indicator_tarif'],2).'">'.form_input(	array( 	'name' => 'indicator_tarifdisp[]',
                                                                                                            'id' => 'indicator_tarif'.$k,
                                                                                                            'class'=>'dataentry',
                                                                                                            'disabled'=>'disabled',                                                                                                                         
                                                                                                            'value' => number_format($row['indicator_tarif'],2))
											)),
                                        array('data' => '<input type="hidden" name="indicator_montant[]" id="montant'.$k.'" value="'.$row['indicator_montant'].'">'.form_input(	array( 	'name' => 'montant[]',
															'id' => 'indicator_montant'.$k,
															'class'=>'dataentry',
															'disabled'=>'disabled',
															'value' => (is_null($row['indicator_verified_value']) && !is_null($row['datafiledetail_id']))?'-':(!is_null($row['indicator_montant'])?number_format($row['indicator_montant'],2):''))
											))
				
						
                                        );
		
                        //$total_attrib += $result['dataelts_validated_value'];
                        //$total_tarif += (is_null($result['dataelts_validated_value']) && !is_null($result['datafiledetail_id']))?0:$result['dataelts_tarif'];
                }
               
                $ci->table->add_row(	
                                    array('data' => '', 'align' => 'right'),
                                    array('data' => '<strong>Total</strong>'),
                                    array('data' => ''),
                                    array('data' => ''),
                                    array('data' => ''),
                                    array('data' => form_input(	array( 	'name' => 'datafile_total',
                                                                        'id' => 'datafile_total',
                                                                        'class'=>'dataentry',
                                                                        'readonly'=>'readonly',
                                                                        'value' => number_format($params['datafile_total']))
                                                                ))				
						
                                );
               $html.=  form_hidden(array( 'datafile_id' => $params['datafile_id'])).
                        form_hidden(array( 'filetype_id' => $params['filetype_id'])).
                        form_hidden(array( 'entity_id' => $params['entity_id'])).
                        form_hidden(array( 'datafile_month' => $params['datafile_month'])).
                        form_hidden(array( 'datafile_year' => $params['datafile_year']));
               $html.=$ci->table->generate();
               $html.='
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">'.form_button('save', $ci->lang->line('app_form_save'), 'onClick="savefile()" id="save"');
								
						 if((isset($canValidate) && $canValidate)) {
               
               $html.= ($datafile_status==0)? form_button($ci->lang->line('validate'), $ci->lang->line('validate'), 'id="validate"'):
                form_button($ci->lang->line('unvalidate'), $ci->lang->line('unvalidate'), 'id="validate"');           
                
            }		
				
								$html.=form_button('cancel',$ci->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;"').
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
