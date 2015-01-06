<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Cms_mdl extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	function get_featured_items($content_category, $limit) {
		$sql = "SELECT pbfcn.content_id, pbfcn.content_title, pbfcn.content_create_date, pbfcn.content_description, pbfcn.content_modif_date, pbfcn.content_link, pbfu.user_fullname FROM pbf_content_news pbfcn LEFT JOIN pbf_users pbfu ON ( pbfu.user_id = pbfcn.content_author ) WHERE pbfcn.content_featured = '1' AND pbfcn.content_published = '1' AND pbfcn.content_category = '" . $content_category . "' ORDER BY pbfcn.content_create_date DESC LIMIT 0 , " . $limit . "";
		
		if ($content_category == 32) {
			$result = $this->db->query ( $sql )->result_array ();
			
			foreach ( $result as $key => $res ) {
				
				$result [$key] ['content_link'] = file_exists ( FCPATH . 'cside/contents/images/' . $res ['content_link'] . '.png' ) ? $res ['content_link'] . '.png' : $res ['content_link'] . '.jpg';
				$images_infos = explode ( '.', $result [$key] ['content_link'] );
				
				$result [$key] ['img_gray'] = $images_infos [0] . '_gray.' . $images_infos [1];
			}
			
			return $result;
		}
		
		return $this->db->query ( $sql )->result_array ();
	}
	function get_zone_name($id) {
		$sql = "SELECT pbf_geozones.geozone_name AS zone1, pr.geozone_name AS zone2, pr.geozone_id AS zone2id FROM pbf_geozones LEFT JOIN pbf_geozones pr ON (pr.geozone_id = pbf_geozones.geozone_parentid) WHERE pbf_geozones.geozone_id='" . $id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_items($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE pbf_content_news.content_category='" . $this->session->userdata ( 'item_category_id' ) . "'";
		
		if (! empty ( $filters ['title'] )) {
			$sql_append .= " AND (pbf_content_news.content_title LIKE '%" . $filters ['title'] . "%') ";
		}
		
		if ($this->session->userdata ['usergroup_id'] == 11) {
			$sql_append .= " AND pbf_content_news.content_published=0";
		}
		
		if (! empty ( $filters ['author'] )) {
			
			$sql_append .= " AND (pbf_users.user_fullname LIKE '%" . trim ( $filters ['author'] ) . "%') ";
		}
		
		$sql = "SELECT pbf_content_news.content_id,pbf_content_news.content_title,SUBSTR(pbf_content_news.content_create_date,1,10),SUBSTR(pbf_content_news.content_modif_date,1,10),pbf_users.user_fullname,pbf_content_news.content_featured,pbf_content_news.content_published FROM pbf_content_news LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_content_news.content_author) " . $sql_append . " ORDER BY pbf_content_news.content_create_date DESC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_items_to_publish($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE pbf_content_news.content_category='" . $this->session->userdata ( 'item_category_id' ) . "' AND pbf_content_news.content_published='0'";
		
		if (! empty ( $filters ['title'] )) {
			$sql_append .= " AND (pbf_content_news.content_title LIKE '%" . $filters ['title'] . "%') ";
		}
		
		if (! empty ( $filters ['author'] )) {
			
			$sql_append .= " AND (pbf_users.user_fullname LIKE '%" . trim ( $filters ['author'] ) . "%') ";
		}
		
		$sql = "SELECT pbf_content_news.content_id,pbf_content_news.content_title,SUBSTR(pbf_content_news.content_create_date,1,10),SUBSTR(pbf_content_news.content_modif_date,1,10),pbf_users.user_fullname,pbf_content_news.content_featured ,pbf_content_news.content_published FROM pbf_content_news LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_content_news.content_author) " . $sql_append . " ORDER BY pbf_content_news.content_create_date DESC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function save_item($content, $html) {
		if (empty ( $content ['content_id'] )) {
			
			$headersaved = $this->db->insert ( 'pbf_content_news', $content );
			$content ['content_id'] = $this->db->insert_id ();
		} else {
			
			$headersaved = $this->db->update ( 'pbf_content_news', $content, array (
					'content_id' => $content ['content_id'] 
			) );
		}
		
		if (isset ( $html ['html_block'] )) {
			
			$this->cms_mdl->save_editotranslation ( $html, $content ['content_id'] );
		}
		
		if ($headersaved) {
			return true;
		} else {
			return false;
		}
	}
	function get_last_content() { // Return the last news added
		$sql = "select content_id from pbf_content_news ORDER BY content_id DESC LIMIT 1";
		$res = $this->db->query ( $sql )->row_array ();
		
		return $res ['content_id'];
	}
	function save_editotranslation($content, $id) {
		if (empty ( $content ['content_id'] )) { // add all languages....
			
			$content ['content_id'] = $id;
			foreach ( $this->config->item ( 'lang_uri_abbr' ) as $langk => $langv ) {
				
				$content ['language'] = $langk;
				
				$headersaved = $this->db->insert ( 'pbf_editotranslation', $content );
			}
		} else {
			
			$headersaved = $this->db->update ( 'pbf_editotranslation', $content, array (
					'content_id' => $content ['content_id'],
					'language' => $content ['language'] 
			) );
		}
		
		if ($headersaved) {
			return true;
		} else {
			return false;
		}
	}
	function get_content_item($content_id) {
		$sql = "SELECT content_id,content_title,content_link,content_description,content_published,content_featured,content_category, content_position, content_params FROM pbf_content_news WHERE content_id = '" . $content_id . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function setfeature($content_id, $state) {
		$sql = "UPDATE pbf_content_news SET content_featured='" . $state . "' WHERE content_id='" . $content_id . "'";
		return $this->db->simple_query ( $sql );
	}
	function setpublish($content_id, $state) {
		$sql = "UPDATE pbf_content_news SET content_published='" . $state . "' WHERE content_id='" . $content_id . "'";
		return $this->db->simple_query ( $sql );
	}
	function del_content($content_id) {
		return $this->db->delete ( 'pbf_content_news', array (
				'content_id' => $content_id 
		) ); // remember to delete associated files
	}
	function delete_selected_acc($content_ids) {
		$affected_tables = array (
				'pbf_content_news' 
		);
		$this->db->where_in ( 'content_id', $content_ids ); // remember to delete associated files
		return $this->db->delete ( $affected_tables );
	}
	function get_articles($num = 0, $filters) {
		$record_set = array ();
		
		$sql_append = " WHERE pbf_content_news.content_category='" . $this->session->userdata ( 'front_item_category_id' ) . "'";
		
		$sql = "SELECT content_id,content_title,content_description,content_create_date,content_link FROM pbf_content_news " . $sql_append . " AND pbf_content_news.content_published = '1' ORDER BY content_create_date DESC";
		
		$record_set ['records_num'] = $this->db->query ( $sql )->num_rows ();
		
		$sql .= " LIMIT $num , " . $this->config->item ( 'rec_per_page' );
		
		$record_set ['list'] = $this->db->query ( $sql )->result_array ();
		
		return $record_set;
	}
	function get_article($content_id) {
		$sql = "SELECT pbf_content_news.content_id,pbf_content_news.content_title,pbf_content_news.content_description,pbf_content_news.content_link,pbf_content_news.content_create_date,pbf_content_news.content_modif_date,pbf_users.user_fullname AS news_author FROM pbf_content_news LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_content_news.content_author) WHERE content_id = '" . $content_id . "' AND pbf_content_news.content_category='" . $this->session->userdata ( 'front_item_category_id' ) . "'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	function get_pop_tot($geo_id = 2, $geozone_id = 0) {
		$year = date ( 'Y' );
		
		$sql_append = "";
		{
			if ($geozone_id) {
				$sql_append = " AND geozone_parentid = $geozone_id";
			}
		}
		
		$sql = "SELECT SUM(geozone_pop*POWER(1+(" . ($this->config->item ( 'pop_growth_rate' ) / 100) . "),
   (" . $year . "-geozone_pop_year))) as tot FROM pbf_geozones WHERE geo_id = $geo_id $sql_append AND geozone_active = '1'";
		
		return $this->db->query ( $sql )->row_array ();
	}
	// Fonction qui permet qui retourne les lookups du lookup_linkfile logo_position
	function get_lookup_position() {
		$sql = "SELECT * FROM pbf_lookups WHERE lookup_linkfile='logo_position'";
		
		return $this->db->query ( $sql )->result_array ();
	}
	
	// function qui recuperre les logos
	function get_logo() {
		$sql = "SELECT * FROM pbf_content_news JOIN pbf_lookups ON pbf_content_news.content_position=pbf_lookups.lookup_id";
		return $this->db->query ( $sql )->result_array ();
	}
}