<div class="row" style="margin-top:60px;">

	<div class="col-lg-4">
		<Center style="margin-top:50px;">
			<i class="fa fa-user" style="font-size:200px;"></i>
			<h2>Change Profil</h2>
		</center>
	</div>
	
	
	<div class="col-lg-8">
		<div class="box box-primary">
			<div class="box-body">
			<?php echo form_open('user/ubah-profil-do');?>
			<input type="hidden" name="user" value="<?php echo $this->user;?>">
			
			
			<div class="form-group">
					<label>Name:</label>
					<input type="text" class="form-control" name="nama" id="nama" maxlength="55" value="<?php echo $account->nama;?>" required>
			</div>
			
			<div class="form-group">
					<label>Address:</label>
					<textarea class="form-control" name="alamat" id="alamat" maxlength="133" required><?php echo $account->alamat;?></textarea>
			</div>
			
			<div class="form-group">
					<label>Email:</label>
					<input type="email" class="form-control" name="email" id="email" maxlength="55" value="<?php echo $account->email;?>" required>
			</div>
			
			<div class="form-group">
					<label>Telephone:</label>
					<input type="number" class="form-control" name="notlp" id="notlp" value="<?php echo $account->nohp;?>" required>
			</div>
			
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-md btn-block" onclick="valid_form()">
					Update
				</button>
			</div>
			<?php echo form_close();?>
				
			</div>
		</div>
	</div>

</div>



<Script type="text/javascript">
function valid_form(){
	valid_nama();
	valid_alamat();
	valid_email();
	valid_notlp();
}

function valid_nama(){
var i = document.getElementById("nama");
	
	if (i.validity.valueMissing == true){
		i.setCustomValidity("Nama harus diisi.");
	}
	else{
		i.setCustomValidity("");
	}
}

function valid_alamat(){
var i = document.getElementById("alamat");
	
	if (i.validity.valueMissing == true){
		i.setCustomValidity("Alamat harus diisi.");
	}
	else{
		i.setCustomValidity("");
	}
}	

function valid_email(){
var i = document.getElementById("email");
	
	if (i.validity.valueMissing == true){
		i.setCustomValidity("Email harus diisi.");
	}
	else{
		i.setCustomValidity("");
	}
}	

function valid_notlp(){
var i = document.getElementById("notlp");
	
	if (i.validity.valueMissing == true){
		i.setCustomValidity("No.Telp harus diisi.");
	}
	else{
		i.setCustomValidity("");
	}
}	
</script>