<?php
    function news_publish_helper() {
		$ci = &get_instance();
		$ci->lang->load('cms', $ci->config->item('language'));
		$sql="SELECT pbf_content_news.content_title as title,SUBSTR(pbf_content_news.content_create_date,1,10) as date,pbf_content_news.content_category as category,pbf_users.user_fullname as name from pbf_content_news LEFT JOIN pbf_users ON (pbf_users.user_id=pbf_content_news.content_author) WHERE pbf_content_news.content_published='0' AND (pbf_content_news.content_category='29' OR pbf_content_news.content_category='30') order BY pbf_content_news.content_create_date DESC LIMIT 0,5";
		$general_cms_news = $ci->db->query($sql)->result_array();
		$rows=$ci->db->query($sql)->num_rows();
        if ($rows>0) {		
			$html= '<div class="block"><div class="block_head">
								<h2>'.strtoupper($ci->lang->line('cms_to_publish')).' </h2>
						</div>		<!-- .block_head ends -->
				
							<div class="block_content">
			
							<table >
									<thead>
										<tr>
											<th class="header" style="width:60%">'.$ci->lang->line('cms_article_title') .'</th>
											<th class="header" style="width:25%">'.$ci->lang->line('data_author') .'</th>
											<th class="header" style="width:15%">'.$ci->lang->line('cms_date_created') .'</th>
										</tr>
									</thead>
									<tbody>';	
	
		
		foreach($general_cms_news as $news){

							$html.=	'<tr>
										<td>'.anchor('/cms/to_publish/'.$news['category'],$news['title'],'').'</td> 
										<td>'.$news['name'].'</td> 										
										<td>'.$news['date'].'</td>   
									</tr>';
	
						}

						$html.=	'</tbody>
						</table>
						
					</div>		<!-- .block_content ends -->
				<div class="bendl"></div>
				<div class="bendr"></div>
		</div>';
		
		}
		else{
		$html='';
		
}
return $html;	
}		

