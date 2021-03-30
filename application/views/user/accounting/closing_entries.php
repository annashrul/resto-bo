
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
            <?php if(isset($closing) && $closing == true){ ?> 
				<div class="row">
					<div class="col-sm-12 text-center">
						<br/>
						Closing Success. <?=anchor('accounting/set_periode', 'Setup new period!')?>
						<br/>
						<br/>
					</div>
				</div>
			<?php } else if(isset($closing) && $closing == false){ ?> 
				<div class="row">
					<div class="col-sm-12 text-center">
						<br/>
						Closing Failed. Period is still running.
						<br/>
						<br/>
					</div>
				</div>
			<?php } else { ?>
				
			<?php } ?>
			<?=form_open($content)?>
			<div class="row col-sm-12">
				<div class="col-sm-12"><label>Currency Exchange : <input type="hidden" name="tanggal" value="<?=date('Y-m-d')?>" /></label></div> 
				<?php $currency = $this->m_crud->read_data('acc_kurs_uang', '*', null); ?>
				<?php $i=0; foreach($currency as $row){ $i++; ?>
				<div class="col-sm-3">
					<label><?=$row['nama']?></label>
					<input type="hidden" name="currency<?=$i?>" value="<?=$row['id_kurs_uang']?>" /> 
					<input type="number" step="any" class="form-control" name="exchange<?=$i?>" value="<?=set_value('exchange'.$i)?set_value('exchange'.$i):$row['rate']?>" required />
					<?= form_error('exchange'.$i, '<div class="error" style="color:red;">', '</div>'); ?>
				</div>
				<?php } ?>
				<input type="hidden" name="jumlah" value="<?=$i?>" />
			</div>
			<div class="row">
				<div class="col-sm-12 text-center">
					<br/>
					<button type="submit" class="btn btn-primary" name="closing">Closing</button>
					<br/><br/><br/>
				</div>
			</div>
			<?=form_close()?>
        </div>
    </div><!-- /.box -->

</section><!-- /.content -->

