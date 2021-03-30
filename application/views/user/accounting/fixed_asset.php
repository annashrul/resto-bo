
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
					<!--<div class="col-sm-2">
						<div class="form-group">
							<label class="control-label text-left">Search</label>
							<select class="form-control" name="pencarian"  id="pencarian">
								<option value="">-- Select --</option>
								<option value="User">Supplier Name</option>
								<option value="User">Purchase Order Number</option>
							</select>
						</div>
					</div>
					<div class="col-sm-2"  style="margin-top: 25px;">
						<input class="form-control" type="text" name="id_pencarian" id="id_pencarian" />
					</div>
					<div class="col-sm-1">
					</div>-->
					<!--<div class="col-sm-2">
						<label class="control-label text-left">Period</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=set_value('tgl_awal')?>" ></input>
						</div>
					</div>
					<div class="col-sm-2">
						<label>To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=set_value('tgl_akhir')?>" ></input>
						</div>
					</div >
					<div class="col-lg-2" style="margin-top: 25px;">
                        <button class="btn btn-primary" type="submit" name="search">Search</button>
                    </div>-->
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
					<div class="pull-right">
						<button type="button" data-toggle="modal" data-target="#0" class="btn btn-primary">Add Fixed Asset</button>
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
										<th>No</th><th>Action</th><th>Asset Number</th><th>Asset Account</th><th>Asset</th>
										<th>Acquisition Date</th><th>Qty</th><th>Acquisition Value</th><th>Estimated Life</th>
										<th>Accum Depr</th><th>Value of Benefits</th><th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php $i=0; foreach($fixed_asset as $row){ $i++; ?>
									<tr>
										<td><?=$i?></td>
										<td>
											<div class="btn-group">
												<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
												<ul class="dropdown-menu" role="menu">
													<li><div class="col-sm-12"><?php if($row['status']==1){ echo anchor($content.'/deactive/'.$row['id_fixed_asset'], '<button class="btn btn-default col-sm-12"><i class="fa fa-power-off"></i> Deactive</button>', array('onclick'=>'return confirm(\'Deactive Fixed Asset? Fixed Asset can`t Active again \');')); } ?></div></li>
													<!--<li class="divider"></li>-->
													<li><div class="col-sm-12"><?php if($row['entry'] <= 0){ echo anchor($content.'/delete/'.$row['id_fixed_asset'], '<button class="btn btn-default col-sm-12"><i class="fa fa-trash"></i> Delete</button>', array('onclick'=>'return confirm(\'Delete Fixed Asset?\');')); } ?></div></li>
													<!--<button class="btn btn-danger" onclick="hapus('coa', 'coa_id', '<?=$row['coa_id']?>')"><i class="fa fa-trash"></i> Delete</button>-->
												</ul>
											</div>
										</td>
										<td><?=$row['id_fixed_asset']?></td>
										<td><?=$this->m_accounting->coa($row['coa'],'nama')?></td>
										<td><?=$row['asset']?></td>
										<td><?=substr($row['tanggal'], 0, 10)?></td>
										<td><?=$row['qty']?></td>
										<td><?=$this->cart->format_number($row['perolehan'])?></td>
										<td><?=$row['estimasi']?> Month</td>
										<td><?=$this->cart->format_number($this->m_accounting->depr($row['qty']*$row['perolehan'], $row['estimasi'], $row['entry'])['susut'])?></td>
										<td><?=$this->cart->format_number($this->m_accounting->depr($row['qty']*$row['perolehan'], $row['estimasi'], $row['entry'], 'sisa'))?></td>
										<td><?=$row['status']==1?'<button class="btn btn-success">Active</button>':'<button class="btn btn-danger">Not Active</button>'?></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

<!-- Modal-->
<div class="modal fade" id="0" role="dialog">
    <div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?=$title?></h4>
			</div>
			<?=form_open($content)?>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="col-sm-6">
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Asset Account</label>
								<div class="col-sm-7">
									<?php $option = null; $option[''] = '-- Select --';
									$coa = $this->m_crud->read_data('coa', '*', "coa_id like '15%'", 'nama asc');
									foreach($coa as $row){ $option[$row['coa_id']] = $row['nama']; }
									echo form_dropdown('coa', $option, set_value('coa'), array('class' => 'form-control', 'required'=>'required')); ?>
									<?=form_error('coa', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Asset</label>
								<div class="col-sm-7">
									<input class="form-control" type="text" name="asset" required />
									<?=form_error('asset', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Acquisition Date</label>
								<div class="col-sm-7">
									<div class="input-group date">
										<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
										<input class="form-control datepicker_date" name="tanggal" type="text" value="<?=date("Y-m-d")?>" required />
									</div>
									<?=form_error('tanggal', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Qty</label>
								<div class="col-sm-7">
									<input class="form-control" type="number" name="qty" min="1" value="1" required />
									<?=form_error('qty', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Acquisition Value</label>
								<div class="col-sm-7">
									<input class="form-control" type="text" name="perolehan" min="0" required />
									<?=form_error('perolehan', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Number</label>
								<div class="col-sm-7">
									<?php $trx = $this->m_website->lokasi(null, 'serial')."-FA-".date('ym')."-";
									$seri = (int) $this->m_crud->get_data($table, 'max(substring(id_fixed_asset, '.(strlen($trx)+1).', 3)) as id', "id_fixed_asset like '".$trx."%'")['id'];
									$seri++; $seri = str_pad($seri, 3, '0', STR_PAD_LEFT); ?>
									<input class="form-control" type="text" name="fix_no" value="<?=$trx.$seri?>" readonly />
									<?=form_error('fix_no', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Estimated Life</label>
								<div class="col-sm-5">
									<input class="form-control" type="number" name="estimasi" min="1" required />
									<?=form_error('estimasi', '<div class="error" style="color:red;">', '</div>')?>
								</div>
								<div class="col-sm-2">Month</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Accum Depr</label>
								<div class="col-sm-7">
									<?php $option = null; $option[''] = '-- Select --';
									$coa = $this->m_crud->read_data('coa', '*', "coa_id like '16%'", 'nama asc');
									foreach($coa as $row){ $option[$row['coa_id']] = $row['nama']; }
									echo form_dropdown('accum', $option, set_value('accum'), array('class' => 'form-control', 'required'=>'required')); ?>
									<?=form_error('accum', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Depr Expense</label>
								<div class="col-sm-7">
									<?php $option = null; $option[''] = '-- Select --';
									$coa = $this->m_crud->read_data('coa', '*', "coa_id like '61%'", 'nama asc');
									foreach($coa as $row){ $option[$row['coa_id']] = $row['nama']; }
									echo form_dropdown('expense', $option, set_value('expense'), array('class' => 'form-control', 'required'=>'required')); ?>
									<?=form_error('expense', '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<!--<div class="row" style="margin-bottom:10px;">
								<label class="col-sm-4 control-label text-left">Active</label>
								<div class="col-sm-7">
									<input type="radio" name="aktif" required /> Yes
									<input type="radio" name="aktif" required /> No
								</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary pull-left" name="save"><i class="fa fa-save"></i> Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
			<?=form_close()?>
		</div>
	</div>
</div>
<!-- End Modal-->

