<div class="block">

	<div class="block_head">
					
					<?php
					echo (isset ( $mod_title ) ? $this->pbf->get_mod_title ( $mod_title ) : '');
					?>
					
					
				</div>
	<!-- .block_head ends -->
	<div class="block_content">
				<?php
				echo (isset ( $mngt_submenu ) ? '<p class="breadcrumb">' . $this->pbf->get_mod_submenu ( $mngt_submenu ) . '</p>' : '').

	/*($this->session->flashdata('mod_clss')?'<div class="message '.
				$this->session->flashdata('mod_clss').
				'" style="display: block;"><p>'.
				$this->session->flashdata('mod_msg').
				'</p></div>':'').*/

	form_open_multipart ( 'configuration/saveconfig' ) . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_active_data_tab' ), 'active_data_tab' ) . 

				form_checkbox ( array (
						'name' => 'active_data_tab',
						'id' => 'active_data_tab',
						'value' => 1,
						'checked' => (isset ( $active_data_tab ) && $active_data_tab == 1) ? TRUE : FALSE 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_active_data_toggle_link' ), 'active_data_toggle_link' ) . 

				form_checkbox ( array (
						'name' => 'active_data_toggle_link',
						'id' => 'active_data_toggle_link',
						'value' => 1,
						'checked' => (isset ( $active_data_toggle_link ) && $active_data_toggle_link == 1) ? TRUE : FALSE 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_num_period_display' ), 'num_period_display' ) . 

				form_input ( array (
						'name' => 'num_period_display',
						'id' => 'num_period_display',
						'class' => 'dataentry',
						'value' => set_value ( 'num_period_display', isset ( $num_period_display ) ? $num_period_display : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_aff_user_entity' ), 'user_entity_aff' ) . 

				form_checkbox ( array (
						'name' => 'user_entity_aff',
						'id' => 'user_entity_aff',
						'value' => 1,
						'checked' => (isset ( $user_entity_aff ) && $user_entity_aff == 1) ? TRUE : FALSE 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_shortdate_format' ), 'short_date_format' ) . 

				form_input ( array (
						'name' => 'short_date_format',
						'id' => 'short_date_format',
						'value' => set_value ( 'short_date_format', isset ( $short_date_format ) ? $short_date_format : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_longdate_format' ), 'long_date_format' ) . 

				form_input ( array (
						'name' => 'long_date_format',
						'id' => 'long_date_format',
						'value' => set_value ( 'long_date_format', isset ( $long_date_format ) ? $long_date_format : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_currency' ), 'app_country_currency' ) . 

				form_input ( array (
						'name' => 'app_country_currency',
						'id' => 'app_country_currency',
						'value' => set_value ( 'app_country_currency', isset ( $app_country_currency ) ? $app_country_currency : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_default_lang' ), 'language' ) . 

				form_dropdown ( 'language', array (
						'english' => 'English',
						'francais' => 'Francais' 
				), str_replace ( "'", '', $language ) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_lang_selector' ), 'show_lang_selector' ) . 

				form_checkbox ( array (
						'name' => 'show_lang_selector',
						'id' => 'show_lang_selector',
						'value' => 1,
						'checked' => (isset ( $show_lang_selector ) && $show_lang_selector == 1) ? TRUE : FALSE 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_logo' ), 'app_logo' ) . 

				form_upload ( array (
						'name' => 'app_logo',
						'id' => 'app_logo' 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_country_map' ), 'app_country_map' ) . 

				form_upload ( array (
						'name' => 'app_country_map',
						'id' => 'app_country_map' 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_theme' ), 'app_color_scheme' ) . 

				form_dropdown ( 'app_color_scheme', array (
						'#DDDDDD' => 'Silver',
						'#3399CC' => 'Blue',
						'#499510' => 'Green' 
				), str_replace ( "'", '', $app_color_scheme ) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_font_size' ), 'app_font_size' ) . 

				form_dropdown ( 'app_font_size', array (
						'11' => 'Small',
						'12' => 'Normal',
						'14' => 'Large' 
				), str_replace ( "'", '', $app_font_size ) ) . '</p>' . 

				'<p>' . form_label ( 'Report prefix', 'report_prefix' ) . 

				form_input ( array (
						'name' => 'report_prefix',
						'id' => 'report_prefix',
						'value' => set_value ( 'report_prefix', isset ( $report_prefix ) ? $report_prefix : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_rec_per_page' ), 'rec_per_page' ) . 

				form_input ( array (
						'name' => 'rec_per_page',
						'id' => 'rec_per_page',
						'class' => 'dataentry',
						'value' => set_value ( 'rec_per_page', isset ( $rec_per_page ) ? $rec_per_page : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_pop_growth_rate' ), 'pop_growth_rate' ) . 

				form_input ( array (
						'name' => 'pop_growth_rate',
						'id' => 'pop_growth_rate',
						'class' => 'dataentry',
						'value' => set_value ( 'pop_growth_rate', isset ( $pop_growth_rate ) ? $pop_growth_rate : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_realtimeresult_evolution_period_home' ), 'realtimeresult_evolution_period_home' ) . 

				form_input ( array (
						'name' => 'realtimeresult_evolution_period_home',
						'id' => 'realtimeresult_evolution_period_home',
						'class' => 'dataentry',
						'value' => set_value ( 'realtimeresult_evolution_period_home', isset ( $realtimeresult_evolution_period_home ) ? $realtimeresult_evolution_period_home : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_realtimeresult_period_data' ), 'realtimeresult_period_data' ) . 

				form_input ( array (
						'name' => 'realtimeresult_period_data',
						'id' => 'realtimeresult_period_data',
						'class' => 'dataentry',
						'value' => set_value ( 'realtimeresult_period_data', isset ( $realtimeresult_period_data ) ? $realtimeresult_period_data : '' ) 
				) ) . '</p>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_average_quality_period' ), 'average_quality_period' ) . 

				form_input ( array (
						'name' => 'average_quality_period',
						'id' => 'average_quality_period',
						'class' => 'dataentry',
						'value' => set_value ( 'average_quality_period', isset ( $average_quality_period ) ? $average_quality_period : '' ) 
				) ) . '</p>' . 

				'<fieldset> <legend>' . $this->lang->line ( 'frmlabel_images_size' ) . '</legend>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_big_image' ), 'image_big_size' ) . 

				form_input ( array (
						'name' => 'image_big_size',
						'id' => 'image_big_size',
						'class' => 'dataentry',
						'value' => set_value ( 'image_big_size', isset ( $image_big_size ) ? $image_big_size : '1024' ) 
				) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'frmlabel_medium_image' ), 'image_medium_size' ) . 

				form_input ( array (
						'name' => 'image_medium_size',
						'id' => 'image_medium_size',
						'class' => 'dataentry',
						'value' => set_value ( 'image_medium_size', isset ( $image_medium_size ) ? $image_medium_size : '300' ) 
				) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'frmlabel_thumb_image' ), 'image_thumb_size' ) . 

				form_input ( array (
						'name' => 'image_thumb_size',
						'id' => 'image_thumb_size',
						'class' => 'dataentry',
						'value' => set_value ( 'image_thumb_size', isset ( $image_thumb_size ) ? $image_thumb_size : '150' ) 
				) ) . '</p>' . 

				'</fieldset>' . 

				'<fieldset> <legend>' . $this->lang->line ( 'frmlabel_percentage_lever' ) . '</legend>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_1stlever' ), 'color_pourcentage_1stlever' ) . 

				form_input ( array (
						'name' => 'color_pourcentage_1stlever',
						'id' => 'color_pourcentage_1stlever',
						'class' => 'dataentry',
						'value' => set_value ( 'color_pourcentage_1stlever', isset ( $color_pourcentage_1stlever ) ? $color_pourcentage_1stlever : '95' ) 
				) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'frmlabel_2ndlever' ), 'color_pourcentage_2ndlever' ) . 

				form_input ( array (
						'name' => 'color_pourcentage_2ndlever',
						'id' => 'color_pourcentage_2ndlever',
						'class' => 'dataentry',
						'value' => set_value ( 'color_pourcentage_2ndlever', isset ( $color_pourcentage_2ndlever ) ? $color_pourcentage_2ndlever : '80' ) 
				) ) . '</p>' . '<p>' . form_label ( $this->lang->line ( 'frmlabel_3rdlever' ), 'color_pourcentage_3rdlever' ) . 

				form_input ( array (
						'name' => 'color_pourcentage_3rdlever',
						'id' => 'color_pourcentage_3rdlever',
						'class' => 'dataentry',
						'value' => set_value ( 'color_pourcentage_3rdlever', isset ( $color_pourcentage_3rdlever ) ? $color_pourcentage_3rdlever : '60' ) 
				) ) . '</p>' . 

				'</fieldset>' . 

				'<p>' . form_label ( $this->lang->line ( 'frmlabel_app_admin_email' ), 'app_admin_email' ) . 

				form_input ( array (
						'name' => 'app_admin_email',
						'id' => 'app_admin_email',
						'class' => 'longtext',
						'value' => set_value ( 'app_admin_email', isset ( $app_admin_email ) ? $app_admin_email : '' ) 
				) ) . '</p>' . 
				'<p>' . form_label ( $this->lang->line ( 'report_related_frontend' ), 'reports_label' ) . 

				form_dropdown ( 'reports', $reports_list_items, $report_feed_frontend_selected ) . '</p>' . 
				
				
				'<p>' . form_label ( $this->lang->line ( 'frm_auto_generate_reports' ), 'auto_generate_reports' ) . 

				form_checkbox ( array (
						'name' => 'auto_report_generation',
						'id' => 'auto_report_generation',
						'value' => 1,
						'checked' => (isset ( $auto_report_generation ) && $auto_report_generation == 1) ? TRUE : FALSE 
				) ) . '</p>' . 
				
				// Begin form footer...........
				
				form_submit ( 'submit', $this->lang->line ( 'app_form_save' ), 'class="submit small"' ) . form_reset ( '', $this->lang->line ( 'app_form_cancel' ), 'onClick="history.go(-1);return true;" class="submit small"' ) . 
				
				// End form footer...........
				
				form_close ();
				?>
</div>
	<!-- .block_content ends -->

	<div class="bendl"></div>
	<div class="bendr"></div>

</div>
