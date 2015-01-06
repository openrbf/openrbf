<?php 
echo 
	form_open('home/search').
	
	form_input(array(   'name' => 'search',
						'id' => 'search',
						'value' => set_value('search',($this->input->post('search'))?$this->input->post('search'):$this->lang->line('app_search_caption_key'))
						)).
	form_close();
?>