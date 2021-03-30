
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?=$title?></h3>
            <div class="box-tools pull-right">
				<?php if (substr($access->access,12,1) == 1 ){ ?><button type="button" data-toggle="modal" data-target="#trx" class="btn btn-primary">Transaction Report</button><?php } ?>
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
			<?= form_open($content) ?>
            <div class="row" style="margin-bottom: 5px;">
				<div class="col-sm-12">
					<div class="col-sm-7">
						<div class="col-sm-12" style="margin-bottom: 5px;">
							<div class="col-sm-4"><label>Cash Mutation No.</label></div>
							<div class="col-sm-5">
								<?php (isset($_POST['tgl_quo'])&&$_POST['tgl_quo']!=null)? $tgl = substr($_POST['tgl_quo'], 2, 2).substr($_POST['tgl_quo'], 5, 2): $tgl = date('ym') ?>
								<?php $trx = $this->m_website->lokasi(null,'serial')."-CMT-".$tgl."-";
								$seri = (int) $this->m_crud->get_data($table, "max(substring(id_cash_mutation, ".(strlen($trx)+1).", 3)) as id", "id_cash_mutation like '".$trx."%'")['id']; 
								$seri++; $seri = str_pad($seri, 3, '0', STR_PAD_LEFT); ?>
								<input class="form-control" type="text" name="id_cm" id="id_cm" value="<?=$trx.$seri?>" readonly />
								<?=form_error('id_cm', '<div class="error" style="color:red;">', '</div>')?>
							</div>
						</div>
						<div class="col-sm-12" style="margin-bottom: 5px;">
							<label class="col-sm-4 control-label text-left">Date</label>
							<div class="col-sm-5">
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input class="form-control pull-right datepicker_back" readonly onchange="trx_number()"
									name="tgl_quo" id="tgl_quo" type="text" value="<?=set_value('tgl_quo')?set_value('tgl_quo'):date("Y-m-d")?>"></input>
								</div>
							</div>
						</div>
						<div class="col-sm-12" style="margin-bottom: 5px;">
							<label class="col-sm-4 control-label text-left">Currency</label>
							<div class="col-sm-5">
								<?php $option = null; $option[''] = '-- Select --';
								$currency = $this->m_crud->read_data('acc_kurs_uang', '*', null, 'nama asc');
								foreach($currency as $row){ $option[$row['id_kurs_uang']] = $row['nama']; }
								echo form_dropdown('currency', $option, set_value('currency'), array('class' => 'form-control select2', 'id'=>'currency', 'onchange'=>'kurs_rate()')); ?>
								<?=form_error('currency', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="col-sm-12" style="margin-bottom: 5px;">
							<label class="col-sm-4 control-label text-left">Exchange</label>
							<div class="col-sm-5">
								<input class="form-control" onchange="hitungDiskon()" onkeyup="hitungDiskon()" onclick="hitungDiskon()" type="number" step="any" name="exchange" id="exchange" value="<?=set_value('exchange')?>" />
								<?=form_error('exchange', '<div class="error" style="color:red;">', '</div>')?>
							</div>   
						</div>
					</div>
					<div class="col-sm-5">
						<div class="col-sm-12">
							<label class="col-sm-4 control-label text-left">Description</label>
							<div class="col-sm-8">
								<textarea rows="2" placeholder="Description" class="form-control" name="descrip"><?=set_value('descrip')?></textarea>
								<?=form_error('descrip', '<div class="error" style="color:red;">', '</div>')?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<div class="col-sm-2">
						<button class="btn btn-primary" type="submit" name="save"><i class="fa fa-save"></i> Save</button>
					</div>
				</div>
			</div>
			<div class="box">
				<div class="row">
					<div class="col-sm-12">
						<div class="col-sm-10" style="margin-top: 10px;">
							<label class="control-label"><?=$title?></label>
						</div>
						<div class="col-sm-2 pull-right" style="margin-top: 10px;">
							<button type="submit" class="btn btn-primary" name="tambah"><i class="fa fa-plus"></i> Add</button>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<table id="table_transaksi" class="table table-bordered table-striped dataTable">
								<thead>
									<tr>
										<th>No</th><th>Account Name</th><th>Debit</th><th>Credit</th><th>Description</th><th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php $option = null; $option[''] = '-- Select --';
									$coa = $this->m_crud->read_data('coa', '*', null, 'coa_id asc');
									foreach($coa as $row){ $option[$row['coa_id']] = $row['coa_id'].' - '.$row['nama']; } ?>
									<?php $i = 0; $debit = 0; $credit = 0; ?> 
									<?php if($this->cart->contents()){ ?>
										<?php foreach($this->cart->contents() as $items){ $i++; ?>
										<tr>
											<td><?=$i?></td>
											<td><?=form_dropdown('coa'.$i, $option, $items['name'], array('class' => 'form-control select2', 'required'=>'required'))?></td>
											<td><input onkeyup="total_balance()" onclick="total_balance()" class="form-control" type="number" step="any" placeholder="Debit" name="debit<?=$i?>" id="debit<?=$i?>" value="<?=$items['debit']?>"/></td>
											<td><input onkeyup="total_balance()" onclick="total_balance()" class="form-control" type="number" step="any" placeholder="Credit" name="credit<?=$i?>" id="credit<?=$i?>" value="<?=$items['credit']?>"/></td>
											<td><textarea rows="1" placeholder="Description" class="form-control" name="descrip<?=$i?>"><?=$items['descrip']?></textarea></td>
											<td><button class="btn btn-danger" type="submit" name="remove[<?=$i?>]" value="<?=$items['rowid']?>"><i class="fa fa-trash"></i> Remove</button></td>
										</tr>
										<?php (set_value('debit'.$i)!=null)?$d=set_value('debit'.$i):$d=$items['debit']; $debit = $debit + $d; ?>
										<?php (set_value('credit'.$i)!=null)?$c=set_value('credit'.$i):$c=$items['credit']; $credit = $credit + $c; ?>
										<?php } ?>
										<tr>
											<?php $i++; ?>
											<td><?=$i?></td>
											<td><?=form_dropdown('coa'.$i, $option, set_value('coa'.$i), array('class' => 'form-control select2', 'required'=>'required'))?></td>
											<td><input onkeyup="total_balance()" onclick="total_balance()" class="form-control" type="number" step="any" placeholder="Debit" name="debit<?=$i?>" id="debit<?=$i?>" value="<?=set_value('debit'.$i)?set_value('debit'.$i):0?>"/></td>
											<td><input onkeyup="total_balance()" onclick="total_balance()" class="form-control" type="number" step="any" placeholder="Credit" name="credit<?=$i?>" id="credit<?=$i?>" value="<?=set_value('credit'.$i)?set_value('credit'.$i):0?>"/></td>
											<td><textarea rows="1" placeholder="Description" class="form-control" name="descrip<?=$i?>"><?=set_value('descrip'.$i)?></textarea></td>
											<td></td>
										</tr>
										<?php (set_value('debit'.$i)!=null)?$d=set_value('debit'.$i):$d=0; $debit = $debit + $d; ?>
										<?php (set_value('credit'.$i)!=null)?$c=set_value('credit'.$i):$c=0; $credit = $credit + $c; ?>
									<?php } else { ?>
										<?php for($i=1;$i<=2;$i++){ ?>
											<tr>
												<td><?=$i?></td>
												<td><?=form_dropdown('coa'.$i, $option, set_value('coa'.$i), array('class' => 'form-control select2', 'required'=>'required'))?></td>
												<td><input onkeyup="total_balance()" onclick="total_balance()" class="form-control" type="number" step="any" placeholder="Debit" name="debit<?=$i?>" id="debit<?=$i?>" value="<?=set_value('debit'.$i)?set_value('debit'.$i):0?>"/></td>
												<td><input onkeyup="total_balance()" onclick="total_balance()" class="form-control" type="number" step="any" placeholder="Credit" name="credit<?=$i?>" id="credit<?=$i?>" value="<?=set_value('credit'.$i)?set_value('credit'.$i):0?>"/></td>
												<td><textarea rows="1" placeholder="Description" class="form-control" name="descrip<?=$i?>"><?=set_value('descrip'.$i)?></textarea></td>
												<td></td>
											</tr>
											<?php (set_value('debit'.$i)!=null)?$d=set_value('debit'.$i):$d=0; $debit = $debit + $d; ?>
											<?php (set_value('credit'.$i)!=null)?$c=set_value('credit'.$i):$c=0; $credit = $credit + $c; ?>
										<?php } $i=2; ?>
									<?php } ?>
									<input type="hidden" id="jumlah" name="jumlah" value="<?=$i?>" />
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2"><b>Total</b></td>
										<td style="text-align:right;"><b><input type="text" class="form-control" id="tot_debit" name="tot_debit" value="<?=$debit?>" readonly /></b></td>
										<td style="text-align:right;"><b><input type="text" class="form-control" id="tot_credit" name="tot_credit" value="<?=$credit?>" readonly /></b></td>
										<td colspan="2"></td>
									</tr>											
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?= form_close() ?>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

<!-- Modal-->
<div class="modal fade" id="trx" role="dialog">
    <div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?=$title?> Transaction</h4>
			</div>
			<div class="modal-body">
				<div class="box">
					<div class="row" style="margin-top: 5px;">
						<div class="col-xs-12">
							<div class="box-body table-responsive">
								<table id="example" class="table table-bordered table-striped dataTable">
									<thead>
										<tr>
											<th>No</th><th>Cash Mutation No.</th><th>Date</th><th>Description</th><th>Total</th><th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php $where = null; if(isset($_POST['lokasi'])&&$_POST['lokasi']!=null){ $lokasi = $_POST['lokasi']; } else { $lokasi = $this->m_website->get_lokasi(); }
										if($lokasi != 'all'){ ($where==null)?$where.="lokasi = '".$lokasi."'":$where.=" and lokasi = '".$lokasi."'"; }
										$i = 0; $transaction = $this->m_crud->read_data($table, '*, (select sum(debit) from acc_general_journal where id_trx = id_cash_mutation) as total', $where, 'tanggal desc');
										foreach($transaction as $row){ $i++; ?>
											<tr>
												<td><?=$i?></td>
												<td><?=$row['id_'.$page]?></td>
												<td><?=$row['tanggal']?></td>
												<td><?=$row['descrip']?></td>
												<td><?='Rp. '.$this->cart->format_number($row['total'])?></td>
												<td><?=anchor($content.'_report/print/?trx='.$row['id_'.$page], '<i class="fa fa-print"></i> Print', array('class'=>'btn btn-primary', 'target'=>'_blank'))?></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End Modal-->

<script>
function trx_number(){
	var table = '<?=$table?>';
	var column = 'id_cash_mutation';
	var trx = '<?=$this->m_website->lokasi(null,'serial')?>-CMT';
	var tanggal = '-' + $('#tgl_quo').val().substr(2, 2) + $('#tgl_quo').val().substr(5, 2) + '-';
	var digit_seri = 3;
	
	$.ajax({
		//type:'POST',
		url:'<?=site_url().$this->control?>/trx_number/?table=' + table + '&column=' + column + '&trx=' + trx + '&tanggal=' + tanggal + '&digit_seri=' + digit_seri,
		//data: {delete_id : id},
		success: function (data) { $('#id_cm').val(data); },
		//error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
	});
}

function kurs_rate(){
	var kurs = <?=json_encode($currency)?>;
	var rate = 0;
	for(var i=0; i<kurs.length; i++){
		if(kurs[i]['id_kurs_uang']==$('#currency').val()){ 
			rate = kurs[i]['rate'];
		}
	}
	$('#exchange').val(rate);
}

function total_balance(){
	var jumlah = $('#jumlah').val();
	var tot_debit = 0; var tot_credit = 0;
	for(var i=1; i<=jumlah; i++ ){ 
		var debit = $('#debit'+i).val(); if(debit == ''){ debit = 0; }
		var credit = $('#credit'+i).val(); if(credit == ''){ credit = 0; }
		tot_debit = tot_debit + parseFloat(debit);
		tot_credit = tot_credit + parseFloat(credit);
	} 
	$('#tot_debit').val(tot_debit);
	$('#tot_credit').val(tot_credit);
} 
</script>

