
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?=$title?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <!--<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>-->
            </div>
        </div>
        <div class="box-body">
            <?= form_open($content); ?>
			<div class="row">
				<div class="col-sm-12">
					<?php $ongoing = $this->m_accounting->ongoing_periode(); ?>
					<div class="col-sm-2">
						<label class="control-label text-left">Period</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=isset($_POST['search'])?set_value('tgl_awal'):substr($ongoing['tanggal_awal'], 0, 10)?>" ></input>
						</div>
					</div>
					<div class="col-sm-2">
						<label>To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=isset($_POST['search'])?set_value('tgl_akhir'):substr($ongoing['tanggal_akhir'], 0, 10)?>" ></input>
						</div>
					</div >
					 <div class="col-lg-2" style="margin-top: 25px;">
                        <button class="btn btn-primary" type="submit" name="search">Search</button>
                    </div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px; margin-top: 15px;">
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
										<th>No</th><th>Action</th><th>cash Voucher No.</th><th>Date</th><th>Account</th><th>Descrip</th>
										<th>Currency</th><th>Exchange</th><th>Receiver</th><th>User</th><th>Total</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; $total = 0;
								foreach($cash_voucher_report as $row){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td>
											<div class="btn-group">
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
												<ul class="dropdown-menu" role="menu">
													<li><div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#<?=$no?>" class="btn btn-default col-sm-12"><i class="fa fa-eye"></i> Detail</button></div></li>
													<!--<li class="divider"></li>-->
													<li><div class="col-sm-12"><?=anchor($content.'/print/?trx='.$row['id_cash_voucher'], '<i class="fa fa-print"></i> Print', array('class'=>'btn btn-default col-sm-12'))?></div></li>
													<!--<button class="btn btn-danger" onclick="hapus('coa', 'coa_id', '<?=$row['coa_id']?>')"><i class="fa fa-trash"></i> Delete</button>-->
												</ul>
											</div>
										</td>
										<td><?=$row['id_cash_voucher']?></td>
										<td><?=$row['tanggal']?></td>
										<td><?=$row['coa'].'-'.$this->m_accounting->coa($row['coa'], 'nama')?></td>
										<td><?=$row['descrip']?></td>
										<td><?=$this->m_website->kurs($row['currency'], 'nama')?></td>
										<td><?=$this->cart->format_number($row['rate'])?></td>
										<td><?=$row['penerima']?></td>
										<td><?=$this->m_website->user_data($row['user_id'])->nama?></td>
										<td style="text-align:right;"><?=$this->cart->format_number($row['total']/$row['rate'])?></td>
									</tr>
									<?php $total = $total + $row['total']; ?>
								<?php } ?>
								</tbody>
								<!--<tfoot>
									<tr>
										<td colspan="7"><b>Total</b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($total)?></b></td>
										<td><b></b></td>
									</tr>											
								</tfoot>-->
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->


<?php $i = 0; $total = 0;
foreach($cash_voucher_report as $row){ $i++; ?>
<!-- Modal-->
<div class="modal fade" id="<?=$i?>" role="dialog">
    <div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?=$title?> Transaction</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="col-sm-4">
							<div class="small-box bg-blue">
								<div class="inner">
									<label class="control-label text-left">cash Voucher No.</label>
									<input class="form-control" readonly value="<?=$row['id_cash_voucher']?>"/>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="small-box bg-blue">
								<div class="inner">
									<label class="control-label text-left">Date</label>
									<input class="form-control" readonly value="<?=$row['tanggal']?>"/>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="small-box bg-blue">
								<div class="inner">
									<label class="control-label text-left">Account</label>
									<input class="form-control" readonly value="<?=$row['coa'].'-'.$this->m_accounting->coa($row['coa'], 'nama')?>"/>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="small-box bg-blue">
								<div class="inner">
									<label class="control-label text-left">Receiver</label>
									<input class="form-control" readonly value="<?=$row['penerima']?>"/>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="small-box bg-blue">
								<div class="inner">
									<label class="control-label text-left">User</label>
									<input class="form-control" readonly value="<?=$this->m_website->user_data($row['user_id'])->nama?>"/>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="small-box bg-blue">
								<div class="inner">
									<label class="control-label text-left">Description</label>
									<input class="form-control" readonly value="<?=$row['descrip']?>"/>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box">
					<div class="row" style="margin-top: 5px;">
						<div class="col-xs-12">
							<div class="box-body table-responsive">
								<table id="example1" class="table table-bordered table-striped dataTable">
									<thead>
										<tr>
											<th>No</th><th>Description</th><th>Code</th><th>Account Name</th><th>Amount</th>
										</tr>
									</thead>
									<tbody>
									<?php $no = 0; $debit = 0;
									$detail = $this->m_crud->read_data('acc_general_journal', '*', "id_trx = '".$row['id_cash_voucher']."' and coa <> '".$row['coa']."'", 'tanggal desc, id_general_journal asc');
									foreach($detail as $rows){ $no++; ?>
										<tr>
											<td><?=$no?></td>
											<td><?=$rows['descrip']?></td>
											<td><?=$rows['coa']?></td>
											<td><?=$this->m_accounting->coa($rows['coa'], 'nama')?></td>
											<td style="text-align:right;"><?=$this->cart->format_number($rows['debit']/$row['rate'])?></td>
										</tr>
										<?php $debit = $debit + $rows['debit']; ?>
									<?php } ?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="4"><b>Grand Total</b></td>
											<td style="text-align:right;"><b><?=$this->cart->format_number($debit/$row['rate'])?></b></td>
										</tr>											
									</tfoot>
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
<?php } ?>

