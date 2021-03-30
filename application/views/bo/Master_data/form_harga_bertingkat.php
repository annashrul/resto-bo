<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->                      
<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">

			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="pull-left page-title"><?=$title?></h4>
					<ol class="breadcrumb pull-right">
						<li><a href="<?=base_url()?>"><?=$site->title?></a></li>
						<li class="active"><?=$title?></li>
					</ol>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
						</div>
						<div class="panel-body">
							<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
							<?=form_open($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
								
								<?php $caption_harga = $this->m_crud->get_data('harga', 'hrg1, hrg2, hrg3, hrg4', "Kode = '1111'"); ?>
								<div class="form-group " style="margin-bottom:5px;">
									<label for="cname" class="control-label col-lg-2">Kode Barang</label>
									<div class="col-lg-10">
										<?php $field = 'kd_brg'; ?>
										<?php /* $option = null; $option[''] = '-- Select --';
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 10000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 20000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 30000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 40000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 50000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 60000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 70000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 80000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										$data_option = $this->m_crud->read_data('barang', 'kd_brg, nm_brg', null, 'nm_brg asc', null, 10000, 90000);
										foreach($data_option as $row){ $option[$row['kd_brg']] = $row['kd_brg'].' - '.$row['nm_brg']; }
										echo form_dropdown($field, $option, isset($master_data[$field])?$master_data[$field]:set_value($field), array('class' => 'select2', 'required' => 'required')); 
										*/ ?>
										<input class="form-control autocomplete_data" type="text" id="<?=$field?>" name="<?=$field?>" onchange="get_qty()" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label for="cemail" class="control-label col-lg-2">Qty 1</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'dari'; ?>
                                        <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=(isset($master_data[$field])?(float)$master_data[$field]:set_value($field))?>" required aria-required="true" readonly />
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label for="cemail" class="control-label col-lg-2">Qty 2</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'sampai'; ?>
                                        <input class="form-control" type="number" onkeyup="valid_qty()" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=(isset($master_data[$field])?(float)$master_data[$field]:set_value($field))?>" required aria-required="true" />
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        <b class="error" id="ntf_sampai"></b>
                                    </div>
                                </div>
								<div class="form-group " style="margin-bottom:5px;">
									<label for="cemail" class="control-label col-lg-2">Harga Jual</label>
									<div class="col-lg-10">
										<?php $field = 'harga'; ?>
										<input class="form-control" type="text" onkeydown="return isNumber(event)" onkeyup="isMoney('harga', '+')" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=(isset($master_data[$field])?(float)$master_data[$field]:set_value($field))?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
									</div>
								</div>
								
							<?=form_close()?>
						</div>
					</div>
				</div>
				
			</div> <!-- End Row -->
			
		</div> <!-- container -->
				   
	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<script>
var site = "<?=site_url()?>";

function valid_qty() {
    var dari = parseInt($("#dari").val());
    var sampai = parseInt($("#sampai").val());

    if (sampai < dari) {
        $("#ntf_sampai").text("Qty 2 harus lebih besar dari Qty 1!");
        $("#save").prop('disabled', true);
    } else if (isNaN(sampai)) {
        $("#ntf_sampai").text("Qty 2 harus lebih dari 0!");
        $("#save").prop('disabled', true);
    } else {
        hide_notif('ntf_sampai');
        $("#save").prop('disabled', false);
    }
}

function get_qty() {
    var val = $("#kd_brg").val();
    var data = val.split('|');
    $.ajax({
        url: "<?=base_url()?>master_data/get_qty_max/" + btoa(data[0]),
        type: "GET",
        success: function (res) {
            if (res == 1) {
                $("#dari").prop("readonly", false);
            } else {
                $("#dari").val(res);
            }
        }
    });
}

$(function(){
	$('.autocomplete_data').autocomplete({
		serviceUrl: site+'site/search_autocomplete/barang/kd_brg-nm_brg/kd_brg-nm_brg'
	});
});
</script>