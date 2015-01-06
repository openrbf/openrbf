<section class="content" id="content">
    <div class="section-a">
        <div class="form-signin" style="width: 60%;margin:auto;">
    <div class="panel panel-info">
        <div class="panel-heading">
           <h2 class="form-signin-heading"><?php echo $this->lang->line('frm_title');?></h2> 
        </div>
        <div class="panel-body">
            <div>
                <?php
                echo 	($this->session->flashdata('mod_clss')?'<div class="alert alert-'.
				$this->session->flashdata('mod_clss').
				'" style="display: block;"><p>'.
				$this->session->flashdata('mod_msg').
				'</p></div>':'').
                        (isset($mod_clss)?'<div class="alert alert-'.$mod_clss.'" style="display: block;"><p>'.$mod_msg.'</p></div>':'');
                ?>
            </div>
            <form class="" role="form" method="post" action="<?php echo site_url('auth/login');?>">

                <div class="form-group">
                    <label for="username"><?php echo $this->lang->line('frmlabel_email'); ?></label>
                    <input id="username" autocomplete="off" name="username" type="text" class="form-control" placeholder="Email address" autofocus>
                </div>
                <div class="form-group">
                    <label for="password"><?php echo $this->lang->line('frmlabel_password') ?></label>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Password">
                </div>

               
                <button class="btn btn-success" style="height: auto;padding-top:10px;padding-bottom: 10px; padding-left: 30px; padding-right: 30px;" type="submit"><?php echo $this->lang->line('frmbutton_login');?></button>
                  
                </p>
                 
                <p>
                    <?php
                    echo anchor('',$this->lang->line('frmlink_back_to_front_end'));
                    ?>
                </p>    
            </form>
        </div>    
        <div class="panel-footer panel-info">
            <?php
            echo $this->lang->line('frmlink_cant_login').' '.$this->config->item('app_admin_email');
            ?>
        </div>  
      </div>
    </div>  
</div>
</section>
