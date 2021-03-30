
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
							<label class="control-label"><?=$title?></label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 5px;">
					<div class="col-xs-12">
						<div class="box-body table-responsive">
							<?php $spasi = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
							<?php if(isset($_POST['tgl_akhir'])){ $periode = $this->input->post('tgl_akhir'); } else { $periode = $this->m_accounting->periode(); } ?>
							<table id="example2" class="table table-bordered table-striped dataTable">
								<tbody>
									<?php $modal = $this->m_accounting->modal_awal(); ?>
									<?php $net_profit = $this->m_accounting->net_profit_pl(null, $periode); ?>
									<?php $prive = 0; ?>
									<tr><td>Beginning Capital</td><td></td><td style="text-align:right;"><?=$this->cart->format_number($modal)?></td></tr>
									<tr><td>Net Profit</td><td style="text-align:right;"><?=$this->cart->format_number($net_profit)?></td><td></td></tr>
									<tr><td>Prive</td><td style="text-align:right;"><?=$this->cart->format_number($prive)?></td><td></td></tr>
									<tr><td>Changes in Capital</td><td></td><td style="text-align:right;"><?=$this->cart->format_number($net_profit - $prive)?></td></tr>
									<tr><td>Ending Capital</td><td></td><td style="text-align:right;"><?=$this->cart->format_number($modal + $net_profit - $prive)?></td></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

