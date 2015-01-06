<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_mdl extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
		
	function check_user_credentials($user_credentials){

		$sql = "SELECT pbf_users.user_id, pbf_users.user_fullname, pbf_users.user_name, pbf_users.user_jobtitle, pbf_users.user_phonenumber,pbf_users.user_entity,pbf_usersgroupsmap.usergroup_id,pbf_usersgroups.usersgroup_name,pbf_usersgroups.datatype_access,pbf_usersgroups.afterlogin AS afterlogin FROM pbf_users JOIN pbf_usersgroupsmap ON (pbf_usersgroupsmap.user_id=pbf_users.user_id) JOIN pbf_usersgroups ON (pbf_usersgroups.usersgroup_id=pbf_usersgroupsmap.usergroup_id) WHERE pbf_users.user_name=? AND pbf_users.user_pwd=? AND pbf_users.user_active = ?";

		return $this->db->query($sql, array($user_credentials['username'], md5($user_credentials['password']), 1))->row_array();
		
		}
	
	function get_usergeozones($user_id){
		
		$sql = "SELECT geozone_id, entity_id FROM pbf_usersgeozones WHERE user_id=?";
		
		$geozones = array();
		
		$raw_geozones = $this->db->query($sql, array($user_id))->result_array();
		
		foreach($raw_geozones as $val){
			
			$geozones[] = $val['geozone_id']; // this is actually ignoring the entity_id
			
			}
			
		return $geozones;
		
		}
		
	function get_usergrouprules($usersgroup_id){
		
		$sql = "SELECT usertask_name FROM pbf_usersgroupsrules JOIN pbf_userstasks ON (pbf_userstasks.usertask_id=pbf_usersgroupsrules.userstask_id) WHERE usersgroup_id=?";
		
		$rules = array();
		
		$raw_rules = $this->db->query($sql, array($usersgroup_id))->result_array();
		
		foreach($raw_rules as $val){
			
			$rules[] = $val['usertask_name'];
			
			}
			
		return $rules;
		
		}
	
}