<section class="content" id="content">
	<div class="section-a">
		<nav class="crumbs-a">
			<ul>
				<li><a href="<?php echo $this->config->item('base_url')?>">Home</a></li>
				<li><a href="<?php echo $this->config->item('base_url')?>articles">Articles</a></li>
				<li><?php echo character_limiter($article_item['content_title'], 70); ?></li>
			</ul>
		</nav>
		<h1><?php echo $article_item['content_title'];?></h1>
		<div class="report-a">
			<div class="featured-a grid-a">
				<div class="column w100 news">
					<article class="news-item-a" style="background: white;">
                    <?php if(isset($article_item['content_link'])) : ?>
                        
                        <figure>
                        <?php
																					
if (in_array ( $article_item ['extension'], array (
																							'pdf',
																							'doc',
																							'docx',
																							'xlsx',
																							'xls',
																							'ppt',
																							'pptx' 
																					) )) {
																						
																						?>
                            <p class="action">
								<a class="button-a icon-pdf" target="_blank"
									href="<?php echo base_url().'cside/contents/docs/'.$article_item['content_link']; ?>">
                                    <?php echo $this->lang->line('download').' '.$article_item['file_size'];?>
                                </a>
							</p>
                            
                        <?php
																					
} else {
																						// On verifie si l'image existe
																						if (file_exists ( 'cside/contents/images/' . $article_item ['content_link'] . '_med.jpg' )) {
																							?>                            
<a
								href="<?php echo base_url().'cside/contents/images/'.$article_item['content_link'].'_big.jpg';?>">
								<img
								src="<?php echo base_url().'cside/contents/images/'.$article_item['content_link'].'_med.jpg';?>"
								alt="<?php echo $article_item['content_title'];?>" />
							</a>
                        <?php
																						}
																					}
																					?>
                        </figure>
                    <?php endif; ?>
                    
                    <h4><?php echo $article_item['content_title'];?></h4>
						<p class="date">
                        <?php echo mdate("%d %M  %Y",  strtotime($article_item['content_create_date']))?>
                    </p>
						<p><?php echo $article_item['content_description']; ?></p>



					</article>

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


<!-- sidebar -->
<div class="row">
	<div class="col-md-6">

            <?php
												foreach ( $featured_news as $news_key => $news_val ) {
													unset ( $featured_news [$news_key] );
													$featured_news [] = anchor ( 'articles/item/' . $news_val ['content_id'], $news_val ['content_title'] );
												}
												?>
            <div class="panel panel-default">
			<div class="panel-heading">               
                    <?php
																				echo heading ( $this->lang->line ( 'front_news_other_titles' ), 3 );
																				?>
                </div>
			<div class="panel-body">
                    <?php
																				echo ul ( $featured_news );
																				
																				?>
                </div>
		</div>
	</div>

	<div class="col-md-6">
				       <?php
											foreach ( $featured_docs as $docs_key => $docs_val ) {
												unset ( $featured_docs [$docs_key] );
												$featured_docs [] = anchor ( base_url () . 'documents/item/' . $docs_val ['content_id'], $docs_val ['content_title'], array () );
											}
											?>
                  <div class="panel panel-default">
			<div class="panel-heading">
                            <?php
																												echo heading ( $this->lang->line ( 'front_docs_header' ), 3 );
																												?>
                      </div>
			<div class="panel-body">
                            <?php
																												echo ul ( $featured_docs );
																												
																												?>
                      </div>
		</div>
	</div>

</div>