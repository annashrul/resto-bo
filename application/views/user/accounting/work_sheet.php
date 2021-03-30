
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
            <div class="row">
				<?= form_open($content); ?>
				<div class="col-sm-12" style="margin-bottom: 10px;">
					<div class="col-sm-2">
						<label class="control-label text-left">Period</label>
						<!--<div class="input-group date">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=set_value('tgl_awal')?>" ></input>
						</div>
					</div>
					<div class="col-sm-2">
						<label>To</label>-->
						<div class="input-group date">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=set_value('tgl_akhir')?>" ></input>
						</div>
					</div >
					<div class="col-lg-2" style="margin-top: 25px;">
						<button class="btn btn-primary" type="submit" name="search">Search</button>
					</div>
				</div>
				<?= form_close(); ?>
				
				<div class="col-sm-12" style="margin-bottom: 10px;">
				</div>
			</div>
			<div class="box">
				<div class="row">
					<div class="col-sm-12">
						<div class="col-sm-4" style="margin-top: 10px;">
							<label class="control-label">Work Sheet</label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<table id="table_transaksi" class="table table-bordered table-striped dataTable">
								<thead>
									<tr>
										<th rowspan="2">No</th><th rowspan="2">Account Name</th>
										<th colspan="2">Profit & Loss</th>
										<th colspan="2">Balance Sheet</th>
										<th colspan="2">Balance</th>
										<th colspan="2">Adjustment</th>
										<th colspan="2">Adjustment Balance</th>
									</tr>
									<tr>
										<th>Debit</th><th>Credit</th>
										<th>Debit</th><th>Credit</th>
										<th>Debit</th><th>Credit</th>
										<th>Debit</th><th>Credit</th>
										<th>Debit</th><th>Credit</th>
									</tr>
								</thead>
								<tbody>	
								<?php if(isset($_POST['tgl_akhir'])){
								//if(isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir'])){ 
									$periode = array($this->input->post('tgl_awal'), $this->input->post('tgl_akhir')); 
								} else { $periode = $this->m_accounting->periode(); } ?>
								<?php $pl['D'] = 0; $pl['C'] = 0; $bs['D'] = 0; $bs['C'] = 0; $b['D'] = 0; $b['C'] = 0; $a['D'] = 0; $a['C'] = 0; $ab['D'] = 0; $ab['C'] = 0; ?>
								<?php $i = 0; foreach($work_sheet as $row){ $i++; ?>
									<tr>
										<td><?=$i?></td><td><?=$row['nama_coa']?></td>
										
										<?php if($row['jenis'] == 'L / R'){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<?php if($row['balance']=='D'){ $pl['D'] = $pl['D'] + $ending; } ?>
											<?php if($row['balance']=='C'){ $pl['C'] = $pl['C'] + $ending; } ?>
											<td style="text-align:right;"><?=$row['balance']=='D'?$this->cart->format_number($ending):''?></td>
											<td style="text-align:right;"><?=$row['balance']=='C'?$this->cart->format_number($ending):''?></td>
										<?php } else { ?> <td></td><td></td> <?php } ?>
										<?php if($row['jenis'] == 'Neraca'){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$row['jenis'] == 'Neraca')?$_POST['tgl_akhir']:$periode); ?>
											<?php if($row['balance']=='D'){ $bs['D'] = $bs['D'] + $ending; } ?>
											<?php if($row['balance']=='C'){ $bs['C'] = $bs['C'] + $ending; } ?>
											<td style="text-align:right;"><?=$row['balance']=='D'?$this->cart->format_number($ending):''?></td>
											<td style="text-align:right;"><?=$row['balance']=='C'?$this->cart->format_number($ending):''?></td>
										<?php } else { ?> <td></td><td></td> <?php } ?>
										<?php if($row['jenis'] == 'L / R' || $row['jenis'] == 'Neraca'){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$row['jenis'] == 'Neraca')?$_POST['tgl_akhir']:$periode); ?>
											<?php if($row['balance']=='D'){ $b['D'] = $b['D'] + $ending; } ?>
											<?php if($row['balance']=='C'){ $b['C'] = $b['C'] + $ending; } ?>
											<td style="text-align:right;"><?=$row['balance']=='D'?$this->cart->format_number($ending):''?></td>
											<td style="text-align:right;"><?=$row['balance']=='C'?$this->cart->format_number($ending):''?></td>
										<?php } else { ?> <td></td><td></td> <?php } ?>
										<?php if($row['jenis'] == 'L / R' || $row['jenis'] == 'Neraca'){ ?>
											<?php $adjust = $this->m_accounting->saldo_adjustment($row['coa_id'], $periode); ?>
											<?php /*if($row['balance'] == 'D'){ 
												if($adjust >= 0){ $a['D'] = $a['D'] + $adjust; } else { $a['C'] = $a['C'] + ($adjust * -1); } 
											} else { 
												if($adjust >= 0){ $a['C'] = $a['C'] + $adjust; } else { $a['D'] = $a['D'] + ($adjust * -1); } 
											}*/ ?>
											<!--
											<td style="text-align:right;"><?=($row['balance']=='D'&&$adjust>=0)||($row['balance']=='C'&&$adjust<0) ? ($adjust>=0?$this->cart->format_number($adjust):$this->cart->format_number($adjust * -1)) : ''?></td>
											<td style="text-align:right;"><?=($row['balance']=='C'&&$adjust>=0)||($row['balance']=='D'&&$adjust<0) ? ($adjust>=0?$this->cart->format_number($adjust):$this->cart->format_number($adjust * -1)) : ''?></td>
											-->
											<td style="text-align:right;"><?=$row['balance']=='D'?$this->cart->format_number($adjust):''?></td>
											<td style="text-align:right;"><?=$row['balance']=='C'?$this->cart->format_number($adjust):''?></td>
										<?php } else { ?> <td></td><td></td> <?php } ?>
										<?php if($row['jenis'] == 'L / R' || $row['jenis'] == 'Neraca'){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$row['jenis'] == 'Neraca')?$_POST['tgl_akhir']:$periode); ?>
											<?php $adjustment = $this->m_accounting->saldo_adjustment($row['coa_id'], $periode); ?>
											<?php if($row['balance']=='D'){ $ab['D'] = $ab['D'] + $ending + $adjustment; } ?>
											<?php if($row['balance']=='C'){ $ab['C'] = $ab['C'] + $ending + $adjustment; } ?>
											<td style="text-align:right;"><?=$row['balance']=='D'?$this->cart->format_number($ending + $adjustment):''?></td>
											<td style="text-align:right;"><?=$row['balance']=='C'?$this->cart->format_number($ending + $adjustment):''?></td>
										<?php } else { ?> <td></td><td></td> <?php } ?>
									</tr>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="2"><center><b></b></center></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($pl['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($pl['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($bs['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($bs['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($b['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($b['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($a['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($a['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($ab['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($ab['C']) ?></b></td>
									</tr>
									<?php $net_profit_pl = $this->m_accounting->net_profit_pl(null, $periode); ?>
									<?php $net_profit_bl = $this->m_accounting->net_profit_pl(null, (isset($_POST['tgl_akhir']))?$_POST['tgl_akhir']:$periode); ?>
									<tr>
										<td colspan="2"><center><b>Current Earning of The Year</b></center></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($net_profit_pl) ?></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($net_profit_bl) ?></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b></b></td>
										<td style="text-align:right;"><b></b></td>
									</tr>
									<tr>
										<td colspan="2"><center><b></b></center></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($pl['D'] + $net_profit_pl) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($pl['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($bs['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($bs['C'] + $net_profit_bl) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($b['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($b['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($a['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($a['C']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($ab['D']) ?></b></td>
										<td style="text-align:right;"><b><?= $this->cart->format_number($ab['C']) ?></b></td>
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

