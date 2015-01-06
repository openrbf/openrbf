<script type="text/javascript"
	src="<?php echo site_url();?>cside/js/highcharts.js"></script>
<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><?php echo anchor(base_url().'data/element/'.$indicator['indicator_id'],strtoupper($this->lang->line('app_country_name'))) ?></li>
				<li><?php echo str_replace(' > ',' / ',$breadcrumb); ?></li>

			</ul>
		</nav>
		<h1><?php echo $indicator['indicator_title']; ?></h1>
		<div class="report-a">
			<div class="">
                <?php
																if (isset ( $indicator ['indicator_description'] ) && $indicator ['indicator_description'] != '') {
																	echo '<div><p>' . $indicator ['indicator_description'] . '</p></div>';
																}
																?>
                <div class="table-a box table-striped table-hover">
                    <?php
																				$tmpl = array (
																						'table_open' => '<table class="table right-align-table table-striped table-hovered">',
																						'table_close' => '</table>' 
																				);
																				$this->table->set_template ( $tmpl );
																				
																				echo $this->table->generate ( $pbf_data );
																				?> 
                </div>
			</div>
		</div>
	</div>
	<div>
		<div class="table-a box table-qualities-a">
			<div id="graph">
                <?php
																
																echo $quantity_chart_zone;
																
																?>
            </div>
		</div>
	</div>
</section>
