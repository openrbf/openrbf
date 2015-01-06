<?php

function comm_verific($params){
	
	$ci = get_instance();
	
        $mod_title = 'SAISIE DES DONNEES - '.$params['filetype_name'];
        
        $template= '<div class="block">
			
                    <div class="block_head">										
                        <h2>'.$mod_title
                            .'
                        </h2>					
                    </div>
                    <div class="block_content">
                    <form action="" name="frm_step_two" id="frm_step_two" method="post" accept-charset="utf-8">';
	$template.= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="dataentry">
            <tr>
                <td><strong>'.$ci->lang->line('list_entity').'</strong></td>
                <td>'.$params['entity_name'].'</td></tr>
              <tr>
                    <td><strong>'.$ci->lang->line('datafile_period').'</strong></td>
                    <td>'.$ci->lang->line('app_month_'.$params['datafile_month']).' '.$params['datafile_year'].'</td>
              </tr>'.
                      
                 "<tr><td><b>Nom de l'OCB</b></td>". 
                '<td rowspan="2" valign="top">'.form_input(	array( 	'name' => 'datafile_remark',
                                                                                                    'id' => 'datafile_remark',
                                                                                                    'class' =>'span12',
                                                                                                    'value' => $params['datafile_remark'],
                                                                                                    'rows' => 1,
                                                                                                    'cols' => 40)).
                '</td></tr>'.
                      
             
             '<tr>
                <td colspan="3">
            ';
  
  	$total_attrib = 0;
	$total_tarif = 0;
	
	$ci->table->set_template(	array ( 'table_open' => '<table class="table table-condensed">',
										'table_close' => '</table>' )
										);
			
	$ci->table->set_heading(	array(	'#',
										'Indicator',
										'D&eacute;clar&eacute;e'));
	
	//$sql = "SELECT pbf_datafiledetails.datafiledetail_id ,pbf_indicators.indicator_id,pbf_dataelts.dataelts_title,pbf_datafiledetails.dataelts_claimed_value,pbf_datafiledetails.dataelts_verified_value,pbf_datafiledetails.dataelts_validated_value,IF(pbf_datafiledetails.datafiledetail_id IS NULL,pbf_dataeltsfileypes.default_tarif,pbf_datafiledetails.dataelts_tarif) AS dataelts_tarif,pbf_datafiledetails.dataelts_montant FROM pbf_dataelts LEFT JOIN pbf_dataeltsfileypes ON (pbf_dataeltsfileypes.dataelts_id = pbf_dataelts.dataelts_id) LEFT JOIN pbf_datafiledetails ON (pbf_datafiledetails.dataelts_id = pbf_dataelts.dataelts_id AND pbf_datafiledetails.datafile_id = '".$params['datafile_id']."') WHERE pbf_dataeltsfileypes.filetype_id = '".$params['filetype_id']."' AND (LAST_DAY('".$params['datafile_year']."-".$params['datafile_month']."-1') BETWEEN pbf_dataeltsfileypes.use_from AND pbf_dataeltsfileypes.use_to) ORDER BY pbf_dataeltsfileypes.order";

	$lang=$ci->config->item('language_abbr');
        $sql = "SELECT pbf_indicators.indicator_id,pbf_datafiledetails.datafiledetail_id,
            pbf_indicators.indicator_vartype,pbf_indicatorstranslations.indicator_title,pbf_datafiledetails.indicator_claimed_value,pbf_datafiledetails.indicator_verified_value,pbf_datafiledetails.indicator_validated_value,IF(pbf_datafiledetails.indicator_tarif IS NULL,IF(pbf_indicatorstarif.indicatortarif_tarif IS NULL,pbf_indicatorsfileypes.default_tarif,pbf_indicatorstarif.indicatortarif_tarif),pbf_datafiledetails.indicator_tarif) as indicator_tarif,pbf_datafiledetails.indicator_montant,pbf_indicatorsfileypes.indicator_category_id FROM pbf_indicators LEFT JOIN pbf_datafiledetails ON (pbf_indicators.indicator_id=pbf_datafiledetails.indicator_id AND pbf_datafiledetails.datafile_id='".$params['datafile_id']."') LEFT JOIN pbf_indicatorsfileypes ON (pbf_indicatorsfileypes.indicator_id=pbf_indicators.indicator_id) LEFT JOIN pbf_entities ON (pbf_entities.entity_id='".$params['entity_id']."') LEFT JOIN pbf_indicatorstranslations ON pbf_indicatorstranslations.indicator_id=pbf_indicators.indicator_id LEFT JOIN pbf_indicatorstarif ON
		  (pbf_indicatorstarif.indicatortarif_monthto>='".$params['datafile_month']."'
		    AND pbf_indicatorstarif.indicatortarif_monthfrom<='".$params['datafile_month']."'
	  AND pbf_indicatorstarif.indicatortarif_year='".$params['datafile_year']."' AND ((pbf_indicatorstarif.indicatortarif_entity_id=pbf_entities.entity_id AND pbf_indicatorstarif.indicatortarif_geozone_id=pbf_entities.entity_geozone_id) AND (pbf_indicatorstarif.indicatortarif_entity_type_id=pbf_entities.entity_type AND pbf_indicatorstarif.indicatortarif_entity_class_id=pbf_entities.entity_class)) AND pbf_indicatorstarif.indicatortarif_filetype_id=pbf_indicatorsfileypes.filetype_id)
		   WHERE pbf_indicatorsfileypes.filetype_id='".$params['filetype_id']."' AND (LAST_DAY('".$params['datafile_year']."-".$params['datafile_month']."-1') BETWEEN pbf_indicatorsfileypes.use_from AND pbf_indicatorsfileypes.use_to) AND pbf_indicatorstranslations.indicator_language ='".$lang."' ORDER BY pbf_indicatorsfileypes.order";
        
       // $details = $ci->datafiles_mdl->get_datafile_details($params['datafile_id'],$params['filetype_id'],$params['entity_id'],$params['datafile_month'],$params['datafile_year']);
		$details = $ci->db->query($sql)->result_array();
		if(empty($details)){//verification si il ya des indicateurs pour cette periode
			
			$ci->session->set_flashdata(array('mod_clss' => 'errormsg', 'mod_msg' => $ci->lang->line('datafile_missing_details'))); 
			//$ci->pbf->set_eventlog();
			//redirect('datafiles/datamngr/');
			redirect('dashboard/');
			
			}

        $i=0;
  
        foreach ($details AS $detail){
         //	json_decode sur le champ �dataelttarif_tarif� pour avoir un array() de type dataelt_id => tarif_value


            if ($detail['indicator_tarif']!=NULL) {
                $temp = json_decode($detail['indicator_tarif'], true);
                if (is_array($temp)) {   /// json tarif, keep only the one for the correct indicator
                    $details[$i]['indicator_tarif']=$temp[$detail['indicator_id']];
                }
    
                $i++;
            }
        }
        
        
	$results = $details;
	
	foreach($results as $k => $result){
		
		$ci->table->add_row(	
						array('data' => $k+1, 'align' => 'right'),
						array('data' => $result['indicator_title'].form_hidden(	array(	'indicator_id[]' => $result['indicator_id'],
																						'datafiledetail_id[]' => $result['datafiledetail_id'])
																					)),
						array('data' => form_input(	array( 	'name' => 'indicator_validated_value[]',
															'id' => 'indicator_validated_value_'.$k,
															'class'=>'dataentry',
															'value'=> (is_null($result['indicator_validated_value']) && !is_null($result['datafiledetail_id']))?'-':!is_null($result['indicator_validated_value'])?number_format($result['indicator_validated_value'],2):'')
											))
						
							);
		
		$total_attrib += $result['indicator_validated_value'];
		$total_tarif += (is_null($result['indicator_validated_value']) && !is_null($result['datafiledetail_id']))?0:$result['indicator_tarif'];
		}
		
		$ci->table->add_row(	
						array('data' => '', 'align' => 'right'),
						array('data' => ''),
						array('data' => '')				
						
							);
		
	return $template.$ci->table->generate().form_hidden(	array(	'datafile_id' => $params['datafile_id'],
																	'filetype_id' => $params['filetype_id'],
																	'entity_id' => $params['entity_id'],
																	'datafile_month' => $params['datafile_month'],
																	'datafile_year' => $params['datafile_year'])).
                '</td>
                            </tr>
                            <tr>
                                <td colspan="3">'.form_button('save', $ci->lang->line('app_form_save'), 'onClick="savefile()"').
                                    form_button('cancel',$ci->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;"').
                                '</td>
                            </tr>
                        </table>
                   </form>
                   </div>
               </div>'.
'<script type="text/javascript">

	function savefile(){
		document.getElementById("frm_step_two").action="'.base_url().'datafiles/save";
		document.getElementById("frm_step_two").submit();
		}
</script>';
	
	}

?>