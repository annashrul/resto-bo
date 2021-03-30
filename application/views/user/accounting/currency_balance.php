
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
					</div>
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
							<?php $spasi = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
							<?php if(isset($_POST['tgl_akhir']) && $_POST['tgl_akhir']!=null){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); } ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<?php /*$cash_bank = 0; $account = $this->m_accounting->plbs_account('group', 'cash');
									foreach($currency_balance as $row){ ?>
										<?php //if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); ?>
											<tr>
												<td><?='('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=0000-00-00&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?currency=2&account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php //} ?>
									<?php } */?>
									<tr><td><b>Cash and Bank</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Cash</b></td><td></td><td class="no-print"></td></tr>
									<?php $cash_bank = 0; $account = $this->m_accounting->plbs_account('group', 'cash');
									foreach($currency_balance as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=0000-00-00&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?currency=2&account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b><?=$spasi?> Bank</b></td><td></td><td class="no-print"></td></tr>
									<?php $account = $this->m_accounting->plbs_account('group', 'bank'); ?>
									<?php foreach($currency_balance as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=0000-00-00&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?currency=2&account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $cash_bank = $cash_bank + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b>Account Receivable</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Account Receivable</b></td><td></td><td class="no-print"></td></tr>
									<?php $ar = 0; $account = $this->m_accounting->plbs_account('group', 'ar');
									foreach($currency_balance as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=0000-00-00&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?currency=2&account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ar = $ar + $ending; ?>
										<?php } ?>
									<?php } ?>
									<tr><td><b>Liabilities</b></td><td></td><td class="no-print"></td></tr>
									<tr><td><b><?=$spasi?> Account Payables</b></td><td></td><td class="no-print"></td></tr>
									<?php $ap = 0; $account = $this->m_accounting->plbs_account('group', 'ap');
									foreach($currency_balance as $row){ ?>
										<?php if($row['group_id'] == $account){ ?>
											<?php $ending = $this->m_accounting->saldo_akhir_asing($row['coa_id'], $periode); ?>
											<tr>
												<td><?=$spasi.$spasi.'('.$row['coa_id'].') '.$row['nama_coa']?></td>
												<td style="text-align:right;"><?=$this->cart->format_number($ending)?></td>										
												<?php (isset($_POST['tgl_awal']) && isset($_POST['tgl_akhir']))?$tanggal='from='.$_POST['tgl_awal'].'&to='.$_POST['tgl_akhir']:$tanggal='from=0000-00-00&to='.$periode; ?>
												<td class="no-print"><?=anchor('accounting/ledger/?currency=2&account='.$row['coa_id'].'&'.$tanggal, '<button class="btn btn-primary">Detail</button>')?></td>
											</tr>
											<?php $ap = $ap + $ending; ?>
										<?php } ?>
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

