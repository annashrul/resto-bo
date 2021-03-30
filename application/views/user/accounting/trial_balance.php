
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?=$title?></h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
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
					<div class="col-sm-2">
						<label class="control-label text-left">Period</label>
						<!--<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=set_value('tgl_awal')?>" ></input>
						</div>
					</div>
					<div class="col-sm-2">
						<label>To</label>-->
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=set_value('tgl_akhir')?>" ></input>
						</div>
					</div >
					 <div class="col-lg-2" style="margin-top: 25px;">
                        <button class="btn btn-primary" type="submit" name="search">Search</button>
                    </div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px; margin-top: 25px;">
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
				<div class="row">
					<div class="col-sm-12">
						<div class="col-sm-4" style="margin-top: 10px;">
							<label class="control-label"><?=$title?></label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<table id="example2" class="table table-bordered table-striped dataTable">
								<thead>
									<tr>
										<th>No</th><th>Account Code</th><th>Account Name</th><th>Debit</th><th>Credit</th>
									</tr>
								</thead>
								<tbody>
								<?php $no = 0; $debit = 0; $credit = 0; 
								if(isset($_POST['tgl_akhir'])){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); } 
								foreach($trial_balance as $row){ $no++; ?>
									<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$row['coa_id']?></td>
										<td><?=$this->m_accounting->coa($row['coa_id'], 'nama')?></td>
										<td style="text-align:right;"><?=$row['balance']=='D'?$this->cart->format_number($ending):''?></td>
										<td style="text-align:right;"><?=$row['balance']=='C'?$this->cart->format_number($ending):''?></td>
										<!--
										<td style="text-align:right;"><?=($row['balance']=='D'&&$ending>=0)||($row['balance']=='C'&&$ending<0) ? ($ending>=0?$this->cart->format_number($ending):$this->cart->format_number($ending * -1)) : ''?></td>
										<td style="text-align:right;"><?=($row['balance']=='C'&&$ending>=0)||($row['balance']=='D'&&$ending<0) ? ($ending>=0?$this->cart->format_number($ending):$this->cart->format_number($ending * -1)) : ''?></td>
										-->
									</tr>
									<?php if($row['balance'] == 'D'){ $debit = $debit + $ending; } else { $credit = $credit + $ending; } ?>
									<?php /*if($row['balance'] == 'D'){ 
										if($ending >= 0){ $debit = $debit + $ending; } else { $credit = $credit + ($ending * -1); } 
									} else { 
										if($ending >= 0){ $credit = $credit + $ending; } else { $debit = $debit + ($ending * -1); } 
									}*/ ?>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="3"><b>Total</b></td>
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

