
<!-- Main content -->
<section class="invoice">

    <!-- Default box -->
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<div class="col-sm-8">
						<?=$this->m_website->logo()?><br>
						<span><?=$this->m_website->address()?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-11 text-left"><br/></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<h3 class="text-center" style="margin-top: 1px;"><b>Adjustment Journal</b></h3>
			</div>
		</div>
		<div class="row col-sm-9" style="margin-bottom: 10px;">
			<label>Adjustment Journal No. : </label> <?=$print['id_trx']?><br/>
			<label>Date : </label> <?=$print['tanggal']?><br/>
		</div>
		<div class="row col-sm-3 pull-right" style="margin-bottom: 10px;">
			<label>Currency : </label> <?=$this->m_website->kurs($print['currency'], 'nama')?><br/>
			<label>Exchange : </label> <?=$this->cart->format_number($print['rate'])?><br/>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body table-responsive">
						<table id="" class="table table-striped dataTable">
							<thead>
								<tr>
									<th style="border:1px solid black">No</th>
									<th style="border:1px solid black">Description</th>
									<th style="border:1px solid black">Code</th>
									<th style="border:1px solid black">Account Name</th>
									<th style="border:1px solid black">Debit</th>
									<th style="border:1px solid black">Credit</th>
								</tr>
							</thead>
							<tbody>
							<?php $no = 0; $debit = 0; $credit = 0; 
							$detail = $this->m_crud->read_data('acc_adjustment_journal', '*', "id_trx = '".$print['id_trx']."'", 'tanggal desc, id_adjustment_journal asc');
							foreach($detail as $rows){ $no++; ?>
								<tr>
									<td style="border:1px solid black"><?=$no?></td>
									<td style="border:1px solid black"><?=$rows['descrip']?></td>
									<td style="border:1px solid black"><?=$rows['coa']?></td>
									<td style="border:1px solid black"><?=$this->m_accounting->coa($rows['coa'], 'nama')?></td>
									<td style="border:1px solid black; text-align:right;"><?=$this->cart->format_number($rows['debit']/$rows['rate'])?></td>
									<td style="border:1px solid black; text-align:right;"><?=$this->cart->format_number($rows['credit']/$rows['rate'])?></td>
								</tr>
								<?php $debit = $debit + ($rows['debit']/$rows['rate']); $credit = $credit + ($rows['credit']/$rows['rate']); ?>
							<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4" style="border:1px solid black"><center><b>Grand Total</b></center></td>
									<td style="border:1px solid black; text-align:right;"><b><?=$this->cart->format_number($debit)?></b></td>
									<td style="border:1px solid black; text-align:right;"><b><?=$this->cart->format_number($credit)?></b></td>
								</tr>											
							</tfoot>
						</table>
					</div>
				</div>
			</div>
        </div>
		<div class="row">
			<div class="col-sm-12" style="margin-top: 10px;">
				<table width="100%">
					<tr>
						<td width="20%" style="vertical-align:text-top; text-align:center;">
							<label class="control-label text-center">Disiapkan Oleh</label><br/><br/><br/>
							<?=$this->m_website->user_data($print['user_id'])->nama?>
						</td>
						<td width="20%" style="vertical-align:text-top; text-align:center;">
							<label class="control-label text-center">Diperiksa Oleh</label>					
						</td>
						<td width="20%" style="vertical-align:text-top; text-align:center;">
							<label class="control-label text-center">Disetujui Oleh</label>
						</td>
						<td width="20%" style="vertical-align:text-top; text-align:center;">
							<label class="control-label text-center">Dibayar Oleh</label>
						</td>
						<td width="20%" style="vertical-align:text-top; text-align:center;">
							<label class="control-label text-center">Diterima Oleh</label>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br/><br/><br/>
		<div class="row no-print">
		  <div class="col-xs-12">
			<!--<a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
			<button class="btn btn-primary pull-right" style="margin-right: 5px;"><i class="fa fa-download"></i> Generate PDF</button>-->
		  </div>
		</div>
	</div>
</section><!-- /.content -->