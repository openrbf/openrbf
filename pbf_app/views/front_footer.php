
<footer class="footer" id="footer">
	<div class="logos-a">

		<ul>

			<li class="pbf"><a href="http://www.openrbf.org" target="_blank"><img
					src="<?php echo site_url();?>cside/frontend/temp/logo_openrbf.png"
					alt="Openrbf Logo" /> <strong><?php echo strtoupper($this->lang->line($this->config->item('app_title')));?></strong>
					<span><?php echo strtoupper($this->lang->line($this->config->item('app_sub_title')));?></span></a></li>
			<li class="wb"><a href="http://www.bluesquare.org/" target="_blank"><span><?php echo strtoupper($this->lang->line('app_financed_by'));?></span>
					<img
					src="<?php echo site_url();?>cside/frontend/temp/logo_blsq.png"
					alt="Bluesquare Logo" /></a></li>

		</ul>
	</div>


	<div class="partners-a">
		<h3>
			<span><?php echo strtoupper($this->lang->line('app_implementing_partners'));?></span>
		</h3>
		<ul>
						<?php
						// Affichage des content_news dont le lookup_linkfile de pbf_lookups est egal à logo_position
						for($i = 0; $i < count ( $logo ); $i ++) :
							// echo "<h1>".$logo[$i]['content_link']."</h1>"."<br/>";							?>
					<li><a href="<?php echo $logo[$i]['content_description'];?>"
				target="_blank"><img
					title="<?php echo $logo[$i]['content_title']; ?>"
					src="<?php echo site_url().'cside/contents/images/'.$logo[$i]['content_link'].".png";?>"
					height="100" class="img-thumbnail"
					alt="<?php echo $logo[$i]['content_title']; ?>" /> </a></li>
							<?php endfor;?>
     
		</ul>
	</div>
	<div class="utils-a">
		<nav class="connect-a">
			<h3>Join us with social media</h3>
			<ul>
				<li class="facebook"><a
					href="#"
					target="_blank"><span>Facebook</span></a></li>
				<li class="twitter"><a href="#" target="_blank"><span>Twitter</span></a></li>
				<li class="youtube"><a
					href="#"
					target="_blank"><span>YouTube</span></a></li>
			</ul>
		</nav>
		<!--<nav class="nav-a">
                    <ul>
                        <li><a href="http://openrbf.org/" target="_blank">Open RBF</a></li>
                        <li><a href="http://www.bluesquare.org/" target="_blank">Blue Square</a></li>
                    </ul>
                </nav>-->
		<p class="copys"><?php echo $this->lang->line('app_copy_info_key');?></p>
	</div>
</footer>
</div>

</body>
</html>