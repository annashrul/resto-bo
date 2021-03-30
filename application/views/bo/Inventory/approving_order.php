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
                        <?=form_open(strtolower($this->control) . '/approval_order/' . $this->uri->segment(3), array('role'=>"form"))?>

                        <div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
							<div class="row">
                                <?php $field = 'trx'; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$trx?>" />

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>No. Order</label>
                                        <?php $field = 'kd_trx'; ?>
                                        <input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=$report?>" readonly />
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>No</th>
												<th>Kode Barang</th>
												<th>Barcode</th>
												<th>Nama Barang</th>
												<th>Qty</th>
												<th>Qty Approval</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0; foreach($report_det as $rows){ ?>
												<tr>
													<?php $field = 'kd_brg'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['kd_brg']?>" />
													<?php $field = 'hrg_beli'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['hrg_beli']?>" />
													<?php $field = 'hrg_jual'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['hrg_jual_1']?>" />
													<td><?=$no+1?></td>
													<td><?=$rows['kd_brg']?></td>
													<td><?=$rows['barcode']?></td>
													<td><?=$rows['nm_brg']?></td>
													<td><?=(int)$rows['qty']?></td>
													<td><?php $field = 'approval'.$no; ?><input class="form-control approval" type="number" onclick="valid_qty(<?=$no?>)" onkeyup="valid_qty(<?=$no?>)" id="<?=$field?>" name="<?=$field?>" value="<?=(int)$rows['qty']?>" autofocus /></td>
                                                </tr>
											<?php $no++; } ?>
                                        <input type="hidden" name="max_data" id="max_data" value="<?=$no?>">
										</tbody>
									</table>
								</div>
							</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <button class="btn btn-primary" onclick="return simpan_transaksi()" name="save" id="simpan" type="submit">Simpan</button>
                                        <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
                                    </div>
                                </div>
                            </div>
						</div>
                        <?=form_close()?>
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

function valid_qty(no){
	var qty = parseInt($('#approval' + no).val());
	var max_data = $("#max_data").val();
	if(isNaN(qty)){
	    qty = 0;
	}

	if(qty >= 0){
        $(".approval").prop('readonly', false);
        $('#simpan').prop('disabled', false);
	} else {
	    for (var x=0; x<max_data; x++) {
	        if (x != no) {
                $("#approval" + x).prop('readonly', true);
            }
        }
		$('#simpan').prop('disabled', true);
	}
}

function simpan_transaksi() {
    if (confirm('Akan menyimpan transaksi?')) {
        return true;
    } else {
        return false;
    }
}

function batal_transaksi() {
    window.location = '<?=base_url()?>inventory/approval_order';
}
</script>

