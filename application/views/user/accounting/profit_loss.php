
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
					<div class="col-sm-1">
						<label class="control-label text-left">Multi</label><br/>
						<input name="multi" id="multi" type="checkbox" value="1" <?=set_value('multi')?'checked':''?> ></input>
					</div>
					<?php $ongoing = $this->m_accounting->ongoing_periode(); ?>
					<div class="col-sm-2">
						<label class="control-label text-left">Period</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=isset($_POST['search'])?set_value('tgl_awal'):substr($ongoing['tanggal_awal'], 0, 10)?>" ></input>
						</div>
					</div>
					<div class="col-sm-2">
						<label>To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=isset($_POST['search'])?set_value('tgl_akhir'):substr($ongoing['tanggal_akhir'], 0, 10)?>" ></input>
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
								$periode = array(($_POST['tgl_awal']!=null)?$_POST['tgl_awal']:'2017-01-01', ($_POST['tgl_akhir']!=null)?$_POST['tgl_akhir']:date('Y-m-d')); 
							} else { $periode = array(substr($ongoing['tanggal_awal'], 0, 10), substr($ongoing['tanggal_akhir'], 0, 10)); } ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<tr><td><b>OPERATING REVENUE</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Revenue</b></td><td></td><td class="no-print"></td></tr>
									<?php $revenue = 0; $account = $this->m_accounting->plbs_account('group', 'revenue');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $revenue = $revenue + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b>Total OPERATING REVENUE</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($revenue)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b>Cost of Goods Sold</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Cost of Goods Sold</b></td><td></td><td class="no-print"></td></tr>
									<?php $cogs = 0; $account = $this->m_accounting->plbs_account('group', 'cogs');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cogs = $cogs + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi?> Overhead Expenses</b></td><td></td><td class="no-print"></td></tr>
									<?php $overhead = 0; $account = $this->m_accounting->plbs_account('group', 'overhead');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>	
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $overhead = $overhead + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b>Total Cost of Goods Sold</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($cogs + $overhead)?></b></td><td class="no-print"></td></tr>
									
									<?php $groos_profit = $revenue - ($cogs + $overhead); ?>
									<tr style="background:#66cc66;"><td><b>GROOS PROFIT</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($groos_profit)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b>Operating Expenses</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Expense</b></td><td></td><td class="no-print"></td></tr>
									<?php $expense = 0; $account = $this->m_accounting->plbs_account('group', 'expense');
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $expense = $expense + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b>Total Operating Expense</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($expense)?></b></td><td class="no-print"></td></tr>
									
									<?php $income = $groos_profit - $expense; ?>
									<tr style="background:#66cc66;"><td><b>INCOME FROM OPERATION</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($income)?></b></td><td class="no-print"></td></tr>
									
									<tr><td><b>Other Income and Expenses</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Other Income</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Other Profit / Loss</b></td><td></td><td class="no-print"></td></tr>
									<?php $other_income = 0; $account = $this->m_accounting->plbs_account('kategori', 'other_income');
									foreach($profit_loss as $row){ ?>
										<?php if($row['kategori_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other_income = $other_income + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi?>Total Other Income</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($other_income)?></b></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Other Expenses</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi.$spasi?> Other Expense</b></td><td></td><td class="no-print"></td></tr>
									<?php $other_expense = 0; $account = $this->m_accounting->plbs_account('group', 'other_expense'); 
									foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from='.$periode[0].'&to='.$periode[1]; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $other_expense = $other_expense + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#66cc99;"><td><b><?=$spasi?>Total Other Expenses</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($other_expense)?></b></td><td class="no-print"></td></tr>
									
									<?php $other_inex = $other_income - $other_expense; ?>
									<tr style="background:#66cc66;"><td><b>Total Other Income and Expenses</b></td><td style="text-align:right;"><b><?=$this->cart->format_number($other_inex)?></b></td><td class="no-print"></td></tr>
									
									
									<tr style="background:#00cc00;">
										<td><b>NET PROFIT/LOSS (Before Tax)</b></td>
										<?php $net_profit = $income + $other_inex; ?>
										<td style="text-align:right;"><b><?=$this->cart->format_number($net_profit)?></b></td>
										<td class="no-print"></td>
									</tr>
									<?php $tax = 0; $account = $this->m_accounting->plbs_account('group', 'tax_income'); ?>
									<?php foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ //'72301' ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr style="background:#00cc00;">
												<td><b><?='('.$row['coa_id'].') '.$row['nama_coa']?></b></td>
												<td style="text-align:right;"><b><?=$this->cart->format_number($ending)?></b></td>
												<td class="no-print"></td>
											</tr>
											<?php $tax = $tax + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#00cc00;">
										<td><b>NET PROFIT/LOSS (After Tax)</b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($net_profit - $tax)?></b></td>
										<td class="no-print"></td>
									</tr>
									<?php $sharing=0; $account = $this->m_accounting->plbs_account('group', 'profit_sharing'); ?>
									<?php foreach($profit_loss as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir($row['coa_id'], $periode); ?>
											<tr style="background:#00cc00;">
												<td><b><?='('.$row['coa_id'].') '.$row['nama_coa']?></b></td>
												<td style="text-align:right;"><b><?=$this->cart->format_number($ending)?></b></td>
												<td class="no-print"></td>
											</tr>
											<?php $sharing = $sharing + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr style="background:#00cc00;">
										<td><b>NET INCOME</b></td>
										<td style="text-align:right;"><b><?=$this->cart->format_number($net_profit - $tax - $sharing)?></b></td>
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

