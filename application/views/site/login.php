<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<link rel="shortcut icon" type="icon" href="<?=$this->config->item('url').$site->fav_icon?>">

        <title><?=$title." | ".$site->title?></title>

        <!-- Base Css Files -->
        <link href="<?=base_url().'assets/'?>css/bootstrap.min.css" rel="stylesheet" />

        <!-- Font Icons -->
        <link href="<?=base_url().'assets/'?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
        <link href="<?=base_url().'assets/'?>assets/ionicon/css/ionicons.min.css" rel="stylesheet" />
        <link href="<?=base_url().'assets/'?>css/material-design-iconic-font.min.css" rel="stylesheet" />

        <!-- animate css -->
        <link href="<?=base_url().'assets/'?>css/animate.css" rel="stylesheet" />

        <!-- Waves-effect -->
        <link href="<?=base_url().'assets/'?>css/waves-effect.css" rel="stylesheet" />

        <!-- sweet alerts -->
        <link href="<?=base_url().'assets/'?>assets/sweet-alert/sweet-alert.min.css" rel="stylesheet" />

        <!-- Custom Files -->
        <link href="<?=base_url().'assets/'?>css/helper.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url().'assets/'?>css/style.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?=base_url().'assets/'?>js/modernizr.min.js"></script>
	
		<noscript>
			 <meta HTTP-EQUIV="REFRESH" content="0; url=<?=base_url().'site/nojs'?>" /> 
		</noscript>
		
		<?php if($this->session->userdata($this->site . 'isLogin')==true){ redirect('site/dashboard'); } ?>
		
	</head>
	
	<body>
		<div class="wrapper-page">
            <div class="panel panel-color panel-primary panel-pages">
                <div class="panel-heading text-center" style="background-color: white; height: 150px">
                    <img style="height: 75px;" src="<?=$this->config->item('url').$site->logo?>">
                    <!--<div class="bg-overlay"></div>-->
                    <!--<h3 class="text-center m-t-10 text-white"> Sign In to <strong>Moltran</strong> </h3>-->
                    <hr>
                </div>


                <div class="panel-body" style="margin-top: -40px">
				<?=form_open('site/log_in', array('class'=>'form-horizontal m-t-20'))?>
                    
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control input-lg " type="text" name="username" required="" placeholder="Username" autofocus />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control input-lg" type="password" name="password" required="" placeholder="Password" />
                        </div>
                    </div>

                    <!--<div class="form-group ">
                        <div class="col-xs-12">
                            <div class="checkbox checkbox-primary">
                                <input id="checkbox-signup" type="checkbox">
                                <label for="checkbox-signup">
                                    Remember me
                                </label>
                            </div>
                            
                        </div>
                    </div>-->
                    
                    <div class="form-group text-center m-t-40">
                        <div class="col-xs-12">
                            <button class="btn btn-primary btn-lg w-lg waves-effect waves-light" type="submit" name="login">Log In</button>
                        </div>
                    </div>

                    <div class="form-group m-t-30">
                        <!--<div class="col-sm-7">
                            <a href="recoverpw.html"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a>
                        </div>
                        <!--<div class="col-sm-5 text-right">
                            <a href="register.html">Create an account</a>
                        </div>-->
                    </div>
                <?=form_close()?>
                </div>

            </div>
        </div>
		
		<script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="<?=base_url().'assets/'?>js/jquery.min.js"></script>
        <script src="<?=base_url().'assets/'?>js/bootstrap.min.js"></script>
        <script src="<?=base_url().'assets/'?>js/waves.js"></script>
        <script src="<?=base_url().'assets/'?>js/wow.min.js"></script>
        <script src="<?=base_url().'assets/'?>js/jquery.nicescroll.js" type="text/javascript"></script>
        <script src="<?=base_url().'assets/'?>js/jquery.scrollTo.min.js"></script>
        <script src="<?=base_url().'assets/'?>assets/chat/moment-2.2.1.js"></script>
        <script src="<?=base_url().'assets/'?>assets/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="<?=base_url().'assets/'?>assets/jquery-detectmobile/detect.js"></script>
        <script src="<?=base_url().'assets/'?>assets/fastclick/fastclick.js"></script>
        <script src="<?=base_url().'assets/'?>assets/jquery-slimscroll/jquery.slimscroll.js"></script>
        <script src="<?=base_url().'assets/'?>assets/jquery-blockui/jquery.blockUI.js"></script>

        <!-- sweet alerts -->
        <script src="<?=base_url().'assets/'?>assets/sweet-alert/sweet-alert.min.js"></script>
        <script src="<?=base_url().'assets/'?>assets/sweet-alert/sweet-alert.init.js"></script>

        <!-- flot Chart -->
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.time.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.tooltip.min.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.resize.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.pie.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.selection.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.stack.js"></script>
        <script src="<?=base_url().'assets/'?>assets/flot-chart/jquery.flot.crosshair.js"></script>

        <!-- Counter-up -->
        <script src="<?=base_url().'assets/'?>assets/counterup/waypoints.min.js" type="text/javascript"></script>
        <script src="<?=base_url().'assets/'?>assets/counterup/jquery.counterup.min.js" type="text/javascript"></script>
        
        <!-- CUSTOM JS -->
        <script src="<?=base_url().'assets/'?>js/jquery.app.js"></script>

        <!-- Dashboard -->
        <script src="<?=base_url().'assets/'?>js/jquery.dashboard.js"></script>

        <!-- Chat -->
        <script src="<?=base_url().'assets/'?>js/jquery.chat.js"></script>

        <!-- Todo -->
        <script src="<?=base_url().'assets/'?>js/jquery.todo.js"></script>

        <script type="text/javascript">
            /* ==============================================
            Counter Up
            =============================================== */
            jQuery(document).ready(function($) {
                $('.counter').counterUp({
                    delay: 100,
                    time: 1200
                });
            });
        </script>
		
	</body>
</html>

