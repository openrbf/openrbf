<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><?php echo $this->lang->line('front_docs_header'); ?></li>
			</ul>
		</nav>
		<h1><?php echo $this->lang->line('front_docs_header'); ?></h1>
		<div class="report-a">
			<div class="featured-a grid-a">
				<div class="column w100 news">
            <?php foreach($list as $article): ?>
                    <article class="news-item-a">

						<h4><?php echo $article['content_title'];?></h4>
						<p class="date">
                            <?php echo mdate("%d %M  %Y",  strtotime($article['content_create_date']))?>
                        </p>
						<p><?php echo strip_tags(character_limiter(nl2br($article['content_description']), 300)); ?></p>

                        <?php if(isset($article['content_link'])) : ?>
                            <p class="action">
							<a class="button-a icon-pdf" target="_blank"
								href="<?php echo base_url().'cside/contents/docs/'.$article['content_link']; ?>">
                                    <?php echo $this->lang->line('download').' '.$article['file_size'];?>
                                </a>
						</p>
                        <?php endif;?>
                        <p class="more">
							<a
								href="<?php echo site_url('articles/item/'.$article['content_id']);?>">More</a>
						</p>
					</article>
                        
                   
            <?php endforeach;?>
                </div>
				<div class="column">
					<div class="pagination">
                    <?php echo $this->pagination->create_links();?>
                    </div>
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

