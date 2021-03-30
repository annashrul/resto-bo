
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
		<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
		<?=form_open($this->control.'/'.$page.$update)?>
		<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-12">
					<div class="col-sm-12">
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Account Group</label>
							<div class="col-sm-8">
								<?php $option = null; $option[''] = '-- Select --';
								$group = $this->m_crud->read_data('coa_group', '*', null, 'group_id asc');
								foreach($group as $row){ $option[$row['group_id']] = $row['group_id'].' - '.$row['nama']; }
								echo form_dropdown('group', $option, isset($coa['group_id'])?$coa['group_id']:set_value('group'), array('class' => 'form-control select2', 'required'=>'required')); ?>
								<?=form_error('group', '<div class="error" style="color:red;">', '</div>')?>
							</div>
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Code</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="code" value="<?=isset($_GET['trx'])?$_GET['trx']:set_value('code')?>" required />	
								<?=form_error('code', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Nama</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="nama" value="<?=isset($coa['nama'])?$coa['nama']:set_value('nama')?>" required />	
								<?=form_error('nama', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Balance</label>
							<div class="col-sm-8">
								<?php $option = null; $option[''] = '-- Select --';
								$option['D'] = 'Debit';
								$option['C'] = 'Credit';
								echo form_dropdown('balance', $option, isset($coa['balance'])?$coa['balance']:set_value('balance'), array('class' => 'form-control select2', 'required'=>'required')); ?>
								<?= form_error('balance', '<div class="error" style="color:red;">', '</div>'); ?>
							</div>
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Type</label>
							<div class="col-sm-8">
								<?php $option = null; $option[''] = '-- Select --';
								$option['L / R'] = 'L / R';
								$option['Neraca'] = 'Neraca';
								echo form_dropdown('jenis', $option, isset($coa['jenis'])?$coa['jenis']:set_value('jenis'), array('class' => 'form-control select2', 'required'=>'required')); ?>
								<?= form_error('jenis', '<div class="error" style="color:red;">', '</div>'); ?>
							</div>
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Currency</label>
							<div class="col-sm-8">
								<?php $option = null; $option[''] = '-- Select --';
								$currency = $this->m_crud->read_data('acc_kurs_uang', '*', null, 'nama asc');
								foreach($currency as $row){ $option[$row['id_kurs_uang']] = $row['nama']; }
								echo form_dropdown('currency', $option, isset($coa['currency'])?$coa['currency']:set_value('currency'), array('class' => 'form-control select2', 'required'=>'required')); ?>
								<?=form_error('currency', '<div class="error" style="color:red;">', '</div>')?>
							</div>
						</div>
					</div> 
				</div>
			</div>
			
			<div class="row" style="margin-bottom: 10px;, margin-top: 5px;">
				<div class="col-sm-12">
					<div class="form-group">
						<div class="col-sm-1 text-left">
							<button class="btn btn-primary" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button> 
						</div>
					</div>
				</div>
			</div>
		<?=form_close()?>
        </div>
    </div><!-- /.box -->
</section><!-- /.content -->


