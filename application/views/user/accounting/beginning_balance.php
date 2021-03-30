<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"><?=$title?></h3>
			</div>
			<div class="box-body">
			<?=form_open($content)?>
				<button type="submit" name="fix" class="btn btn-primary" onclick="return confirm('Are you sure fix beginning balance? Can`t edit after fix.');">Fix Beginning Balance</button>
				<table id="example2" class="table table-bordered table-striped">
					<thead>
						<tr><th>No</th><th>Account Category</th><th>Account Group</th><th>Account Code</th><th>Name</th>
						<th>Balance</th><th>Beginning Balance</th><th>Exchange</th><th>Action</th></tr>
					</thead>
					<tbody>
					<?php $no = 0; $debit = 0; $credit = 0; 
					foreach($beginning_balance as $row){ $no++; ?>
						<tr>
							<td><?=$no?></td>
							<td><?=$row['kategori_id'].' - '.$row['nama_kategori']?></td>
							<td><?=$row['group_id'].' - '.$row['nama_group']?></td>
							<td><?=$row['coa_id']?></td>
							<td><?=$row['nama_coa']?></td>
							<td><?=$row['balance']=='D'?'Debit':'Credit'?></td>
							<?php if($this->m_website->get_lokasi()!='all'){ $beginning = $this->m_crud->get_data($table, 'balance, rate, status', "coa = '".$row['coa_id']."' and lokasi='".$this->m_website->get_lokasi()."'"); }
							else { $beginning = $beginning = $this->m_crud->get_data($table, 'balance, rate, status', "coa = '".$row['coa_id']."' and lokasi='".$this->m_website->get_lokasi()."'"); } ?>
							<td style="text-align:right;"><?= $this->cart->format_number($beginning['balance']) ?></td>
							<td style="text-align:right;"><?= $this->cart->format_number($beginning['rate']) ?></td>
							<td>
								<button type="button" class="btn <?=($beginning['status']==0&&$this->m_website->get_lokasi()!='all')?'btn-primary':'btn-secondary'?> right" data-toggle="modal" <?=($beginning['status']==0&&$this->m_website->get_lokasi()!='all')?'data-target="#'.$row['coa_id'].'"':''?> ><i class="fa fa-edit"></i> Edit</button>
							</td>
						</tr>
						<?php if($row['balance'] == 'D'){ $debit = $debit + $beginning['balance']; } else { $credit = $credit + $beginning['balance']; } ?>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td rowspan="2" colspan="5" style="vertical-align:middle;"><center><b>Total</b></center></td>
							<td><b>Debit</b></td>
							<td style="text-align:right;"><b><?= $this->cart->format_number($debit) ?></b></td>
							<td><b></b></td>
							<td><b></b></td>
						</tr>
						<tr>
							<td><b>Credit</b></td>
							<td style="text-align:right;"><b><?= $this->cart->format_number($credit) ?></b></td>
							<td><b></b></td>
							<td><b></b></td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden" name="debit" value="<?=$debit?>" />
				<input type="hidden" name="credit" value="<?=$credit?>" />
			<?=form_close()?>
			</div>
		</div>
	</div>
</div>

<?php $i = 0; foreach($beginning_balance as $row){ $i++; ?>
<div class="modal fade" id="<?=$row['coa_id']?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		<?= form_open_multipart($content); ?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?=$title?></h4>
			</div>
			<div class="modal-body">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Account Category</th><th>Account Group</th><th>Account Code</th><th>Name</th>
							<th>Beginning Balance</th><th>Rate</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?=$row['kategori_id'].' - '.$row['nama_kategori']?></td>
							<td><?=$row['group_id'].' - '.$row['nama_group']?></td>
							<td><?=$row['coa_id']?></td>
							<td><?=$row['nama_coa']?></td>
							<input type="hidden" name="coa" value="<?=$row['coa_id']?>" />
							<?php $beginning = $this->m_crud->get_data($table, 'balance, rate', "coa = '".$row['coa_id']."' and lokasi = '".$this->m_website->get_lokasi()."'");?>
							<td><input type="number" step="any" class="form-control" name="balance" placeholder="IDR" value="<?=$beginning['balance']?>" required /></td>
							<td><input type="number" step="any" class="form-control" name="exchange" value="<?=$beginning['rate']?>" required /></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="submit" name="save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
			</div>
		<?= form_close() ?>
		</div>
	</div>
</div>
<?php } ?>

