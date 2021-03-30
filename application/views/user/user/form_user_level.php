
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
							<label class="col-sm-4 control-label text-left">Nama</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="nama" value="<?=isset($user_level['lvl'])?$user_level['lvl']:set_value('nama')?>" required />	
								<?=form_error('nama', '<div class="error" style="color:red;">', '</div>')?>
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


