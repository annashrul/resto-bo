 <!--
  BODY TAG OPTIONS:
  =================
  Apply one or more of the following classes to get the
  desired effect
  |---------------------------------------------------------|
  | SKINS         | skin-blue                               |
  |               | skin-black                              |
  |               | skin-purple                             |
  |               | skin-yellow                             |
  |               | skin-red                                |
  |               | skin-green                              |
  |---------------------------------------------------------|
  |LAYOUT OPTIONS | fixed                                   |
  |               | layout-boxed                            |
  |               | layout-top-nav                          |
  |               | sidebar-collapse                        |
  |               | sidebar-mini                            |
  |---------------------------------------------------------|
  -->
  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">
	<?php include "content-navtoggle.php" ?>
	<?php include "content-nav.php" ?>

	<div class="content-wrapper">
	<?php include "content-top.php" ?>
        
		<section class="content">
		<?php $cnt = $content.'.php'; ?>
        <?php include $cnt; ?>

        </section>
      
	 </div>

      <?php include "content-bot.php" ?>
	  <?php include "content-controltoggle.php" ?>

    </div>