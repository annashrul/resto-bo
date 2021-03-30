
<!-- Main content -->
<section class="invoice">

    <!-- Default box -->
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<div class="col-sm-8">
						<?=$this->m_website->logo()?>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-11 text-left"><br/></div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<h3 class="text-center" style="margin-top: 1px;"><b>Payment Voucher</b></h3>
			</div>
		</div>
		<div class="row col-sm-7" style="margin-bottom: 10px;">
			<label>Voucher No. : </label> <?=$print['id_cash_voucher']?><br/>
			<label>Date : </label> <?=$print['tanggal']?><br/>
			<label>Description : </label> <?=$print['descrip']?><br/>
		</div>
		<div class="row col-sm-5 pull-right" style="margin-bottom: 10px;">
			<label>Currency : </label> <?=$this->m_website->kurs($print['currency'], 'nama')?><br/>
			<label>Exchange : </label> <?=$this->cart->format_number($print['rate'])?><br/>
			<label>Dibayar dari : </label> <?=$print['coa'].'-'.$this->m_accounting->coa($print['coa'], 'nama')?><br/>
			<label>Penerima : </label> <?=$print['penerima']?><br/>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body table-responsive">
						<table id="" class="table table-striped dataTable">
							<thead>
								<tr>
									<th style="border:1px solid black">No</th>
									<th style="border:1px solid black">Code</th>
									<th style="border:1px solid black">Account Name</th>
									<th style="border:1px solid black">Amount</th>
									<th style="border:1px solid black">Description</th>
								</tr>
							</thead>
							<tbody>
							<?php $no = 0; $debit = 0; $credit = 0; 
							$detail = $this->m_crud->read_data('acc_general_journal', '*', "id_trx = '".$print['id_cash_voucher']."' and coa <> '".$print['coa']."'", 'tanggal desc, id_general_journal asc');
							foreach($detail as $rows){ $no++; ?>
								<tr>
									<td style="border:1px solid black"><?=$no?></td>
									<td style="border:1px solid black"><?=$rows['coa']?></td>
									<td style="border:1px solid black"><?=$this->m_crud->get_data('coa', '*', "coa_id = '".$rows['coa']."'")['nama']?></td>
									<td style="border:1px solid black; text-align:right;"><?=$this->cart->format_number($rows['debit']/$print['rate'])?></td>
									<td style="border:1px solid black"><?=$rows['descrip']?></td>
								</tr>
								<?php $debit = $debit + $rows['debit']; ?>
							<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3" style="border:1px solid black"><center><b>Grand Total</b></center></td>
									<td style="border:1px solid black; text-align:right;"><b><?=$this->cart->format_number($debit/$print['rate'])?></b></td>
									<td style="border:1px solid black"></td>
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