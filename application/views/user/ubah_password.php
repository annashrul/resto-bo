<div class="row" style="margin-top:60px;">

	<div class="col-lg-2">
		<Center style="margin-top:50px;">
			<i class="fa fa-key" style="font-size:40px;"></i>
			<h5>Change Password</h5>
		</center>
	</div>
	
	
<div class="col-lg-10">
		<div class="box box-primary">
			<div class="box-body">
			<?php echo form_open('user/ubah-password');?>
			<input type="hidden" name="user" value="<?php echo $this->user;?>">
			
			
			<div class="form-group">
					<label>Old Password:</label>
					<input type="password" class="form-control" name="passlama" id="passlama" required>
			</div>
			
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-md btn-block" onclick="valid_form()">
					Next <i class="fa fa-chevron-right"></i>
				</button>
			</div>
			<?php echo form_close();?>
				
			</div>
		</div>
	</div>

</div>



<Script type="text/javascript">
function valid_form(){
	valid_passlama();
}

function valid_passlama(){
var i = document.getElementById("passlama");
	
	if (i.validity.valueMissing == true){
		i.setCustomValidity("Password Lama harus diisi.");
	}
	else{
		i.setCustomValidity("");
	}
}
</script>