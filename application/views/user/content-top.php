<section class="content-header">
          <h1><i class="ion-ios-browsers"></i>
            <?php echo $title; ?>
            <small></small>
          </h1>
          <ol class="breadcrumb">
		  <li><a href="<?php echo base_url();?>user"><i class="fa fa-user"></i>User</a></li>
			<?php
			/*$crumbs = explode("/",$_SERVER["REQUEST_URI"]);
				if($this->uri->segment(2) == 'merchantgo'){
					unset($crumbs[0]);
					unset($crumbs[1]);
					unset($crumbs[2]);	
					unset($crumbs[4]);
				}else if($this->uri->segment(2) == 'log-transaksi'){
					unset($crumbs[0]);
					unset($crumbs[1]);
					unset($crumbs[2]);	
					$crumbs[3] = 'Histori Transaksi';
				}else{
					unset($crumbs[0]);
					unset($crumbs[1]);
					unset($crumbs[2]);	
				}
			foreach($crumbs as $crumb){
					echo "<li class='active'>".ucfirst(str_replace(array(".php","_","-"),array(""," "," "),$crumb) . ' ')."</li>";
			}*/
			echo ' &nbsp;&nbsp;>&nbsp;&nbsp; '.$title;
			?>
          </ol>
        </section>