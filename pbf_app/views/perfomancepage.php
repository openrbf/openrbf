<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><a href=<?php echo $this->config->item('base_url')?> data><?php echo $this->lang->line('front_breadcrumb_data');?></a></li>
                <?php
																if (! isset ( $zone1 )) {
																	echo "<li class=\"active\">" . $this->lang->line('front_breadcrumb_payment') . "</li>";
																} else {
																	if (! isset ( $zone2 )) {
																		echo "<li><a href=" . $this->config->item ( 'base_url' ) . "data/payment/" . $class . ">" . $this->lang->line('front_breadcrumb_payment') . "</a></li>";
																		echo "<li class=\"active\">" . $zone1 . "</li>";
																	} else {
																		echo "<li><a href=" . $this->config->item ( 'base_url' ) . "data/payment/" . $class . ">" . $this->lang->line('front_breadcrumb_payment') . "</a></li>";
																		echo "<li><a href=" . $this->config->item ( 'base_url' ) . "data/payment/" . $class . "/" . $zone2id . ">" . $zone2 . "</a></li>";
																		echo "<li class=\"active\">" . $zone1 . "</li>";
																	}
																}
																?>
            </ul>
		</nav>
		<h1><?php
		echo $indicator_title;
		echo ! empty ( $zone1 ) ? ' : ' . $zone1 : '';
		?>
        </h1>
		<div class="report-a">
            <?php
												
if (count ( $pbf_data ) > 1) {
													?>
            <div class="table-a box table-striped table-hover">
                <?php
													$tmpl = array (
															'table_open' => '<table class="table right-align-table table-striped table-hover table-condensed" style="margin:0em">',
															'table_close' => '</table>' 
													);
													$this->table->set_template ( $tmpl );
													echo $this->table->generate ( $pbf_data );
													?>
            </div>
            <?php } ?>
        </div>
	</div>
</section>
