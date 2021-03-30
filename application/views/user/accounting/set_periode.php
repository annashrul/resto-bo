
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
			<div class="row">
				<div class="col-sm-12">
					<label>Last Periode : </label> <?=substr($this->m_crud->max_data($table, 'tanggal_akhir', "status = 4 and lokasi = '".$this->session->lokasi."'"), 0, 10)?>
				</div>
			</div>
			<?= form_open($content); ?>
			<!--<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<div class="col-sm-3">
						<label class="control-label text-left">Beginning Balance</label>
						<div class="input-group date">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							<input class="form-control pull-right datepicker_date" name="beginning" id="beginning" type="text" value="<?=$beginning?$beginning['tanggal_akhir']:set_value('beginning')?>" ></input>
						</div>
						<?=form_error('beginning', '<div class="error" style="color:red;">', '</div>')?>
					</div>
				</div>
			</div>-->
            <!--<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<div class="col-sm-2">
						<label class="control-label text-left">Type</label>
						<div class="input-group">
							<?php /*$option = null; $option[''] = '-- Select --';
							$option['Bulan'] = 'Mount';
							$option['Tahun'] = 'Year';
							echo form_dropdown('jenis', $option, $set_periode?$set_periode['jenis']:set_value('jenis'), array('class' => 'form-control')); ?>
							<?=form_error('jenis', '<div class="error" style="color:red;">', '</div>')*/?>
						</div>
					</div>
				</div>
			</div>--> <input type="hidden" name="jenis" value="Tahun" />
			<div class="row" style="margin-bottom: 15px;">
				<div class="col-sm-12">
					<div class="col-sm-3">
						<label class="control-label text-left">Periode</label>
						<div class="input-group date">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							<input class="form-control pull-right datepicker_date" name="tgl_awal" id="tgl_awal" type="text" value="<?=$set_periode?substr($set_periode['tanggal_awal'], 0, 10):set_value('tgl_awal')?>" ></input>
						</div>
						<?=form_error('tgl_awal', '<div class="error" style="color:red;">', '</div>')?>
					</div>
					<div class="col-sm-3">
						<label>To</label>
						<div class="input-group date">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							<input class="form-control pull-right datepicker_date" name="tgl_akhir" id="tgl_akhir" type="text" value="<?=$set_periode?substr($set_periode['tanggal_akhir'], 0, 10):set_value('tgl_akhir')?>" ></input>
						</div>
						<?=form_error('tgl_akhir', '<div class="error" style="color:red;">', '</div>')?>
					</div >
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<div class="col-sm-12">
						<button class="btn btn-primary" type="submit" name="save"><i class="fa fa-save"></i> Save</button>
					</div>
				</div>
			</div>
			<?= form_close(); ?>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

