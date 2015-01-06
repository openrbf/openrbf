<?php
$this->load->view('front_header',$front_main_nav);

$this->load->view($page);

$this->load->view('front_footer',$front_main_nav,$logo, $featured_links, $featured_accounts);
