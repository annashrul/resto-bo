
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
            <?= form_open('accounting/balance_sheet'); ?>
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
							<?php $td = null; $spasi = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
							<?php if(isset($_POST['tgl_akhir']) && $_POST['tgl_akhir']!=null){ 
								$periode = $this->input->post('tgl_akhir'); 
							} else { $periode = $this->m_accounting->periode(); } ?>
							<?php $multi = null; if(isset($_POST['multi']) && $_POST['multi']==1){ 
								$multi = $this->m_website->multi_periode(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2016-06-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d'));
							} ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<?php if($multi != null){ ?>
										<tr>
											<td></td>
											<?php $i=0; foreach($multi as $mp){ $i++; $td .= '<td></td>'; ?><td><b><?=($i==1)?$mp[0]:$mp[1]?></b></td><?php } ?>
											<td class="no-print"></td>
										</tr>
									<?php } ?>
									<tr><td><b>ASSETS</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> CURRENT ASSETS</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Cash and Bank</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Cash</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $cash_bank[$i] = 0; }  
									$account = $this->m_accounting->plbs_account('group', 'cash');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$cash_bank[$i] = $cash_bank[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Bank</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'bank'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$cash_bank[$i] = $cash_bank[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Other Cash and Bank</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'other_cash_bank'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$cash_bank[$i] = $cash_bank[$i] + $ending;
												} ?><?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Deposit</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'deposit'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$cash_bank[$i] = $cash_bank[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Cash and Bank</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($cash_bank[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi.$spasi?> Account Receivable</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Account Receivable</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $ar[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'ar');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$ar[$i] = $ar[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Account Receivable</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($ar[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi.$spasi?> Inventory</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Inventory</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $inventory[$i] = 0; }  
									$account = $this->m_accounting->plbs_account('group', 'inventory');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$inventory[$i] = $inventory[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Inventory</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($inventory[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi.$spasi?> Other Current Assets</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $other[$i] = 0; }  
									$account = $this->m_accounting->plbs_account('group', 'oca');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$other[$i] = $other[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Prepaid</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'prepaid'); ?>
									<?php foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$other[$i] = $other[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Other Current Assets</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($other[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b><?=$spasi?> Total CURRENT ASSETS</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$current_assets[$i] = $cash_bank[$i] + $ar[$i] + $inventory[$i] + $other[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($current_assets[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi?> FIXED ASSETS</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Historical Value</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Fixed Assets</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $history[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'fixed_assets');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$history[$i] = $history[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Historical Value</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($history[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi.$spasi?> Accumulated Depreciation</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Accumulated Depreciation</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $accum[$i] = 0; }  
									$account = $this->m_accounting->plbs_account('group', 'depreciation');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$accum[$i] = $accum[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Accumulated Depreciation</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($accum[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b><?=$spasi?> Total FIXED ASSETS</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$fixed_assets[$i] = $history[$i] - $accum[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($fixed_assets[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi?> OTHER ASSETS</b></td><td></td><td class="no-print"></td></tr>
									<tr style="background:#66cc66;">
										<td><b><?=$spasi?> Total OTHER ASSETS</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$other_assets[$i] = 0;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($other_assets[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#00cc00;">
										<td><b>Total ASSETS</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$total_assets[$i] = $current_assets[$i] + $fixed_assets[$i] + $other_assets[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($total_assets[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									
									<tr><td><b>LIABILITIES and EQUITIES</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi?> LIABILITIES</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									
									<tr><td><b><?=$spasi.$spasi?> Current Liabilities</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi.$spasi?> Account Payables</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $ap[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'ap');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$ap[$i] = $ap[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi.$spasi?> Total Account Payables</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($ap[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi.$spasi.$spasi?> Other Current Liabilities</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $ocl[$i] = 0; }  
									$account = $this->m_accounting->plbs_account('group', 'ocl');
									foreach($balance_sheet as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$ocl[$i] = $ocl[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi.$spasi?> Total Other Current Liabilities</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($ocl[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Current Liabilities</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$current_liabilities[$i] = $ap[$i] + $ocl[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($current_liabilities[$i]).'</b></td>';
										} ?><td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi.$spasi?> Long Term Liabilities</b></td><td></td><td class="no-print"></td></tr>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Long Term Liabilities</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$longterm_liabilities[$i] = 0;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($longterm_liabilities[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b><?=$spasi?> Total LIABILITIES</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$liabilities[$i] = $current_liabilities[$i] + $longterm_liabilities[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($liabilities[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi?> EQUITIES</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Capital</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $capital[$i] = 0; }  
									$account = $this->m_accounting->plbs_account('group', 'capital');
									foreach($balance_sheet as $row){ ?>
										<?php //if($row['group_id'] == $account && $row['coa_id'] != 90104){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], ($i==1)?$mp[0]:$mp[1]); 
													if($row['coa_id']=='3104'){ $ending = $ending + $this->m_accounting->net_profit_pl(null, (substr(($i==1)?$mp[0]:$mp[1], 0, 4)>=substr(date('Y'), 0, 4) )?date('Y-m-d', strtotime('-1 day', strtotime(date('Y').'-01-01'))):date('Y-m-d', strtotime('-1 day', strtotime(substr(($i==1)?$mp[0]:$mp[1], 0, 4).'-01-01'))) ); } 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$capital[$i] = $capital[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=2016-06-01&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr>
										<td><?=$spasi.$spasi?> Current Earning of The Year</td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$net_profit[$i] = $this->m_accounting->net_profit_pl(null, (($i==1)?$mp[0]:$mp[1]>date('Y').'-01-01')?array(date('Y').'-01-01', ($i==1)?$mp[0]:$mp[1]):array(substr(($i==1)?$mp[0]:$mp[1], 0, 4).'-01-01', ($i==1)?$mp[0]:$mp[1]));
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($net_profit[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc99;">
										<td><b><?=$spasi.$spasi?> Total Capital</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$capital[$i] = $capital[$i] + $net_profit[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($capital[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b><?=$spasi?> Total EQUITIES</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$equities[$i] = $capital[$i] + 0;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($equities[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>		
									
									<tr style="background:#00cc00;">
										<td><b>Total LIABILITIES and EQUITIES</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$liab_equit[$i] = $liabilities[$i] + $equities[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($liab_equit[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
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

