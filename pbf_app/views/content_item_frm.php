<div class="block">

	<div class="block_head">
					
					<?php
                    echo (isset($mod_title)?$this->pbf->get_mod_title($mod_title):'');
					?>
									
				</div>
	<!-- .block_head ends -->
	<div class="block_content">
				
					<?php 
					
echo 
	form_open_multipart('cms/save').
	
	form_hidden(array( 'content_id' => isset($content_item['content_id'])?$content_item['content_id']:'')).
	
	'<p>'.form_label($this->lang->line('cms_article_title_label'), 'content_title').
	
	form_input(array( 'name' => 'content_title',
						'id' => 'content_title',
						'class' => 'longtext',
						'value' => set_value('content_title',isset($content_item['content_title'])?$content_item['content_title']:'')
						)).'</p>';

switch($this->session->userdata('item_category_id')) {
 case 38:  //edito
  
  echo '<p>'.form_label($this->lang->line('cms_html'), 'html').
  form_textarea(array('name' => 'html_block',
    'id' => 'content_description',
    'rows'=> 6,
    'cols'=> 40,
    'value' => set_value('html_block',isset($html_block)?$html_block:'')
  )).form_wysiwyg($ckeditor).'</p>';
  //echo $this->lang->line('cms_html_edit').$content_item['content_id'];
  
  //echo '<p>'.$this->lang->line('edito_key_'.$content_item['content_id']).'</p>';
  echo '<p>'.form_label($this->lang->line('cms_position'), 'content_position');
  
  echo form_dropdown('content_position', array ('Home_Top_Left'=>'Home_Top_Left','Home_Top_Right'=>'Home_Top_Right','Home_Below_Map'=>'Home_Below_Map','Quality_definition'=>'Quality Definition'),isset($content_item['content_position']) ?$content_item['content_position']:'')
  
  . '</p>'; 
  
  //echo '<p>'.form_label($this->lang->line('cms_article_featured_img'), 'content_link');
  
  
  
  // echo form_upload(array(  'name' => 'content_link',
  //   'id' => 'content_link'
  // )).'</p>';
  

  
  echo '<p>'.form_label($this->lang->line('cms_article_published'), 'content_published').
  
  form_checkbox(array( 	'name' =>
    'content_published',
    'id' => 'content_published',
    'value' => 1,
    'checked' => (isset($content_item['content_published']) && $content_item['content_published']==1)?TRUE:FALSE
  )). '</p>'.
  
  
  /*'<p>'.form_label($this->lang->line('cms_article_featured'), 'content_featured').
  
  form_checkbox(array( 	'name' =>
  'content_featured',
  'id' => 'content_featured',
  'value' => 1,
  'checked' => (isset($content_item['content_featured']) && $content_item['content_featured']==1)?TRUE:FALSE
  )). '</p>'.
  */
  
  // Begin form footer...........
  
  form_submit('submit', $this->lang->line('app_form_save'), 'class="submit small"').
  form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"').
  
  // End form footer...........
  
  form_close();
  break;
  
 case 39: //top
  
   
  // use content_position to content type....
 echo '<p>'.form_label($this->lang->line('cms_content_type'), 'content_position');
 
 echo form_dropdown('content_position', array ('quality'=>'Quality','quantity'=>'Quantity'),isset($content_item['content_position']) ?$content_item['content_position']:'')
 
 . '</p>';
 
 //use content_description to store top amount
 echo '<p>'.form_label($this->lang->line('cms_top_amount'), 'top_amount').
     form_input(array( 'name' => 'content_description',
  'id' => 'content_description',
  'class' => 'longtext',
  'value' => set_value('top_amount',isset($content_item['content_description'])?$content_item['content_description']:'')
  )).'</p>';
  

  echo '<p>'.form_label($this->lang->line('cms_article_published'), 'content_published').
  
  
  form_checkbox(array( 	'name' =>
    'content_published',
    'id' => 'content_published',
    'value' => 1,
    'checked' => (isset($content_item['content_published']) && $content_item['content_published']==1)?TRUE:FALSE
  )). '</p>'.

  
  '<p>'.form_label('Entity types', 'content_params').
          
  form_multiselect('content_params[]',$entity_types, json_decode($content_item['content_params']),'class="longtext" size="6"').'</p>'.       
  
  // Begin form footer...........
  
  form_submit('submit', $this->lang->line('app_form_save'), 'class="submit small"').
  form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"').
  
  // End form footer...........
  
  form_close();
  
  break;
  case 40:  //keydata
  
   echo '<p>'.form_label($this->lang->line('cms_html'), 'html').
   form_textarea(array('name' => 'html_block',
   'id' => 'content_description',
   'rows'=> 6,
   'cols'=> 40,
   'value' => set_value('html_block',isset($html_block)?$html_block:'')
   )).'</p>'; 


   //use content_description to store percentage
   echo '<p>'.form_label($this->lang->line('cms_percentage'), 'cms_percentage').
   form_input(array( 'name' => 'content_description',
     'id' => 'content_description',
     'class' => 'longtext',
     'value' => set_value('top_amount',isset($content_item['content_description'])?$content_item['content_description']:'')
   )).'</p>';
   

   echo '<p>'.form_label($this->lang->line('cms_article_published'), 'content_published').
  
   form_checkbox(array( 	'name' =>
     'content_published',
     'id' => 'content_published',
     'value' => 1,
     'checked' => (isset($content_item['content_published']) && $content_item['content_published']==1)?TRUE:FALSE
   )). '</p>'.
   

  
   form_submit('submit', $this->lang->line('app_form_save'), 'class="submit small"').
   form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"').
  
   // End form footer...........
  
   form_close();
   break;
  
  
 default:
  if($this->session->userdata('item_category_id')==32){
  
   echo '<p>'.form_label($this->lang->line('cms_article_content'), 'content_description').
  
   form_input(array( 'name' => 'content_description',
     'id' => 'content_description',
     'class' => 'longtext',
     'value' => set_value('content_description',isset($content_item['content_description'])?$content_item['content_description']:'')
   )).'</p>';
   
     	if (!in_array('cms/setfeature/',$this->session->userdata('usergroupsrules'))){
	echo '';
	}
	else{
  
   echo '<p>'.form_label($this->lang->line('cms_article_featured_img'), 'content_link');
   echo form_upload(array(  'name' => 'content_link',
     'id' => 'content_link'
   ));
   echo '&nbsp;&nbsp;&nbsp;';
   echo anchor_popup(base_url('cside/contents/images/'.$content_item['content_link']),'View file');
   
   echo '</p>';
  }
   }
  
  else{
  
   echo '<p>'.form_label($this->lang->line('cms_article_content'), 'content_description').
  
   form_textarea(array('name' => 'content_description',
     'id' => 'content_description',
     'rows'=> 6,
     'cols'=> 40,
     'value' => set_value('content_description',isset($content_item['content_description'])?$content_item['content_description']:'')
   )).form_wysiwyg($ckeditor).'</p>';
   	
   if($this->session->userdata('item_category_id')==30){
  
    echo '<p>'.form_label($this->lang->line('doc_file_label'), 'content_link');
  
   }
   else{
    	if (!in_array('cms/setfeature/',$this->session->userdata('usergroupsrules'))){
	echo '';
	}
	else{
  
    echo '<p>'.form_label($this->lang->line('cms_article_featured_img'), 'content_link');
    }
  
  }
  
  
   echo form_upload(array(  'name' => 'content_link',
     'id' => 'content_link'
   )).'</p>';
  
  }
  
 if ((!in_array('cms/setpublish/',$this->session->userdata('usergroupsrules'))) AND ($this->session->userdata('item_category_id')==29 OR $this->session->userdata('item_category_id')==30 OR $this->session->userdata('item_category_id')==32)){
  echo '';
  }
  else{
   echo '<p>'.form_label($this->lang->line('cms_article_published'), 'content_published').
  
  form_checkbox(array( 	'name' =>
    'content_published',
    'id' => 'content_published',
    'value' => 1,
    'checked' => (isset($content_item['content_published']) && $content_item['content_published']==1)?TRUE:FALSE
  )). '</p>';
  }
  	if ((!in_array('cms/setfeature/',$this->session->userdata('usergroupsrules'))) AND ($this->session->userdata('item_category_id')==29 OR 
			$this->session->userdata('item_category_id')==30 OR $this->session->userdata('item_category_id')==32)){
	echo '';
	}
	else{
 echo  '<p>'.form_label($this->lang->line('cms_article_featured'), 'content_featured').
  
  form_checkbox(array( 	'name' =>
    'content_featured',
    'id' => 'content_featured',
    'value' => 1,
    'checked' => (isset($content_item['content_featured']) && $content_item['content_featured']==1)?TRUE:FALSE
  )). '</p>';
  }

//Ajout d'un nouveau champ dans le formulaire s'il s'agit d'enregistrement d'un lien important par cms
if($this->session->userdata('item_category_id')==32){
	 $form_position_dropdown='<select name="content_position">';
							if(isset($content_item['content_position'])){
$form_position_dropdown.='<option value="'.$content_item['content_position'].'" selected="selected">'.$content_item['lookup_title'].'</option>';
							}else
	 $form_position_dropdown.='<option value="" selected="selected">Select</option>';
			for($i=0; $i<count($lookup__logo); $i++){
		$form_position_dropdown.='<option value="'.$lookup__logo[$i]['lookup_id'].'">'.$lookup__logo[$i]['lookup_title'].'</option>';
			}
	$form_position_dropdown.='</select>';
	echo '<p>'.form_label('Position', 'position_logo').$form_position_dropdown.'</p>';
}

echo
  
  form_submit('submit', $this->lang->line('app_form_save'), 'class="submit small"').
  form_reset('',$this->lang->line('app_form_cancel'),'onClick="history.go(-1);return true;" class="submit small"').
  
  form_close();
  
  break; 
}



?>					
					
				</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>
