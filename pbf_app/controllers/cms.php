<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Cms extends CI_Controller {
	function __construct() {
		parent::__construct ();
		
		$this->load->model ( 'cms_mdl' );
		$this->lang->load ( 'cms', $this->config->item ( 'language' ) );
		$this->lang->load ( 'otheroptions', $this->config->item ( 'language' ) );
	}
	function index($item_category_id = 29) {
		$this->session->set_userdata ( array (
				'item_category_id' => $item_category_id 
		) );
		
		redirect ( 'cms/items/' );
	}
	function to_publish($item_category_id) {
		print_r ( $item_category_id );
		$this->session->set_userdata ( array (
				'item_category_id' => $item_category_id 
		) );
		
		redirect ( 'cms/items_to_publish/' );
	}
	function items() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->cms_mdl->get_items ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['content_title'] = anchor ( '/cms/edit/' . $data ['list'] [$k] ['content_id'], $data ['list'] [$k] ['content_title'] );
			
			if ((! in_array ( 'cms/setfeature/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['content_featured'] = '';
			} else {
				$data ['list'] [$k] ['content_featured'] = $this->pbf->rec_op_icon ( 'home_' . $data ['list'] [$k] ['content_featured'], '/cms/setfeature/' . $data ['list'] [$k] ['content_id'] );
			}
			if ((! in_array ( 'cms/edit/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['edit'] = '';
			} else {
				
				if ((! in_array ( 'cms/edit_published/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32) and ($data ['list'] [$k] ['content_published'] == 1)) {
					$data ['list'] [$k] ['edit'] = '';
				} else {
					$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/cms/edit/' . $data ['list'] [$k] ['content_id'] );
				}
			}
			if ((! in_array ( 'cms/setpublish/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['content_published'] = '';
			} else {
				$data ['list'] [$k] ['content_published'] = $this->pbf->rec_op_icon ( 'publish_' . $data ['list'] [$k] ['content_published'], '/cms/setpublish/' . $data ['list'] [$k] ['content_id'] );
			}
			
			if ((! in_array ( 'cms/del/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['delete'] = '';
			} else {
				$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/cms/del/' . $data ['list'] [$k] ['content_id'] );
			}
			
			$data ['list'] [$k] ['checkbox'] = form_checkbox ( 'item[]', $data ['list'] [$k] ['content_id'] );
			$data ['list'] [$k] ['content_id'] = $k + $preps ['offset'] + 1;
		}
		
		$check_all = array (
				'name' => 'sel_all',
				'id' => 'sel_all',
				'onClick' => 'selecte_all(this)' 
		);
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'cms_article_title' ),
				$this->lang->line ( 'cms_date_created' ),
				$this->lang->line ( 'cms_date_modified' ),
				$this->lang->line ( 'cms_author' ),
				'',
				'',
				'',
				'',
				form_checkbox ( $check_all ) 
		) );
		
		$item_name = $this->pbf->get_lookup ( $this->session->userdata ( 'item_category_id' ) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'cms_title' ) . ' - ' . $this->lang->line ( 'option_lkp_ky_' . $item_name ['lookup_id'] ) . ' [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/cms/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['#'] = $this->pbf->rec_op_icon ( 'delete_selected' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		// $data['tab_menus'] = implode('&nbsp;&nbsp;|&nbsp;&nbsp;',$this->pbf->get_lookup_submenu('cms/cms/','front_articles_category')).'&nbsp;&nbsp;|&nbsp;&nbsp;'.$this->pbf->get_mod_submenu(11);
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_lookup_submenu ( 'cms/cms/', 'front_articles_category' ) );
		
		$usergrouprules = $this->session->userdata ( 'usergroupsrules' );
		// TO DO improve this // add publish if publish right in session
		if (in_array ( "publication/", $usergrouprules )) {
			$data ['tab_menus'] = $data ['tab_menus'] . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 11 );
		}
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'title',
				'author' 
		) );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		
		$this->load->view ( 'body', $data );
	}
	function items_to_publish() {
		$preps = $this->pbf->prep_listing_terms_uri_keys ();
		$data = $this->cms_mdl->get_items_to_publish ( $preps ['offset'], $preps ['terms'] );
		
		foreach ( $data ['list'] as $k => $v ) {
			
			$data ['list'] [$k] ['content_title'] = anchor ( '/cms/edit/' . $data ['list'] [$k] ['content_id'], $data ['list'] [$k] ['content_title'] );
			if ((! in_array ( 'cms/setfeature/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['content_featured'] = '';
			} else {
				$data ['list'] [$k] ['content_featured'] = $this->pbf->rec_op_icon ( 'active_' . $data ['list'] [$k] ['content_featured'], '/cms/setfeature/' . $data ['list'] [$k] ['content_id'] );
			}
			if ((! in_array ( 'cms/edit/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['edit'] = '';
			} else {
				
				if ((! in_array ( 'cms/edit_published/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32) and ($data ['list'] [$k] ['content_published'] == 1)) {
					$data ['list'] [$k] ['edit'] = '';
				} else {
					$data ['list'] [$k] ['edit'] = $this->pbf->rec_op_icon ( 'edit', '/cms/edit/' . $data ['list'] [$k] ['content_id'] );
				}
			}
			if ((! in_array ( 'cms/setpublish/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['content_published'] = '';
			} else {
				$data ['list'] [$k] ['content_published'] = $this->pbf->rec_op_icon ( 'publish_' . $data ['list'] [$k] ['content_published'], '/cms/setpublish/' . $data ['list'] [$k] ['content_id'] );
			}
			
			if ((! in_array ( 'cms/del/', $this->session->userdata ( 'usergroupsrules' ) )) and ($this->session->userdata ( 'item_category_id' ) == 29 or $this->session->userdata ( 'item_category_id' ) == 30 or $this->session->userdata ( 'item_category_id' ) == 32)) {
				$data ['list'] [$k] ['delete'] = '';
			} else {
				$data ['list'] [$k] ['delete'] = $this->pbf->rec_op_icon ( 'delete', '/cms/del/' . $data ['list'] [$k] ['content_id'] );
			}
			
			$data ['list'] [$k] ['checkbox'] = form_checkbox ( 'item[]', $data ['list'] [$k] ['content_id'] );
			$data ['list'] [$k] ['content_id'] = $k + $preps ['offset'] + 1;
		}
		$check_all = array (
				'name' => 'sel_all',
				'id' => 'sel_all',
				'onClick' => 'selecte_all(this)' 
		);
		array_unshift ( $data ['list'], array (
				'#',
				$this->lang->line ( 'cms_article_title' ),
				$this->lang->line ( 'cms_date_created' ),
				$this->lang->line ( 'cms_date_modified' ),
				$this->lang->line ( 'cms_author' ),
				'',
				'',
				'',
				'',
				form_checkbox ( $check_all ) 
		) );
		
		$item_name = $this->pbf->get_lookup ( $this->session->userdata ( 'item_category_id' ) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'cms_title' ) . ' - ' . $this->lang->line ( 'option_lkp_ky_' . $item_name ['lookup_id'] ) . ' [' . $data ['records_num'] . ']';
		$data ['mod_title'] ['/cms/add'] = $this->pbf->rec_op_icon ( 'add' );
		$data ['mod_title'] ['#'] = $this->pbf->rec_op_icon ( 'delete_selected' );
		$data ['mod_title'] ['dashboard/'] = $this->pbf->rec_op_icon ( 'close' );
		
		// $data['tab_menus'] = implode('&nbsp;&nbsp;|&nbsp;&nbsp;',$this->pbf->get_lookup_submenu('cms/cms/','front_articles_category')).'&nbsp;&nbsp;|&nbsp;&nbsp;'.$this->pbf->get_mod_submenu(11);
		
		$data ['tab_menus'] = implode ( '&nbsp;&nbsp;|&nbsp;&nbsp;', $this->pbf->get_lookup_submenu ( 'cms/to_publish/', 'front_articles_category' ) );
		
		$usergrouprules = $this->session->userdata ( 'usergroupsrules' );
		// TO DO improve this // add publish if publish right in session
		if (in_array ( "publication/", $usergrouprules )) {
			$data ['tab_menus'] = $data ['tab_menus'] . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $this->pbf->get_mod_submenu ( 11 );
		}
		
		$data ['rec_filters'] = $this->pbf->get_filters ( array (
				'title',
				'author' 
		) );
		
		$this->pbf->get_pagination ( $data ['records_num'], $preps ['keys'], $preps ['uri_segment'] );
		
		$data ['page'] = 'list';
		
		$this->load->view ( 'body', $data );
	}
	function add($data = '') {
		$this->load->model ( 'cms_mdl' );
		$this->load->model ( 'entities_mdl' );
		$entities = $this->entities_mdl->get_types ();
		$entity_types = array ();
		foreach ( $entities ['list'] as $entity_type ) {
			$entity_types [$entity_type ['entity_type_id']] = $entity_type ['entity_type_name'] . '( ' . $entity_type ['entity_type_abbrev'] . ' )';
		}
		
		$data ['entity_types'] = $entity_types;
		$item_name = $this->pbf->get_lookup ( $this->session->userdata ( 'item_category_id' ) );
		
		$data ['mod_title'] ['mod_title'] = $this->lang->line ( 'cms_title' ) . ' - ' . $this->lang->line ( trim ( $item_name ['lookup_title'] ) );
		
		$data ['ckeditor'] = array (
				// ID of the textarea that will be replaced
				'id' => 'content_description', // Must match the textarea's id
				'path' => 'cside/js/ckeditor', // Path to the ckeditor folder relative to index.php
				                                     // Ckfinder's configuration
				'ckfinder' => array (
						'path' => 'cside/js/ckfinder'  // Path to the ckeditor folder relative to index.php
								),
				// Optionnal values
				'config' => array (
						'toolbar' => "Full", // Using the Full toolbar
						'width' => "850px", // Setting a custom width
						'height' => '200px'  // Setting a custom height
								),
				// Replacing styles from the "Styles tool"
				'styles' => array (
						// Creating a new style named "style 1"
						'style 1' => array (
								'name' => 'Blue Title',
								'element' => 'h2',
								'styles' => array (
										'color' => 'Blue',
										'font-weight' => 'bold' 
								) 
						),
						// Creating a new style named "style 2"
						'style 2' => array (
								'name' => 'Red Title',
								'element' => 'h2',
								'styles' => array (
										'color' => 'Red',
										'font-weight' => 'bold',
										'text-decoration' => 'underline' 
								) 
						) 
				) 
		);
		$lookups_logo = $this->cms_mdl->get_lookup_position ();
		$data ['lookup__logo'] = array ();
		
		for($i = 0; $i < count ( $lookups_logo ); $i ++) {
			array_push ( $data ['lookup__logo'], $lookups_logo [$i] );
		}
		
		$data ['page'] = 'content_item_frm';
		
		$this->load->view ( 'body', $data );
	}
	function edit($content_id) {
		$data ['content_item'] = $this->cms_mdl->get_content_item ( $content_id );
		
		if ((($data ['content_item'] ['content_category'] == '29') or ($data ['content_item'] ['content_category'] == '30')) and $data ['content_item'] ['content_published'] == '1') {
			
			redirect ( 'cms/edit_published/' . $content_id );
		}
		
		if (($data ['content_item'] ['content_category'] == '38') || ($data ['content_item'] ['content_category'] == '40')) { // edito or key data
			$r = $this->pbf_mdl->get_edito_translation ( $data ['content_item'] ['content_id'], $this->config->item ( 'language_abbr' ) );
			$data ['html_block'] = $r ['html_block'];
		}
		
		$this->add ( $data );
	}
	function edit_published($content_id) {
		$data ['content_item'] = $this->cms_mdl->get_content_item ( $content_id );
		
		if (($data ['content_item'] ['content_category'] == '38') || ($data ['content_item'] ['content_category'] == '40')) { // edito or key data
			$r = $this->pbf_mdl->get_edito_translation ( $data ['content_item'] ['content_id'], $this->config->item ( 'language_abbr' ) );
			$data ['html_block'] = $r ['html_block'];
		}
		
		$this->add ( $data );
	}
	function save() {
		$content = $this->input->post ();
		
		$this->load->library ( 'form_validation' );
		
		$this->form_validation->set_rules ( 'content_title', 'Title', 'trim|required' );
		
		if (isset ( $content ['content_description'] ))
			$this->form_validation->set_rules ( 'content_description', 'Description', 'trim|required' );
		
		if ($this->form_validation->run () == FALSE) {
			
			$data ['content_item'] = $content;
			$this->add ( $data );
		} else {
			
			unset ( $content ['submit'] );
			
			$content ['content_published'] = ! isset ( $content ['content_published'] ) ? 0 : 1;
			$content ['content_featured'] = ! isset ( $content ['content_featured'] ) ? 0 : 1;
			$content ['content_author'] = $this->session->userdata ( 'user_id' );
			$content ['content_category'] = $this->session->userdata ( 'item_category_id' );
			$content ['content_params'] = json_encode ( $content ['content_params'] );
			
			if (empty ( $content ['content_id'] )) {
				$content ['content_create_date'] = date ( 'Y-m-d h:i:s' );
			}
			
			if (isset ( $content ['html_block'] )) {
				$html ['html_block'] = $content ['html_block'];
				unset ( $content ['html_block'] );
				$html ['content_id'] = $content ['content_id'];
				$html ['language'] = $this->config->item ( 'language_abbr' );
			}
			
			// if new content : Save the article, get the last id, generate new filename, update the file in db
			
			if ($this->cms_mdl->save_item ( $content, $html )) 

			{
				
				// Upload picture
				if (isset ( $_FILES ['content_link'] )) {
					
					if (file_exists ( $_FILES ['content_link'] ['tmp_name'] )) {
						
						$config ['file_field_name'] = 'content_link';
						$config ['file_name'] = $this->pbf->Slug ( $content ['content_title'] );
						if ($this->session->userdata ( 'item_category_id' ) == 30) {
							$config ['upload_path'] = FCPATH . 'cside/contents/docs/';
							$config ['allowed_types'] = 'doc|docx|pdf|xls|xlsx';
							$up_content ['content_id'] = $this->cms_mdl->get_last_content ();
						} else {
							
							if (empty ( $content ['content_id'] )) { // Ajout d'un nouveau contenu
							                                   
								// Recuperation du dernier element enregistre
								
								$up_content ['content_id'] = $this->cms_mdl->get_last_content ();
								$picture_name = $up_content ['content_id'] . "_" . $content ['content_title'];
							} else { // Modification de l'element existant
								
								$picture_name = $content ['content_id'] . "_" . $content ['content_title'];
								$up_content ['content_id'] = $content ['content_id'];
							}
							
							$picture_name = $this->pbf->Slug ( $picture_name );
							
							$config ['upload_path'] = FCPATH . 'cside/contents/images/';
							$config ['allowed_types'] = 'jpg|gif|png';
							$config ['file_name'] = $picture_name . "_or";
						}
						
						$config ['overwrite'] = TRUE;
						$config ['remove_spaces'] = TRUE;
						$config ['max_filename'] = '0';
						$config ['max_size'] = '0'; // use the system limit... see php.ini config in regards
						$config ['max_width'] = '0'; // should be 360 at destination
						$config ['max_height'] = '0'; // should be 300 at destination
						
						$this->load->library ( 'upload', $config );
						
						if (! $this->upload->do_upload ( $config ['file_field_name'] )) {
							$error = array (
									'error' => $this->upload->display_errors () 
							);
							
							// $this->load->view('upload_form', $error);
							$this->add ( $content );
						} else {
							$data = $this->upload->data ();
							// $this->load->view('upload_success', $data);
							
							$up_content ['content_link'] = $data ['file_name']; // may file_name is enough
							$up_content ['content_link'] = $picture_name;
						}
						
						// Don't resize images for documents and important links
						// 30 : Documents //TODO hard corded. Improve this
						// 32 : Important links (Parteners need custom images treatement) // TODO same as 30. Improve this
						if ($this->session->userdata ( 'item_category_id' ) != 30 && $this->session->userdata ( 'item_category_id' ) != 32) { // whene we have a picture
							
							$up_content ['content_link'] = $picture_name;
							
							$thumb_size = $this->config->item ( 'image_thumb_size' );
							$medium_size = $this->config->item ( 'image_medium_size' );
							$big_size = $this->config->item ( 'image_big_size' );
							
							$image_data = $this->upload->data ();
							$config_thumb = array (
									'source_image' => $image_data ['full_path'],
									'new_image' => FCPATH . 'cside/contents/images/' . $picture_name . "_thumb.jpg",
									'maintain_ratio' => true,
									'width' => $thumb_size,
									'height' => $thumb_size 
							);
							
							$config_medium = array (
									'source_image' => $image_data ['full_path'],
									'new_image' => FCPATH . 'cside/contents/images/' . $picture_name . "_med.jpg",
									'maintain_ration' => true,
									'width' => $medium_size,
									'height' => $medium_size 
							);
							
							$config_big = array (
									'source_image' => $image_data ['full_path'],
									'new_image' => FCPATH . 'cside/contents/images/' . $picture_name . "_big.jpg",
									'maintain_ration' => true,
									'width' => $big_size,
									'height' => $big_size 
							);
							
							$this->load->library ( 'image_lib' );
							$this->image_lib->initialize ( $config_thumb );
							
							if (! $this->image_lib->resize ()) {
								die ( $this->image_lib->display_errors () );
							}
							
							$this->image_lib->clear ();
							
							$this->image_lib->initialize ( $config_medium );
							
							if (! $this->image_lib->resize ()) {
								die ( $this->image_lib->display_errors () );
							}
							
							$this->image_lib->clear ();
							
							$this->image_lib->initialize ( $config_big );
							
							if (! $this->image_lib->resize ()) {
								die ( $this->image_lib->display_errors () );
							}
							
							$html = array ();
							if ($up_content ['content_link'])
								$this->cms_mdl->save_item ( $up_content, $html ); // Enregistrer le nom de l'image dans la BD
						} else {
							
							if ($this->session->userdata ( 'item_category_id' ) == 32) {
								// custom resizing and image transformation for Important links
								// transform the image into gray scale
								$image_data = $this->upload->data ();
								$config = array (
										'source_image' => $image_data ['full_path'],
										'new_image' => FCPATH . 'cside/contents/images/' . $picture_name . $image_data ['file_ext'],
										'maintain_ration' => true,
										'height' => 64 
								);
								
								$this->load->library ( 'image_lib' );
								$this->image_lib->initialize ( $config );
								
								if (! $this->image_lib->resize ()) {
									die ( $this->image_lib->display_errors () );
								}
								
								// create a copy of the image with a gray transform
								if ($image_data ['file_ext'] == '.png') {
									$im = imagecreatefrompng ( $config ['new_image'] );
								} else {
									$im = imagecreatefromjpeg ( $config ['new_image'] );
								}
								imagefilter ( $im, IMG_FILTER_GRAYSCALE );
								imagepng ( $im, FCPATH . 'cside/contents/images/' . $picture_name . '_gray' . $image_data ['file_ext'] );
								
								$html = array ();
								
								$this->cms_mdl->save_item ( $up_content, $html ); // Enregistrer le nom de l'image dans la BD
							} else {
								$up_content ['content_link'] = $data ['file_name'];
								$html = array ();
								
								$this->cms_mdl->save_item ( $up_content, $html ); // Enregistrer le nom de l'image dans la BD
							}
						}
					}
				}
				
				$this->session->set_flashdata ( array (
						'mod_clss' => 'errormsg',
						'mod_msg' => $this->lang->line ( 'cms_save_error' ) 
				) );
			}
			$this->pbf->set_eventlog ( '', 0 );
			redirect ( 'cms/cms/' . $this->session->userdata ( 'item_category_id' ) );
		}
	}
	function setfeature($content_id, $state) {
		if ($this->cms_mdl->setfeature ( $content_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'cms_save_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'cms_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'cms/cms/' . $this->session->userdata ( 'item_category_id' ) );
	}
	function setpublish($content_id, $state) {
		if ($this->cms_mdl->setpublish ( $content_id, $state )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'cms_save_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'cms_save_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		if ($state == 1) {
			redirect ( 'cms/items/' );
		} else {
			redirect ( 'cms/cms/' . $this->session->userdata ( 'item_category_id' ) );
		}
	}
	function del($content_id) {
		if ($this->cms_mdl->del_content ( $content_id )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'cms_delete_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'cms_delete_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'cms/cms/' . $this->session->userdata ( 'item_category_id' ) );
	}
	function delete_selected_acc() {
		if ($this->cms_mdl->delete_selected_acc ( $this->input->post ( 'item' ) )) {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'success',
					'mod_msg' => $this->lang->line ( 'cms_delete_select_success' ) 
			) );
		} else {
			$this->session->set_flashdata ( array (
					'mod_clss' => 'errormsg',
					'mod_msg' => $this->lang->line ( 'cms_delete_select_error' ) 
			) );
		}
		$this->pbf->set_eventlog ( '', 0 );
		redirect ( 'cms/cms/' . $this->session->userdata ( 'item_category_id' ) );
	}
}