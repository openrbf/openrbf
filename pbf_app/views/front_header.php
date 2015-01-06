<!DOCTYPE html>
<html lang="<?php echo $this->config->item('language_abbr');?>">
<head>
<meta charset="utf-8">
<title><?php echo !isset($page_title)?'':$page_title.' - '.strtoupper($this->lang->line($this->config->item('app_title'))); ?></title>
<meta name="title"
	content="<?php echo isset($page_title)?$page_title:''; ?>">
<meta name="description"
	content="<?php echo isset($meta_description)?$meta_description:''; ?>">
<meta name="author" content="bluesquare">
<meta name="keywords" content="" />
<meta name="language"
	content="<?php echo $this->config->item('language_abbr');?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="stylesheet" type="text/css"
	href="<?php echo site_url();?>cside/frontend/styles/bootstrap.min.css"
	media="screen">
<link rel="stylesheet" type="text/css"
	href="<?php echo site_url();?>cside/frontend/styles/screen.css?v=1"
	media="screen" />
<link rel="stylesheet" type="text/css"
	href="<?php echo site_url();?>cside/frontend/styles/print.css?v=1"
	media="print" />
<link rel="stylesheet" type="text/css"
	href="<?php echo site_url();?>cside/frontend/styles/custom.css?v=1"
	media="screen" />
<link rel="shortcut icon"
	href="<?php echo $this->config->item('base_url');?>cside/images/icons/favicon.ico"
	type="image/x-icon" />
<script type="text/javascript"
	src="<?php echo site_url();?>cside/frontend/javascript/head.js"></script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/frontend/javascript/jquery.min.js"></script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/frontend/javascript/bootstrap.min.js"></script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/js/highcharts.js"></script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/js/jshashtable.js"></script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/js/jquery.numberformatter.min.js"></script>
<script type="text/javascript">head.js(
            '<?php echo site_url();?>cside/frontend/javascript/extras.js?v=1',
            '<?php echo site_url();?>cside/frontend/javascript/scripts.js?v=1'
    );</script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/js/highstock.js"></script>
<script type="text/javascript"
	src="<?php echo site_url();?>cside/js/exporting.js"></script>

</head>
<body>
	<div class="root-a">
		<header class="top" id="top">
			<h1 class="logo" style="margin-left: -70px">
				<a href="<?php echo $this->config->item('base_url')?>" accesskey="h">
                    <?php
																				if (file_exists ( FCPATH . 'cside/images/portal/' . $this->config->item ( 'app_logo' ) )) {
																					?>
                    <img width="60"
					src="<?php echo $this->config->item('base_url').'cside/images/portal/'.$this->config->item('app_logo');?>">
                    <?php
																				}
																				?>
                    
                    <strong><?php echo strtoupper($this->lang->line($this->config->item('app_title')));?></strong>
					<span><?php echo strtoupper($this->lang->line($this->config->item('app_sub_title')));?></span>
				</a>
			</h1>
			<nav class="skips">
				<ul>
					<li><a href="#nav" accesskey="n">Skip to navigation [n]</a></li>
					<li><a href="#content" accesskey="c">Skip to content [c]</a></li>
					<li><a href="#footer" accesskey="f">Skip to footer [f]</a></li>
				</ul>
			</nav>
			<nav class="languages-a">
				<ul>
					<!--      <li><a href="./">Fr</a></li>
                     <li class="active"><a href="./">En</a></li> -->
                       <?php echo ($this->config->item('show_lang_selector'))?$this->load->view('language_selector_front'):'';?>
                </ul>
			</nav>
			<nav class="nav" id="nav">
				<h2 class="offset">Navigation</h2>
				<ul>
                   <?php
																			$current_url = '/' . $this->router->fetch_class () . '/';
																			foreach ( $front_main_nav as $main_nav_key => $main_nav_val ) {
																				$class = ($main_nav_key == $current_url) ? 'active' : '';
																				echo '<li class="' . $class . '">' . anchor ( $main_nav_key, $main_nav_val ) . '</li>';
																			}
																			?> 
                </ul>
			</nav>

		</header>

		<!-- end Header -->