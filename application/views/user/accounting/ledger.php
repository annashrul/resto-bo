
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">General Ledger</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
			<?= form_open($content); ?>
            <div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<div class="col-sm-2">
						<label class="control-label text-left">Currency</label>
						<div class="input-group">
							<?php $option = null; $option[''] = '-- Select --';
							$currency = $this->m_crud->read_data('acc_kurs_uang', '*', null, 'id_kurs_uang asc');
							foreach($currency as $row){ $option[$row['id_kurs_uang']] = $row['nama']; }
							echo form_dropdown('currency', $option, isset($_GET['currency'])?$_GET['currency']:set_value('currency'), array('class' => 'form-control select2')); ?>
							<?=form_error('currency', '<div class="error" style="color:red;">', '</div>')?>
						</div>
					</div>
					<div class="col-sm-4">
						<label class="control-label text-left">Account Name</label>
						<div class="input-group">
							<?php $option = null; $option[''] = '-- Select --';
							$coa = $this->m_crud->read_data('coa', '*', null, 'coa_id asc');
							foreach($coa as $row){ $option[$row['coa_id']] = $row['coa_id'].' - '.$row['nama']; }
							echo form_dropdown('coa', $option, isset($_GET['account'])?$_GET['account']:set_value('coa'), array('class' => 'form-control select2')); ?>
							<?=form_error('coa', '<div class="error" style="color:red;">', '</div>')?>
						</div>
					</div>
					<?php $ongoing = $this->m_accounting->ongoing_periode(); ?>
					<div class="col-sm-2">
						<label class="control-label text-left">Period</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=isset($_GET['from'])?$_GET['from']:(isset($_POST['search'])?set_value('tgl_awal'):substr($ongoing['tanggal_awal'], 0, 10))?>" ></input>
						</div>
					</div>
					<div class="col-sm-2">
						<label>To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=isset($_GET['to'])?$_GET['to']:(isset($_POST['search'])?set_value('tgl_akhir'):substr($ongoing['tanggal_akhir'], 0, 10))?>" ></input>
						</div>
					</div >
					<div class="col-lg-2" style="margin-top: 25px;">
						<button class="btn btn-primary" type="submit" name="search">Search</button>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<!--<div class="col-sm-2 text-left">
						<a href="<?=base_url().$content.'/print/'.set_value('tgl_awal').':'.set_value('tgl_akhir')?>"
							<input class="btn btn-primary col-sm-12" type="button"/>Print
						</a>
					</div>-->
					<div class="col-sm-2">
						<?php //anchor($content.'/to_excel', 'Export', array('class' => 'btn btn-primary col-sm-12'))?>
						<input class="btn btn-primary col-sm-12" type="submit" name="to_excel" value="Export" />
					</div>
				</div>
			</div>
			<?= form_close(); ?>
			<div class="box">
				<div class="row" style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<table id="example1" class="table table-bordered table-striped dataTable">
								<thead>
									<tr>
										<th>No</th><th>Transaction</th><th>Date</th><th>Description</th><th>Debit</th>
										<th>Credit</th><th>Balance</th><th class="no-print"></th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; $debit = 0; $credit = 0;  $balance = 0;
								foreach($ledger as $row){ $no++; ?>
									<?php if((isset($_GET['currency'])&&$_GET['currency']>1)||(isset($_POST['currency'])&&$_POST['currency']>1)){ $rate = $row['rate']; } else { $rate = 1; } ?>
									<?php if($this->m_accounting->coa($row['coa'], 'balance') == "D"){ $balance = $balance + (($row['debit'] - $row['credit']) / $rate); }
									else { $balance = $balance + (($row['credit'] - $row['debit']) / $rate); } ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$row['id_trx']?></td>
										<td><?=$row['tanggal']?></td>
										<td><?=$row['descrip']?></td>
										<td style="text-align:right;"><?=$this->cart->format_number($row['debit']/$rate)?></td>
										<td style="text-align:right;"><?=$this->cart->format_number($row['credit']/$rate)?></td>
										<td style="text-align:right;"><?=$this->cart->format_number($balance)?></td>
										<td class="no-print"><?=anchor($row['link_report'].'?trx='.$row['id_trx'], '<button class="btn btn-primary">Detail</button>')?></td>
									</tr>
									<?php $debit = $debit + ($row['debit']/$rate); $credit = $credit + ($row['credit']/$rate); ?>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="4"><b>Total</b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($debit)?></b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($credit)?></b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($balance)?></b></td>
										<td class="no-print"></td>
									</tr>
									<tr>
										<?php isset($_POST['coa'])?$account=$_POST['coa']:'';
										isset($_POST['tgl_awal'])?$tgl_awal=$_POST['tgl_awal']:'';
										isset($_GET['account'])?$account=$_GET['account']:'';
										isset($_GET['from'])?$tgl_awal=$_GET['from']:''; ?>
										
										<?php $beginning = 0;
										if((isset($_POST['coa'])||isset($_GET['account'])) && $this->m_accounting->coa($account, 'jenis') == 'Neraca'){ 
											if((isset($_GET['currency'])&&$_GET['currency']>1)||(isset($_POST['currency'])&&$_POST['currency']>1)){  
												$beginning = $this->m_accounting->saldo_awal_asing($account, $tgl_awal);
											} else { 
												$beginning = $this->m_accounting->saldo_awal($account, $tgl_awal); 
											}  
										} ?>
								
										<td colspan="4"><b>Beginning Balance</b></td>
										<td colspan="3"><center><b><?=$this->cart->format_number($beginning)?></b></center></td>
										<td class="no-print"></td>
									</tr>
									<tr>
										<?php $balance = 0;
										if(isset($_POST['coa'])||isset($_GET['account'])){ 
											if($this->m_accounting->coa($account, 'balance') == "D"){ $balance = $debit - $credit; }
											else { $balance = $credit - $debit; }
										} ?>
										<td colspan="4"><b>Ending Balance</b></td>
										<td colspan="3"><center><b><?=$this->cart->format_number($beginning + $balance)?></b></center></td>
										<td class="no-print"></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

