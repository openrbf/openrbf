<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="X-UA-Compatible" content="IE=7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language"
	content="<?php echo $this->config->item('language_abbr');?>" />
<title><?php echo strtoupper($this->lang->line($this->config->item('app_title')));?></title>
<link rel="shortcut icon"
	href="<?php echo $this->config->item('base_url');?>cside/images/icons/favicon.ico">
	<link rel="stylesheet"
		href="<?php echo $this->config->item('base_url');?>cside/css/style.css">
		<link rel="stylesheet"
			href="<?php echo $this->config->item('base_url');?>cside/css/jquery.wysiwyg.css">
			<link rel="stylesheet"
				href="<?php echo $this->config->item('base_url');?>cside/css/south-street/jquery-ui.css">
				<link rel="stylesheet"
					href="<?php echo $this->config->item('base_url');?>cside/css/jquery.multiselect.css">
					<link rel="stylesheet"
						href="<?php echo $this->config->item('base_url');?>cside/css/facebox.css">
						<link rel="stylesheet"
							href="<?php echo $this->config->item('base_url');?>cside/css/visualize.css">
							<link rel="stylesheet"
								href="<?php echo $this->config->item('base_url');?>cside/css/date_input.css">
								<link rel="stylesheet"
									href="<?php echo $this->config->item('base_url');?>cside/css/pbf-style.css">
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/jquery.min.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/jquery-ui.min.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/jquery.multiselect.min.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/jquery.chained.mini.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/cascade_drop_down.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/jshashtable.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/jquery.numberformatter.min.js"></script>
									<script type="text/javascript"
										src="<?php echo $this->config->item('base_url');?>cside/js/highcharts.js"></script>

									<script type="text/javascript">
		//Contrôle du formulaire d'enregistrement ou de modification d'un utilisateur
		function controlFormOnSubmit(theForm){
			var reason = "";

			var idUser=theForm.user_id.value;
			reason=controlFullName(theForm.user_fullname);
			reason+=controlFullName(theForm.user_name);

			//Verification du champ mot de passe lors de l'enregistrement
				if(idUser==""){
			reason+=controlFullName(theForm.user_pwd);
			reason+=controlFullName(theForm.user_pwd_conf);
				}
			reason+=controlFullName(theForm.usergroup_id);
			reason+=controlFullName(theForm.geozones);
			if(reason != ""){
				alert("<?php echo $this->lang->line('acl_control_form_error');?>"+ reason);
				return false;
			}
		}

		function controlFullName(fld){
			var error="";

			if(fld.value==""){
				error="\b";
			}
			return error;
		}

		//Contrôle du formulaire d'enregistrement ou de modification d'une entité
		 function controlFormEntity(theForm){
			var reason = "";

			reason=controlRegion(theForm.entity_geozone_id, theForm.entity_id);
				if(reason != ""){
				alert("<?php echo $this->lang->line('entity_control_form_error');?>"+ reason);
				return false;
			}
		  }

		function controlRegion(fld1, fld2){
			var error="";
			if(fld1.value=="" || fld1.value==""){
				error="\b";
			}
			return error;
		}
	</script>

</head>





<body>

	<div>

		<div class="wrapper">
			<!-- wrapper begins -->

			<div id="topheader">
				<div class="floatleft" id="fbrlogo">
    <?php
				if (file_exists ( FCPATH . 'cside/images/portal/' . $this->config->item ( 'app_logo' ) )) {
					?>
    <a href="<?php echo base_url();?>"><img height="65px"
						src="<?php echo $this->config->item('base_url').'cside/images/portal/'.$this->config->item('app_logo');?>"
						hspace="10"></a>
    <?php
				}
				?>
    </div>
				<div class="floatleft">
					<H1><?php echo strtoupper($this->lang->line($this->config->item('app_title')));?></H1>
					<h3><?php echo ucwords($this->lang->line($this->config->item('app_sub_title')));?></h3>
				</div>
			</div>
			<div class="clearfix" align="right">
				<div id="langbar"><?php echo ($this->config->item('show_lang_selector'))?$this->load->view('language_selector'):'';?></div>
			</div>
			<div class="clearall"></div>
			
<?php

$this->load->view ( 'mgmt_menu' );

if ($page != 'list' && $page != 'login_frm') {
	
	echo ($this->session->flashdata ( 'mod_clss' ) ? '<div class="message ' . $this->session->flashdata ( 'mod_clss' ) . '" style="display: block;"><p>'.
				$this->session->flashdata('mod_msg').
				'</p></div>':'').
				
			(isset($mod_clss)?'<div class="message '.$mod_clss.'" style="display: block;"><p>'.$mod_msg.'</p></div>':'');
		
}
?>	