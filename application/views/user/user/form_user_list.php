
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
						<input type="hidden" name="user_id" value="<?=isset($user_list['user_id'])?$user_list['user_id']:$this->m_crud->read_data('user_akun', 'max(user_id) as id')[0]['id'] + 1?>" required />	
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Username</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="username" value="<?=isset($user_list['username'])?$user_list['username']:set_value('username')?>" required />	
								<?=form_error('username', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Password</label>
							<div class="col-sm-8">
								<input class="form-control" type="password" id="password" name="password" value="<?=isset($user_list['password'])?$user_list['password']:set_value('password')?>" required />	
								<?=form_error('password', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Conf Password</label>
							<div class="col-sm-8">
								<input class="form-control" type="password" id="conf_password" name="conf_password" value="<?=isset($user_list['password'])?$user_list['password']:set_value('conf_password')?>" required />	
								<?=form_error('conf_password', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Level</label>
							<div class="col-sm-8">
								<?php $option = null; $option[''] = '-- Select --';
								$user_lvl = $this->m_crud->read_data('user_lvl', '*', 'id <> 1', 'lvl asc');
								foreach($user_lvl as $row){ $option[$row['id']] = $row['lvl']; }
								echo form_dropdown('user_lvl', $option, isset($user_list['user_lvl'])?$user_list['user_lvl']:set_value('user_lvl'), array('class' => 'form-control', 'required' => 'required')); ?>
								<?=form_error('user_lvl', '<div class="error" style="color:red;">', '</div>')?>
							</div>
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Name</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="nama" value="<?=isset($user_list['nama'])?$user_list['nama']:set_value('nama')?>" required />	
								<?=form_error('nama', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Address</label>
							<div class="col-sm-8">
								<input class="form-control" type="text" name="alamat" value="<?=isset($user_list['alamat'])?$user_list['alamat']:set_value('alamat')?>" required />	
								<?=form_error('alamat', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Email</label>
							<div class="col-sm-8">
								<input class="form-control" type="email" name="email" value="<?=isset($user_list['email'])?$user_list['email']:set_value('email')?>" required />	
								<?=form_error('email', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
						<div class="row" style="margin-bottom:10px;">
							<label class="col-sm-4 control-label text-left">Phone</label>
							<div class="col-sm-8">
								<input class="form-control" type="number" name="nohp" value="<?=isset($user_list['nohp'])?$user_list['nohp']:set_value('nohp')?>" required />	
								<?=form_error('nohp', '<div class="error" style="color:red;">', '</div>')?>
							</div> 
						</div>
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom: 10px;, margin-top: 5px;">
				<div class="col-sm-12">
					<div class="form-group">
						<div class="col-sm-1 text-left">
							<button class="btn btn-primary" onclick="valid_form()" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button> 
						</div>
					</div>
				</div>
			</div>
		<?=form_close()?>
        </div>
    </div><!-- /.box -->
</section><!-- /.content -->



<Script type="text/javascript">
function valid_form(){
	valid_conf_password();
}


function valid_conf_password(){
var i = document.getElementById("conf_password");
var password = $('#password').val();
var conf_password = $('#conf_password').val();
	
	if (i.validity.valueMissing == true){
		i.setCustomValidity("Don't empty");
	}
	else if (password!=conf_password){
		i.setCustomValidity("Not match");
	}
	else{
		i.setCustomValidity("");
	}
}		
</script>