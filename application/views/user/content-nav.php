      <!-- Main Header -->
<header class="main-header">

        <!-- Logo -->
        <a href="<?php echo base_url();?>user" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>U</b>L</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>User</b>Lounge</span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
		  
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
				
				<?php if(substr($access->access,26,1) == 1){ ?>
				<script type="text/javascript">
					window.onload = function(){
						setInterval(function(){ 
						var notif = 0;
							$.ajax({
								type: "POST",
								url: "<?php echo base_url();?>user/data-alert-1",
								dataType:'json',
								success: function(response){
									if(response != 0){
										notif = notif + response;
			  							$("#notif").text(""+notif+"");
										$("#notif2").text(""+notif+"");
										document.getElementById('li-notif1').style.display = 'block';
									}else{
										$("#notif").text("");
										$("#notif2").text("");
										document.getElementById('li-notif1').style.display = 'none';
									}
								}
							});
						}, 3000);
					}
				</script>
				<?php } ?>
  
			 <!--<li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i>
                  <span id="notif" class="label label-warning"></span>
                </a>
                <ul class="dropdown-menu">
                  <!--<li class="header">You have <span id="notif2"></span> notifications</li>--
                  <li>
                    <ul class="menu">
					<?php if(substr($access->access,26,1) == 1){ ?>
                      <li id="li-notif1" style="display:none;">
                        <a href="<?php echo base_url();?>user/board-completed-approval">
                          <i class="fa fa-cog text-aqua"></i> <span id="notif2"></span> new completed repairing Boards.
                        </a>
                      </li>
					<?php } ?>
                    </ul>
                  </li>
                  <!--<li class="footer"><a href="#">View all</a></li>--
                </ul>
              </li>-->
              
              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <!-- The user image in the navbar-->
                  <img src="<?php echo base_url();?>assets/images/user-default.png" class="user-image" alt="User Image">
                  <!-- hidden-xs hides the username on small devices so only the image appears. -->
                  <span class="hidden-xs"><?= $account->nama ?> <i>(<?= $access->lvl.' - '.$this->m_crud->get_data("Lokasi", "Nama", "Kode = '".$this->m_website->get_lokasi()."'")['Nama'] ?>)</i></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- The user image in the menu -->
                  <li class="user-header">
                    <img src="<?php echo base_url();?>assets/images/user-default.png" class="img-circle" alt="User Image">
                    <p>
                      <?php echo $account->nama; ?>
                      <small><b><?php echo $account->alamat; ?></b>, <?php echo $account->email; ?></small>
                    </p>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="<?php echo base_url();?>user/profil" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="<?php echo base_url();?>site/logout" class="btn btn-default btn-flat">Log Out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
              </li>
            </ul>
          </div>
        </nav>
      </header>