<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Acl_mdl extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
		
	function get_users($num = 0, $filters){
		
		$record_set=array();
		
		$sql_append=" WHERE 1=1";
		$join_append="";
		
		if(!empty($filters['geozone_id'])){
		$join_append .= " LEFT JOIN pbf_usersgeozones ON (pbf_usersgeozones.user_id=pbf_users.user_id)";
		$sql_append .=" AND pbf_usersgeozones.geozone_id='".$filters['geozone_id']."' ";	
			}
			
		if(!empty($filters['user_fullname'])){
			
			$sql_append.= " AND (user_fullname LIKE '%".trim($filters['user_fullname'])."%' OR user_name  LIKE '%".trim($filters['user_fullname'])."%' OR user_jobtitle LIKE '%".trim($filters['user_fullname'])."%') ";
			
			}
			
		if(!empty($filters['usergroup_id'])){
			
			$join_append .= " LEFT JOIN pbf_usersgroupsmap ON (pbf_usersgroupsmap.user_id=pbf_users.user_id)";
			$sql_append .=" AND pbf_usersgroupsmap.usergroup_id='".$filters['usergroup_id']."' ";
			
			}
			
		$usergeozones = $this->session->userdata('usergeozones');
		
		if(!empty($usergeozones)){
			
			$join_append .= " LEFT JOIN pbf_usersgeozones ON (pbf_usersgeozones.user_id=pbf_users.user_id)";
			$sql_append .=" AND pbf_usersgeozones.geozone_id IN (".implode(',',$usergeozones).") ";	
			
			}

		$sql = "SELECT pbf_users.user_id, user_fullname, user_name, user_jobtitle, user_phonenumber, user_active, user_published FROM pbf_users ".$join_append.$sql_append." ORDER BY user_fullname";
		
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
				
		return $record_set;
		}
		
		function get_users_restricted($num = 0, $filters, $zones, $group){
		
		 $record_set=array();
		
		 $sql_append=" WHERE 1=1";
		 $join_append="";
		                
		 $group = $this->pbf->group_access_order($group);

		 
		 if(!empty($filters['user_fullname'])){
		  	
		  $sql_append.= " AND (user_fullname LIKE '%".trim($filters['user_fullname'])."%' OR user_name  LIKE '%".trim($filters['user_fullname'])."%' OR user_jobtitle LIKE '%".trim($filters['user_fullname'])."%') ";
		  	
		 }

		 if(!empty($filters['geozone_id'])){
		  $join_append .= " LEFT JOIN pbf_usersgeozones ON (pbf_usersgeozones.user_id=pbf_users.user_id)";
		  $sql_append .=" AND pbf_usersgeozones.geozone_id='".$filters['geozone_id']."' ";
		 }else {
		 
		 if(!empty($zones)){
		   
		  $join_append .= " LEFT JOIN pbf_usersgeozones ON (pbf_usersgeozones.user_id=pbf_users.user_id)";
		  $sql_append .=" AND pbf_usersgeozones.geozone_id IN (".implode(',',$zones).") ";
		   
		 }
		 }
		 	
		 if(!empty($filters['usergroup_id'])){
		  	
		  $join_append .= " LEFT JOIN pbf_usersgroupsmap ON (pbf_usersgroupsmap.user_id=pbf_users.user_id)";
		  $sql_append .=" AND pbf_usersgroupsmap.usergroup_id='".$filters['usergroup_id']."' ";
		  	
		 } else {
		 	
		 if(!empty($group)){
		  	
		  $join_append .= " LEFT JOIN pbf_usersgroupsmap ON (pbf_usersgroupsmap.user_id=pbf_users.user_id)";
		  $sql_append .=" AND pbf_usersgroupsmap.usergroup_id IN (".implode(',',$group).")";
		  	
		 }
		 }	
		
		 
		
		 $sql = "SELECT DISTINCT pbf_users.user_id, user_fullname, user_name, user_jobtitle, user_phonenumber, user_active, user_published FROM pbf_users ".$join_append.$sql_append." ORDER BY user_fullname";
		
		 $record_set['records_num']=$this->db->query($sql)->num_rows();
		
		 $sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		 $record_set['list']=$this->db->query($sql)->result_array();
		
		 return $record_set;
		}		
		
		
	function get_groups($num = 0, $filters){
		
		$record_set=array();
		$sql = "SELECT pbfusrgps.usersgroup_id,pbfusrgps.usersgroup_name,pbfusrgps.usersgroup_name,pbf_usr_gps.usersgroup_entity_associated AS inheritby,pbfusrgps.afterlogin,pbfusrgps.sortorder,pbfusrgps.isdefault,pbfusrgps.usersgroup_active FROM pbf_usersgroups AS pbfusrgps LEFT JOIN pbf_usersgroups AS pbf_usr_gps ON (pbfusrgps.usersgroup_id=pbf_usr_gps.inheritby) ORDER BY pbfusrgps.sortorder";
		$record_set['records_num'] = $this->db->query($sql)->num_rows();
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		$record_set['list'] = $this->db->query($sql)->result_array();
		return $record_set;
		}
		
	function get_tasks($num = 0, $filters){
		
		$record_set=array();
		
		$sql_append=" WHERE 1=1";
		
		if(!empty($filters['usertask_name'])){
			
			$sql_append.= " AND (usertask_name LIKE '%".trim($filters['usertask_name'])."%' OR usertask_description  LIKE '%".trim($filters['usertask_name'])."%') ";
			
			}
		
		$sql="SELECT usertask_id, usertask_name, usertask_description FROM pbf_userstasks ".$sql_append." ORDER BY usertask_name";
		
		$record_set['records_num']=$this->db->query($sql)->num_rows();
		
		$sql .= " LIMIT $num , ".$this->config->item('rec_per_page');
		
		$record_set['list']=$this->db->query($sql)->result_array();
		
		return $record_set;
		}
		
	function get_rules($usersgroup_id){
		
		$sql="SELECT pbf_usersgroupsrules.usersgroupsrule_id,pbf_userstasks.usertask_id,pbf_userstasks.usertask_name,pbf_userstasks.usertask_description FROM pbf_userstasks LEFT JOIN pbf_usersgroupsrules ON (pbf_userstasks.usertask_id = pbf_usersgroupsrules.userstask_id AND pbf_usersgroupsrules.usersgroup_id='".$usersgroup_id."') ORDER BY pbf_userstasks.usertask_name";		
		
		return $this->db->query($sql)->result_array();
		}
		
	function save_user($user_profile, $usergeozone, $usergroup_id, $user_profile_key){
		
		$user_profile_id = $user_profile['user_id'];
		$usersgroupsmap['usergroup_id'] = $usergroup_id;
		$usersgroupsmap['user_id'] = $user_profile['user_id'];
		
		$this->db->delete('pbf_usersgeozones', array('user_id' => $user_profile_id));
		$this->db->delete('pbf_usersgroupsmap', array('user_id' => $user_profile_id));
		
		if(empty($user_profile['user_id'])){
			
			$headersaved = $this->db->insert('pbf_users', $user_profile);
			
			$user_profile_id=$this->db->insert_id();
			$usersgroupsmap['user_id'] = $user_profile_id;
			
			}
		else{
			
			$headersaved = $this->db->update('pbf_users', $user_profile, array('user_id' => $user_profile['user_id']));
			
			}
	  if(empty($usergeozone)) {
	
	    $detailesaved = true;
	  }else {
		foreach($usergeozone as $k => $v){
			
			if(($v['geozone_id'] != '') && (!is_null($v['geozone_id']))){
				$array_val[$user_profile_key] = $v;
				$array_val['user_id'] = $user_profile_id;
				$detailesaved = $this->db->insert('pbf_usersgeozones', $array_val);
				}
			
			}
	  }
		
		//commet the user group
		$this->db->insert('pbf_usersgroupsmap', $usersgroupsmap);
		
		if($detailesaved && $headersaved){
			return true;
			}
		else{
			return false;
			}
		
		}
		
	function save_profile($user_profile){
		
		return $this->db->update('pbf_users', $user_profile, array('user_id' => $user_profile['user_id']));
		
		}
		
	function set_user_state($user_id, $state){
		
		$sql="UPDATE pbf_users SET user_active='".$state."' WHERE user_id='".$user_id."'";
		return $this->db->simple_query($sql);
		}
		
	function set_gp_state($usersgroup_id, $state){
		
		$sql="UPDATE pbf_usersgroups SET usersgroup_active='".$state."' WHERE usersgroup_id='".$usersgroup_id."'";
		return $this->db->simple_query($sql);
		}
		
	function set_user_publish($user_id, $state){
		
		$sql="UPDATE pbf_users SET user_published='".$state."' WHERE user_id='".$user_id."'";
		return $this->db->simple_query($sql);
		}
		
	function set_default($usersgroup_id, $state){
		
		$this->db->simple_query("UPDATE pbf_usersgroups SET isdefault='0'"); 
		return $this->db->simple_query("UPDATE pbf_usersgroups SET isdefault='".$state."' WHERE usersgroup_id='".$usersgroup_id."'");
		}
		
	function save_group($groups){
			
                
                if($groups['isdefault']==1){
                    $this->db->simple_query("UPDATE pbf_usersgroups SET isdefault='0'"); 
                }
		
		$usersgroup_id = $groups['usersgroup_id'];
		
		if(empty($groups['usersgroup_id'])){
		$this->db->insert('pbf_usersgroups', $groups);
		$usersgroup_id = $this->db->insert_id();
		}
		else{
		$this->db->update('pbf_usersgroups', $groups, array('usersgroup_id' => $usersgroup_id));
		}
		
		$this->pbf->set_translation(array(	'text' => $groups['usersgroup_name'],
											'text_key' => 'acl_group_key_'.$usersgroup_id) , 'acl');	
											
		return $usersgroup_id;
        }
		
	function save_task($task){
		if(empty($task['usertask_id'])){
		return $this->db->insert('pbf_userstasks', $task);
		}
		else{
		return $this->db->update('pbf_userstasks', $task, array('usertask_id' => $task['usertask_id']));
		}
		}
		
	function save_rules($rules){
		
		$this->db->delete('pbf_usersgroupsrules', array('usersgroup_id' => $rules['usersgroup_id']));
		
		foreach($rules['userstask_id'] as $userstask){
			
			$this->db->insert('pbf_usersgroupsrules', array(
   															'usersgroup_id' => $rules['usersgroup_id'],
   															'userstask_id' => $userstask
															));
			
			}
		return 1;
				
		}
		
	function get_acc($user_id){
		
		//$sql = "SELECT pbf_users.user_id, pbf_users.user_fullname, pbf_users.user_name, pbf_users.user_jobtitle, pbf_users.user_phonenumber, pbf_users.user_active, pbf_users.user_published, pbf_usersgroupsmap.usergroup_id FROM pbf_users LEFT JOIN pbf_usersgroupsmap ON ( pbf_usersgroupsmap.user_id = pbf_users.user_id ) WHERE pbf_users.user_id = '".$user_id."'";
		$sql = "SELECT pbf_users.user_id, pbf_users.user_fullname, pbf_users.user_name, pbf_users.user_jobtitle, pbf_users.user_phonenumber, pbf_users.user_active, pbf_users.user_published, pbf_users.user_entity, pbf_usersgroupsmap.usergroup_id FROM pbf_users LEFT JOIN pbf_usersgroupsmap ON ( pbf_usersgroupsmap.user_id = pbf_users.user_id ) WHERE pbf_users.user_id = '".$user_id."'";
		return $this->db->query($sql)->row_array();
		}
		
	function get_acc_geozones($user_id){
		
		$sql = "SELECT geozone_id AS usergeozone FROM pbf_usersgeozones WHERE user_id='".$user_id."'";
		
		$raw_geozones = $this->db->query($sql)->result_array();
		
		 $active_geozones=array();
		
		foreach($raw_geozones as $key)
  			{
					$active_geozones[] = $key['usergeozone'];
  			}
  		return $active_geozones;
		
		}
		
	function get_group($usersgroup_id){
		return $this->db->get_where('pbf_usersgroups',array('usersgroup_id'=>$usersgroup_id))->row_array();
		}
		
	function get_task($usertask_id){
		return $this->db->get_where('pbf_userstasks',array('usertask_id'=>$usertask_id))->row_array();
		}
		
	function get_rule($usersgroupsrule_id){
		return $this->db->get_where('pbf_usersgroupsrules',array('usersgroupsrule_id'=>$usersgroupsrule_id))->row_array();
		}
		
	function del_user($user_id){
		
		$affected_tables = array('pbf_usersgroupsmap', 'pbf_usersgeozones', 'pbf_users');
		$this->db->where('user_id', $user_id);
		$this->db->delete($affected_tables);	
		return 1; // fucken buggy CodeIgniter	
		}
		
	function delete_selected_acc($user_ids){
		
		$affected_tables = array('pbf_usersgroupsmap', 'pbf_usersgeozones', 'pbf_users');
		$this->db->where_in('user_id', $user_ids);
		$this->db->delete($affected_tables);
		return 1; // fucken buggy CodeIgniter
		
		}
		
	function del_group($usersgroup_id){
		return $this->db->delete('pbf_usersgroups', array('usersgroup_id' => $usersgroup_id));
		}
		
	function del_task($usertask_id){
		return $this->db->delete('pbf_userstasks', array('usertask_id' => $usertask_id));
		}
		
	function del_rule($usersgroupsrule_id){
		return $this->db->delete('pbf_usersgroupsrules', array('usersgroupsrule_id' => $usersgroupsrule_id));
		}
	
}