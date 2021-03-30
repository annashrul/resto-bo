<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->                      
<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">

			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="pull-left page-title"><?=$title?></h4>
					<ol class="breadcrumb pull-right">
						<li><a href="<?=base_url()?>"><?=$site->title?></a></li>
						<li class="active"><?=$title?></li>
					</ol>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
						</div>
						<div class="panel-body">
							<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
							<?=form_open($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?=isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
							<?=isset($_GET['trx'])?'<input type="hidden" id="tmp_alamat" name="tmp_alamat" value="'.$master_data['alamat'].'" />':''; ?>

							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Nama</label>
								<div class="col-lg-10">
									<?php $field = 'Nama'; ?>
									<input class="form-control" autocomplete="off" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Tipe</label>
								<div class="col-lg-10">
									<?php $field = 'Cust_Type';
									$option = null; $option[''] = '-- Tipe Customer --';
									//$option['all'] = 'All';
									$data_option = $this->m_crud->read_data('Customer_Type', 'KODE, NAMA', null, 'NAMA asc');
									foreach($data_option as $row){ $option[$row['KODE']] = $row['NAMA']; }
									echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
									?>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Status</label>
								<div class="col-lg-10 form-inline">
									<?php $field = 'status'; ?>
									<div class="radio radio-primary">
										<input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=($master_data[$field]=='1')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
										<label for="<?=$field?>1"> Aktif </label>
									</div>
									<div class="radio radio-primary">
										<input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=($master_data[$field]=='0')?'checked':null?> required />
										<label for="<?=$field?>0"> Tidak Aktif </label>
									</div>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="hidden">
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Provinsi</label>
								<div class="col-lg-10">
									<?php $field = 'nama_provinsi';
									$option = null; $option[''] = '-- Provinsi --';
									//$option['all'] = 'All';
									$data_option = $this->m_crud->read_data('provinces', 'id, name', null, 'name asc');
									foreach($data_option as $row){ $option[$row['id']] = $row['name']; }
									echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'onchange'=>"get_sub_dropdown($(this).val(),'regencies','province_id','-- Kota --','nama_kota')"));
									?>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Kota</label>
								<div class="col-lg-10">
									<?php $field = 'nama_kota'; ?>
									<select name="<?=$field?>" id="<?=$field?>" class="select2" onchange="get_sub_dropdown($(this).val(),'districts','regency_id','-- Kecamatan --','nama_kecamatan')" >
										<option value="">-- Kota --</option>
										<?php
										/*if (isset($_GET['trx'])) {
											$data_option = $this->m_crud->read_data('regencies', 'id, name', null, 'name asc');
											foreach($data_option as $row){
												echo "<option value='".$row['id']."'>".$row['name']."</option>";
											}
										}*/
										?>
									</select>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Kecamatan</label>
								<div class="col-lg-10">
									<?php $field = 'nama_kecamatan'; ?>
									<select name="<?=$field?>" id="<?=$field?>" class="select2" onchange="get_sub_dropdown($(this).val(),'villages','district_id','-- Desa/Kelurahan --','nama_desa')" >
										<option value="">-- Kecamatan --</option>
									</select>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Desa/Kelurahan</label>
								<div class="col-lg-10">
									<?php $field = 'nama_desa'; ?>
									<select name="<?=$field?>" id="<?=$field?>" class="select2" >
										<option value="">-- Desa/Kelurahan --</option>
									</select>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Alamat</label>
								<div class="col-lg-10">
									<?php $field = 'alamat'; (isset($master_data[$field]))?$alamat = explode('|', $master_data[$field]):null; ?>
									<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$alamat[0]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Diskon %</label>
								<div class="col-lg-10">
									<?php $field = 'diskon'; ?>
									<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Tanggal Ultah</label>
								<div class="col-lg-10">
									<?php $field = 'tgl_ultah'; ?>
									<input class="form-control datepicker_date" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?substr($master_data[$field],0,10):null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Diskon Ultah %</label>
								<div class="col-lg-10">
									<?php $field = 'diskon_ultah'; ?>
									<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Telp 1</label>
								<div class="col-lg-10">
									<?php $field = 'tlp1'; ?>
									<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Telp 2</label>
								<div class="col-lg-10">
									<?php $field = 'tlp2'; ?>
									<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Telp 3</label>
								<div class="col-lg-10">
									<?php $field = 'tlp3'; ?>
									<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Tanggal Deposit</label>
								<div class="col-lg-10">
									<?php $field = 'tgldeposit'; ?>
									<input class="form-control datepicker_date" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?substr($master_data[$field],0,10):null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Deposit</label>
								<div class="col-lg-10">
									<?php $field = 'deposit'; ?>
									<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Special Price</label>
								<div class="col-lg-10 form-inline">
									<?php $field = 'SPECIAL_PRICE'; ?>
									<div class="radio radio-primary">
										<input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=($master_data[$field]=='1')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
										<label for="<?=$field?>1"> Ya </label>
									</div>
									<div class="radio radio-primary">
										<input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=($master_data[$field]=='0')?'checked':null?> required />
										<label for="<?=$field?>0"> Tidak </label>
									</div>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Tanggal Akhir</label>
								<div class="col-lg-10">
									<?php $field = 'TGLAKHIR'; ?>
									<input class="form-control datepicker_date" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?substr($master_data[$field],0,10):null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-offset-2 col-lg-10">
									<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
								</div>
							</div>
								
							<?=form_close()?>
						</div>
					</div>
				</div>
				
			</div> <!-- End Row -->
			
		</div> <!-- container -->
				   
	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<script>
	$(document).ready(function () {
		var tmp = $("#tmp_alamat").val();
		if (tmp != undefined) {
			var alamat = tmp.split("|");

			$("#nama_provinsi").val(alamat[4]);
			get_sub_dropdown(alamat[4],'regencies','province_id','-- Kota --','nama_kota');
			get_sub_dropdown(alamat[3],'districts','regency_id','-- Kecamatan --','nama_kecamatan');
			get_sub_dropdown(alamat[2],'villages','district_id','-- Desa/Kelurahan --','nama_desa');
		}

	});

	function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
	}

function get_sub_dropdown(id, table, column, def_sel, id_sel) {
	$.ajax({
		url: "<?=site_url()?>site/get_dropdown/" + table + "/" + column + "/" + id + "/" + btoa(def_sel),
		type: "GET",
		success: function (res) {
			$("#"+id_sel).html(res);
			if (id_sel == 'nama_kota') {
				$("#nama_kota, #nama_kecamatan, #nama_desa").select2("val", "")
			} else if (id_sel == 'nama_kecamatan') {
				$("#nama_kecamatan, #nama_desa").select2("val", "")
			} else {
				$("#nama_desa").select2("val", "")
			}
		}
	});
}

function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/cek_data/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data) { 
				if(data==1){ 
					alert(pesan);
					//if(tipe=='error'){ alert('error'); }
					//else if(tipe=='warning'){ alert('warning'); }
				}
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
		});
	}
}
</script>
