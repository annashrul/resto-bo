
<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?=$title?></h3>
            <div class="box-tools pull-right no-print">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            <?= form_open($content); ?>
			<div class="row">
				<div class="col-sm-12">
					<div class="col-sm-2">
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
					<div class="col-lg-2 no-print" style="margin-top: 25px;">
                        <button class="btn btn-primary" type="submit" name="search">Search</button>
                    </div>
				</div>
			</div>
			<div class="row no-print" style="margin-bottom: 10px; margin-top: 25px;">
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
				<div class="row no-print">
					<div class="col-sm-12">
						<div class="col-sm-4" style="margin-top: 10px;">
							<label class="control-label"><?=$title?></label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<?php $spasi = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
							<?php if(isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir'])){ 
								$periode = array(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2016-06-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
							} else { $periode = $this->m_accounting->periode(); } ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<tr><td><b>Cash Flows from Operating Activities</b></td><td></td><td class="no-print"></td></tr>
									<?php $cash_sales = $this->m_accounting->cash_sales(null, $periode); ?>
									<tr>
										<td><?=$spasi?> Cash from Sales</td>
										<td style="text-align:right;"><?=$this->cart->format_number($cash_sales)?></td>		
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi?> Other Profit / Loss</b></td><td></td><td class="no-print"></td></tr>
									<?php $other_profit = 0; $account = $this->m_accounting->plbs_account('kategori', 'other_income');
									foreach($cash_flow as $row){ ?>
										<?php if($row['kategori_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other_profit = $other_profit + $ending; ?>
										<?php } ?>
									<?php } ?>
									<!--<tr><td>total other profit / loss</td><td><?=$other_profit?></td></tr>-->
									
									<?php $cash_cogs = $this->m_accounting->cash_cogs(null, $periode); ?>
									<tr>
										<td><?=$spasi?> Cash to COGS</td>
										<td style="text-align:right;"><?=$this->cart->format_number($cash_cogs)?></td>										
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi?> Overhead Expenses</b></td><td></td><td class="no-print"></td></tr>
									<?php $overhead = 0; $account = $this->m_accounting->plbs_account('group', 'overhead');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $overhead = $overhead + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<tr><td><b><?=$spasi?> Expense</b></td><td></td><td class="no-print"></td></tr>
									<?php $expense = 0; $account = $this->m_accounting->plbs_account('group', 'expense');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $expense = $expense + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<tr><td><b><?=$spasi?> Other Expense</b></td><td></td><td class="no-print"></td></tr>
									<?php $other_expense = 0; $account = $this->m_accounting->plbs_account('kategori', 'other_expense');
									foreach($cash_flow as $row){ ?>
										<?php if($row['kategori_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other_expense = $other_expense + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<?php $oprt_ass_liab = $cash_sales + $other_profit - $cash_cogs - $overhead - $expense - $other_expense; ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi?> Operating Profit(Loss) before changes in operating assets and liabilities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($oprt_ass_liab)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> Decrease (increase) in operating assets</b></td><td style="text-align:right;"><b></b></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Account Receivable</b></td><td style="text-align:right;"><b></b></td><td class="no-print"></td></tr>
									<?php $ar = 0; $account = $this->m_accounting->plbs_account('group', 'ar');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ar = $ar + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<tr><td><b><?=$spasi.$spasi?> Inventory</b></td><td style="text-align:right;"><b></b></td><td class="no-print"></td></tr>
									<?php $inventory = 0; $account = $this->m_accounting->plbs_account('group', 'inventory');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $inventory = $inventory + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<?php $other_ar = 0; $account = $this->m_accounting->plbs_account('group', 'oca');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other_ar = $other_ar + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<tr><td><b><?=$spasi.$spasi?> Prepaid</b></td><td style="text-align:right;"><b></b></td><td class="no-print"></td></tr>
									<?php $prepaid = 0; $account = $this->m_accounting->plbs_account('group', 'prepaid');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $prepaid = $prepaid + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<?php //$oprt_ass = -$ar + $inventory - $other_ar - $prepaid; ?>
									<?php $oprt_ass = $ar + $inventory + $other_ar + $prepaid; ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi?> Total Decrease (increase) in operating assets</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($oprt_ass)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> Increase (decrease) in operating liabilities</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Account Payable</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<?php $ap = 0; $account = $this->m_accounting->plbs_account('group', 'ap');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ap = $ap + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<tr><td><b><?=$spasi.$spasi?> Other Current Liebility</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<?php $liebility = 0; $account = $this->m_accounting->plbs_account('group', 'ocl');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $liebility = $liebility + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<?php $oprt_liab = $ap + $liebility; ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi?> Total Increase (decrease) in operating liabilities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($oprt_liab)?></b></td><td class="no-print"></td></tr>
									
									<?php $operating = $oprt_ass_liab - $oprt_ass + $oprt_liab; ?>
									<tr style="background:#66cc66;"><td><b>Net Cash (used in) / Provided by operating activities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($operating)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b>Cash Flows from Investing Activities</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Fixed Asset</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<?php $asset = 0; $account = $this->m_accounting->plbs_account('group', 'fixed_assets');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $asset = $asset + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi?> Accumulated Depreciation</b></td><td></td><td class="no-print"></td></tr>
									<?php $accum = 0; $account = $this->m_accounting->plbs_account('group', 'depreciation');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $accum = $accum + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<?php $investing = $asset - $accum; ?>
									<tr style="background:#66cc66;"><td><b>Net cash provided by / (used in) investing activities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($investing)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b>Cash Flows from financing Activities</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Capital</b></td><td style="text-align:right;"></td><td class="no-print"></td></tr>
									<?php $capital = 0; $account = $this->m_accounting->plbs_account('group', 'capital');
									foreach($cash_flow as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], (isset($_POST['tgl_akhir'])&&$_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:$periode); ?>
											<?php if($row['coa_id']=='3104'){ $ending = $ending + $this->m_accounting->net_profit_pl(null, (substr(is_array($periode)?$periode[0]:$periode, 0, 4)>=substr(date('Y'), 0, 4) )?date('Y-m-d', strtotime('-1 day', strtotime(date('Y').'-01-01'))):date('Y-m-d', strtotime('-1 day', strtotime(substr(is_array($periode)?$periode[0]:$periode, 0, 4).'-01-01'))) ); } ?>
											<tr>
												<td><?=$spasi.$spasi.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $capital = $capital + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<?php $financing = $capital; ?>
									<tr style="background:#66cc66;"><td><b>Net Cash provided by / (used in) financing activities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($financing)?></b></td><td class="no-print"></td></tr>
									
									
									<?php $net_cash_period = $operating - $investing + $financing; ?>
									<tr style="background:#00cc00;"><td><b>Net Cash provided / (used in) in this period</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($net_cash_period)?></b></td><td class="no-print"></td></tr>
									<?php $beginning_periode = $this->m_accounting->net_profit_pl(null, ((is_array($periode)?$periode[0]:$periode)>date('Y').'-01-01')?array(date('Y').'-01-01', (is_array($periode)?$periode[0]:$periode)):array(substr((is_array($periode)?$periode[0]:$periode), 0, 4).'-01-01', (is_array($periode)?$periode[0]:$periode))); ?>
									<tr style="background:#00cc00;"><td><b>Cash & equivalent at the Beginning of Period</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($beginning_periode)?></b></td><td class="no-print"></td></tr>
									<?php $end_periode = $net_cash_period + $beginning_periode; ?>
									<tr style="background:#00cc00;"><td><b>Cash & equivalent at the End of period</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($end_periode)?></b></td><td class="no-print"></td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

