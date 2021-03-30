
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
            <?= form_open('accounting/profit_loss'); ?>
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
					<div class="col-sm-1">
						<label class="control-label text-left">Multi</label><br/>
						<input name="multi" id="multi" type="checkbox" value="1" <?=set_value('multi')?'checked':''?> ></input>
					</div>
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
							<?php $td = null; $spasi = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
							<?php if(isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir'])){ 
								$periode = array(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
							} else { $periode = array(substr($ongoing['tanggal_awal'], 0, 10), substr($ongoing['tanggal_akhir'], 0, 10)); } ?>
							<?php $multi = null; if(isset($_POST['multi']) && $_POST['multi']==1){ 
								$multi = $this->m_website->multi_periode(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d'));
							} ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<?php if($multi != null){ ?>
										<tr>
											<td></td>
											<?php foreach($multi as $mp){ $td .= '<td></td>'; ?><td><b><?=$mp[1]?></b></td><?php } ?>
											<td class="no-print"></td>
										</tr>
									<?php } ?>
									<tr><td><b>OPERATING REVENUE</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Revenue</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $revenue[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'revenue');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$revenue[$i] = $revenue[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b>Total OPERATING REVENUE</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($revenue[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									<tr><td><b>Cost of Goods Sold</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Cost of Goods Sold</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $cogs[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'cogs');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$cogs[$i] = $cogs[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi?> Overhead Expenses</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $overhead[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'overhead');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$overhead[$i] = $overhead[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b>Total Cost of Goods Sold</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($cogs[$i] + $overhead[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b>GROOS PROFIT</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$groos_profit[$i] = $revenue[$i] - ($cogs[$i] + $overhead[$i]);
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($groos_profit[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b>Operating Expenses</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Expense</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $expense[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'expense');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$expense[$i] = $expense[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b>Total Operating Expense</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($expense[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b>INCOME FROM OPERATION</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$income[$i] = $groos_profit[$i] - $expense[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($income[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b>Other Income and Expenses</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Other Income</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Other Profit / Loss</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $other_income[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('kategori', 'other_income');
									foreach($profit_loss as $row){ ?>
										<?php if($row['kategori_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$other_income[$i] = $other_income[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi?>Total Other Income</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($other_income[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr><td><b><?=$spasi?> Other Expenses</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Other Expense</b></td><td></td><?=substr($td, 9)?><td class="no-print"></td></tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $other_expense[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'other_expense');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;">'.$this->cart->format_number($ending).'</td>';
													$other_expense[$i] = $other_expense[$i] + $ending;
												} ?>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;">
										<td><b><?=$spasi?>Total Other Expenses</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($other_expense[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									<tr style="background:#66cc66;">
										<td><b>Total Other Income and Expenses</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$other_inex[$i] = $other_income[$i] - $other_expense[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($other_inex[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									
									
									<tr style="background:#00cc00;">
										<td><b>NET PROFIT/LOSS (Before Tax)</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$net_profit[$i] = $income[$i] + $other_inex[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($net_profit[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $tax[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'tax_income');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr style="background:#00cc00;">
												<td><b><?='('.$row['coa_id'].') '.$row['nama_coa']?></b></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;"><b>'.$this->cart->format_number($ending).'</b></td>';
													$tax[$i] = $tax[$i] + $ending;
												} ?>
												<td class="no-print"></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#00cc00;">
										<td><b>NET PROFIT/LOSS (After Tax)</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											$net_profit[$i] = $income[$i] + $other_inex[$i];
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($net_profit[$i] - $tax[$i]).'</b></td>';
										} ?>
										<td class="no-print"></td>
									</tr>
									<?php $i=0; foreach($multi as $mp){ $i++; $sharing[$i] = 0; } 
									$account = $this->m_accounting->plbs_account('group', 'profit_sharing');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<tr style="background:#00cc00;">
												<td><b><?='('.$row['coa_id'].') '.$row['nama_coa']?></b></td>
												<?php $i = 0; foreach($multi as $mp){ $i++;
													$ending = $this->m_accounting->saldo_akhir($row['coa_id'], $mp); 
													echo '<td style="text-align:right;"><b>'.$this->cart->format_number($ending).'</b></td>';
													$sharing[$i] = $sharing[$i] + $ending;
												} ?>
												<td class="no-print"></td>
											</tr>
										<?php } ?>
									<?php } ?>
									<tr style="background:#00cc00;">
										<td><b>NET INCOME</b></td>
										<?php $i = 0; foreach($multi as $mp){ $i++;
											echo '<td style="text-align:right;"><b>'.$this->cart->format_number($net_profit[$i] - $tax[$i] - $sharing[$i]).'</b></td>';
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

