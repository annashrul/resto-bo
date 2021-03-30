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
							<div class="row">
								<?=form_open(strtolower($this->control) . '/' . $page . '/' . $this->uri->segment(3), array('role'=>"form", 'class'=>""))?>
									
									<?php
									$trx = $this->m_crud->get_data('kartu_stock', 'kd_trx', "keterangan = 'Retur Approval ".$report."'");
									if($trx == null){
										$seri = (int) $this->m_crud->get_data('kartu_stock', "max(substring(kd_trx, ".(strlen('AV-'.date('ymd'))+1).", 4)) as id", "kd_trx like '%".('AV-'.date('ymd').'%'.'-'.$this->m_website->lokasi('HO','serial'))."%'")['id'];
										$seri++; $seri = str_pad($seri, 4, '0', STR_PAD_LEFT);
										$trx = 'AV-'.date('ymd').$seri.'-'.$this->m_website->lokasi('HO','serial');
									} else { $trx = $trx['kd_trx']; }
									?>
									
									<?php $field = 'trx'; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$trx?>" />
									
									<div class="col-sm-3">
										<div class="form-group">
											<label>No. Retur</label>
											<?php $field = 'kd_trx'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=$report?>" readonly />	
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Barcode</label>
											<?php $field = 'barcode'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?>" autofocus />	
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
								<?=form_close()?>
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
												<th>Approval</th>
												<th>Aksi</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0; foreach($report_det as $rows){ $no++; ?>
												<tr>
													<?php $field = 'kd_brg'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['kd_brg']?>" />
													<?php $field = 'hrg_beli'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['hrg_beli']?>" />
													<?php $field = 'qty'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['qty']?>" />
													<?php $field = 'qty_approval'.$no; ?><input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['qty_approval']?>" />
													<?php $sisa_approval = $rows['qty'] - $rows['qty_approval']; ?>
													<td><?=$no?></td>
													<td><?=$rows['kd_brg']?></td>
													<td><?=$rows['barcode']?></td>
													<td><?=$rows['nm_brg']?></td>
													<td><?=(int) $rows['qty']?></td>
													<td><p id="v_qty_approval<?=$no?>"><?=(int) $rows['qty_approval']?></p></td>
													<td><?php $field = 'sisa_approval'.$no; ?><input class="form-control" type="number" <?=($sisa_approval==0)?'disabled':null?> onclick="valid_qty(<?=$no?>)" onkeyup="valid_qty(<?=$no?>)" id="<?=$field?>" name="<?=$field?>" min="-<?=(int) ($rows['qty_approval'])?>" max="<?=(int) ($sisa_approval)?>" value="<?=set_value($field)?set_value($field):($sisa_approval)?>" autofocus /></td>
													<td>
														<?php if($sisa_approval > 0){ ?><button id="approv<?=$no?>" type="button" class="btn btn-primary waves-effect waves-light m-b-5" onclick="approv(<?=$no?>)">Approval</button><?php } ?>
														<!--<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">Dropdown <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#">Action</a></li>
																<li><a href="#">Another action</a></li>
																<li><a href="#">Something else here</a></li>
																<li class="divider"></li>
																<li><a href="#">Separated link</a></li>
															</ul>
														</div>-->
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
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
function approv(no){
	var qty = parseInt($('#qty' + no).val()); if(isNaN(qty)){ qty = 0; }
	var qty_approval = parseInt($('#qty_approval' + no).val()); if(isNaN(qty_approval)){ qty_approval = 0; }
	var sisa_approval = parseInt($('#sisa_approval' + no).val()); if(isNaN(sisa_approval)){ sisa_approval = 0; }
	
	$.ajax({
		url: "<?=base_url().'site/approval_retur_cabang/'?>",
		type: "POST",
		data: {
			trx_ : $('#trx').val(),
			kd_trx_ : $('#kd_trx').val(),
			kd_brg_ : $('#kd_brg' + no).val(),
			hrg_beli_ : $('#hrg_beli' + no).val(),
			sisa_approval_ : sisa_approval, 
		},
        beforeSend: function () {
            $('#loading').show();
        },
        complete: function () {
            $("#loading").hide();
        },
		success: function(){
			if((qty - qty_approval - sisa_approval) == 0){
				$('#approv'+no).hide(); $('#sisa_approval' + no).prop('disabled', true);
			}
			$('#qty_approval' + no).val(qty_approval + sisa_approval);
			$('#sisa_approval' + no).val(qty-qty_approval-sisa_approval);
			$('#v_qty_approval' + no).text(qty_approval + sisa_approval);
			document.getElementById('sisa_approval' + no).max = (qty-qty_approval-sisa_approval);
			document.getElementById('sisa_approval' + no).min = -1 * (qty_approval+sisa_approval);
		}
	});
}

function valid_qty(no){
	var qty = parseInt($('#qty' + no).val()); if(isNaN(qty)){ qty = 0; }
	var qty_approval = parseInt($('#qty_approval' + no).val()); if(isNaN(qty_approval)){ qty_approval = 0; }
	var sisa_approval = parseInt($('#sisa_approval' + no).val()); if(isNaN(sisa_approval)){ sisa_approval = 0; }
	//alert(qty + ',' + qty_approval + ',' + sisa_approval);
	if( (sisa_approval <= (qty - qty_approval)) && (sisa_approval >= (-1 * qty_approval)) && (sisa_approval != 0)){
		$('#approv' + no).prop('disabled', false);
	} else {
		$('#approv' + no).prop('disabled', true);
	}
}
</script>

