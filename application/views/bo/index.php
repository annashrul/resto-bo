<!DOCTYPE html>
<html>

	<head>
    <?php $this->load->view('bo/head'); ?>
	</head>
	
    <body class="fixed-left">
		<div class="loading" id="loading" hidden>Loading&#8230;</div>
        <!-- Begin page -->
        <div id="wrapper">

            <div id="modal_otorisasi" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="modal-label">OTORISASI</h4>
                        </div>
                        <div class="row" style="margin-top: 5px">
                            <div class="row" style="margin-bottom: 10px" id="container_bayar">
                                <input type="password" class="form-control" id="password_otorisasi" name="password_otorisasi" onkeyup="hide_notif('alr_password_otorisasi');" placeholder="Password Otorisasi"> 
                                <b class="error" id="alr_password_otorisasi"></b>
                            </div>
							<div class="row" style="margin-bottom: 10px" id="container_bayar">
                                <input type="text" class="form-control" id="keterangan_otorisasi" name="keterangan_otorisasi" onkeyup="hide_notif('alr_keterangan_otorisasi');" placeholder="Keterangan Otorisasi"> 
                                <b class="error" id="alr_keterangan_otorisasi"></b>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <?php $this->load->view('bo/topbar'); ?>
            
			<?php
            if ($this->uri->segment(2) != 'pos_web') {
                $this->load->view('bo/side_menu');
            }
            ?>

			<?php $this->load->view('bo/'.$content); ?>
			
			<?php //$this->load->view('bo/side_bar'); ?>
			
			<?php $this->load->view('bo/footer'); ?>
			
		</div>
        <!-- END wrapper -->
		
    </body>
	
</html>

