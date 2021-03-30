
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">General Journal</h3>
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
										<th>No</th><th>Transaction</th><th>Description</th><th>Date</th><th>Code</th><th>Account Name</th>
										<th>Debit</th><th>Credit</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; $debit = 0; $credit = 0; 
								foreach($general_journal as $row){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$row['id_trx']?></td>
										<td><?=$row['descrip']?></td>
										<td><?=$row['tanggal']?></td>
										<td><?=$row['coa']?></td>
										<td><?= $this->m_crud->get_data('coa', 'nama', "coa_id = '".$row['coa']."'")['nama'] ?></td>
										<td style="text-align:right;"><?=$this->cart->format_number($row['debit'])?></td>
										<td style="text-align:right;"><?=$this->cart->format_number($row['credit'])?></td>
									</tr>
									<?php $debit = $debit + $row['debit']; $credit = $credit + $row['credit']; ?>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6"><b>Grand Total</b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($debit)?></b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($credit)?></b></td>
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

