<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><?php echo $this->lang->line('front_about'); ?></li>
			</ul>
		</nav>
		<h1><?php echo $this->lang->line('front_about'); ?></h1>
		<div class="report-a">
			<div class="featured-a grid-a">
				<div class="column w100 news">
            <?php foreach($list as $article): ?>
                    <article class="news-item-a"
						style="background: white;">
                    <?php if(isset($article['content_link'])) : ?>
                        <figure>
							<a
								href="<?php echo base_url().'cside/contents/images/'.$article['content_link'].'_or.jpg';?>"><img
								src="<?php echo base_url().'cside/contents/images/'.$article['content_link'].'_med.jpg';?>"
								alt="<?php echo $article['content_title'];?>" /></a>
						</figure>
                    <?php endif; ?>
                    
                    <h4><?php echo $article['content_title'];?></h4>
						<p class="date">
                        <?php echo mdate("%d %M  %Y",  strtotime($article['content_create_date']))?>
                    </p>
						<p><?php echo strip_tags(character_limiter(nl2br($article['content_description']), 300)); ?></p>
						<p class="more">
							<a
								href="<?php echo site_url('articles/item/'.$article['content_id']);?>">More</a>
						</p>
					</article>
                        
                   
            <?php endforeach;?>
                </div>
				<div>
                    <?php ?>
                </div>
			</div>
		</div>


	</div>
    
    <?php if(count($featured_accounts_display)>0) : ?>
       
            
            <div class="table-a box table-qualities-a">
            <?php
					
					$tmpl = array (
							'table_open' => '<table>',
							'table_close' => '</table>' 
					);
					$this->table->set_template ( $tmpl );
					
					echo $this->table->generate ( $featured_accounts_display );
					
					?>

            </div>
        
    <?php endif;?>
</section>
