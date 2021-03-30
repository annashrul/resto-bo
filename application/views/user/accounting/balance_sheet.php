
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
					<div class="col-sm-1">
						<label class="control-label text-left">Multi</label><br/>
						<input name="multi" id="multi" onclick="multi_periode()" onchange="multi_periode()" type="checkbox" value="1" <?=set_value('multi')?'checked':''?> ></input>
					</div>
					<div id="periode_awal" class="col-sm-2">
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
					</div>
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
							<?php if(isset($_POST['tgl_akhir']) && $_POST['tgl_akhir']!=null){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); } ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<tr><td><b>ASSETS</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> CURRENT ASSETS</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Cash and Bank</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Cash</b></td><td></td><td class="no-print"></td></tr>
									<?php $cash_bank = 0; $account = $this->m_accounting->plbs_account('group', 'cash');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Bank</b></td><td></td><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'bank'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Other Cash and Bank</b></td><td></td><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'other_cash_bank'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Deposit</b></td><td></td><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'deposit'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php } ?>
									<?php } ?>
									
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Cash and Bank</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($cash_bank)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Account Receivable</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Account Receivable</b></td><td></td><td class="no-print"></td></tr>
									<?php $ar = 0; $account = $this->m_accounting->plbs_account('group', 'ar');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ar = $ar + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Account Receivable</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($ar)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Inventory</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Inventory</b></td><td></td><td class="no-print"></td></tr>
									<?php $inventory = 0; $account = $this->m_accounting->plbs_account('group', 'inventory');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $inventory = $inventory + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Inventory</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($inventory)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Other Current Assets</b></td><td></td><td class="no-print"></td></tr>
									<?php $other = 0; $account = $this->m_accounting->plbs_account('group', 'oca');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other = $other + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Prepaid</b></td><td></td><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'prepaid'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other = $other + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Other Current Assets</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($other)?></b></td><td class="no-print"></td></tr>
									
									<?php $current_assets = $cash_bank + $ar + $inventory + $other; ?>
									<tr style="background:#66cc66;"><td><b><?=$spasi?> Total CURRENT ASSETS</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($current_assets)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> FIXED ASSETS</b></td><td></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Historical Value</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Fixed Assets</b></td><td></td><td class="no-print"></td></tr>
									<?php $history = 0; $account = $this->m_accounting->plbs_account('group', 'fixed_assets');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $history = $history + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Historical Value</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($history)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Accumulated Depreciation</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Accumulated Depreciation</b></td><td></td><td class="no-print"></td></tr>
									<?php $accum = 0; $account = $this->m_accounting->plbs_account('group', 'depreciation');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $accum = $accum + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Accumulated Depreciation</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($accum)?></b></td><td class="no-print"></td></tr>
									
									<?php $fixed_assets = $history - $accum; ?>
									<tr style="background:#66cc66;"><td><b><?=$spasi?> Total FIXED ASSETS</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($fixed_assets)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> OTHER ASSETS</b></td><td></td><td class="no-print"></td></tr>
									<?php $other_assets = 0; ?>
									<tr style="background:#66cc66;"><td><b><?=$spasi?> Total OTHER ASSETS</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($other_assets)?></b></td><td class="no-print"></td></tr>
									
									<?php $total_assets = $current_assets + $fixed_assets + $other_assets; ?>
									<tr style="background:#00cc00;"><td><b>Total ASSETS</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($total_assets)?></b></td><td class="no-print"></td></tr>
									
									
									<tr><td><b>LIABILITIES and EQUITIES</b></td><td></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> LIABILITIES</b></td><td></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Current Liabilities</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Account Payables</b></td><td></td><td class="no-print"></td></tr>
									<?php $ap = 0; $account = $this->m_accounting->plbs_account('group', 'ap');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ap = $ap + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi.$spasi?> Total Account Payables</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($ap)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi.$spasi?> Other Current Liabilities</b></td><td></td><td class="no-print"></td></tr>
									<?php $ocl = 0; $account = $this->m_accounting->plbs_account('group', 'ocl');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ocl = $ocl + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi.$spasi?> Total Other Current Liabilities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($ocl)?></b></td><td class="no-print"></td></tr>
									
									<?php $current_liabilities = $ap + $ocl; ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Current Liabilities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($current_liabilities)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Long Term Liabilities</b></td><td></td><td class="no-print"></td></tr>
									<?php $longterm_liabilities = 0; ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Long Term Liabilities</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($longterm_liabilities)?></b></td><td class="no-print"></td></tr>
									
									<?php $liabilities = $current_liabilities + $longterm_liabilities; ?>
									<tr style="background:#66cc66;"><td><b><?=$spasi?> Total LIABILITIES</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($liabilities)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> EQUITIES</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Capital</b></td><td></td><td class="no-print"></td></tr>
									<?php $capital = 0; $account = $this->m_accounting->plbs_account('group', 'capital');
									foreach($balance_sheet as $row){ ?>
										<?php //if($row['group_id'] == $account && $row['coa_id'] != 90104){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<?php if($row['coa_id']=='3104'){ $ending = $ending + $this->m_accounting->net_profit_pl(null, (substr($periode, 0, 4)>=substr(date('Y'), 0, 4) )?date('Y-m-d', strtotime('-1 day', strtotime(date('Y').'-01-01'))):date('Y-m-d', strtotime('-1 day', strtotime(substr($periode, 0, 4).'-01-01'))) ); } ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $capital = $capital + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr>
										<?php $net_profit = $this->m_accounting->net_profit_pl(null, ($periode>date('Y').'-01-01')?array(date('Y').'-01-01', $periode):array(substr($periode, 0, 4).'-01-01', $periode)); ?>
										<td><?=$spasi.$spasi?> Current Earning of The Year</td>
										<td style="text-align:right;"><?=$this->cart->format_number($net_profit)?></td>	
										<td class="no-print"></td>
									</tr>
									
									<?php $capital = $capital + $net_profit; ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi.$spasi?> Total Capital</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($capital)?></b></td><td class="no-print"></td></tr>
									
									<?php $equities = $capital + 0; ?>
									<tr style="background:#66cc66;"><td><b><?=$spasi?> Total EQUITIES</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($equities)?></b></td><td class="no-print"></td></tr>		
									
									<?php $liab_equit = $liabilities + $equities; ?>
									<tr style="background:#00cc00;"><td><b>Total LIABILITIES and EQUITIES</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($liab_equit)?></b></td><td class="no-print"></td></tr>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->


<?=set_value('multi')?'<script>$("#periode_awal").show()</script>':'<script>$("#periode_awal").hide()</script>'?>
<script>
function multi_periode(){
	if($("#multi").is(":checked")){
		$("#periode_awal").show();
	} else {
		$("#periode_awal").hide();
	}
}
</script>

