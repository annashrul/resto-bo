<div class="row" style="margin-top:60px;">

	<div class="col-lg-4">
		<Center style="margin-top:50px;">
			<i class="fa fa-user" style="font-size:200px;"></i>
			<h2>My Profile</h2>
		</center>
	</div>
	
	
	<div class="col-lg-8">
		<div class="box box-primary">
			<div class="box-body">
			<div class="form-group">
					<label>Name:</label>
					<h6 style="border-bottom:1px solid gray;"><?php echo $account->nama;?></h6>
			</div>
			
			<div class="form-group">
					<label>Address:</label>
					<h6 style="border-bottom:1px solid gray;"><?php echo $account->alamat;?></h6>
			</div>
			
			<div class="form-group">
					<label>Email:</label>
					<h6 style="border-bottom:1px solid gray;"><?php echo $account->email;?></h6>
			</div>
			
			<div class="form-group">
					<label>Telephone:</label>
					<h6 style="border-bottom:1px solid gray;"><?php echo $account->nohp;?></h6>
			</div>
			
			<div class="form-group">
				<a href="<?php echo base_url();?>user/ubah-profil" class="btn btn-primary btn-md btn-block">
					Change Profile
				</a>
			</div>
				
			</div>
		</div>
	</div>

</div>