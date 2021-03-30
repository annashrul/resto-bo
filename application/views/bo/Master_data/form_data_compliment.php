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
								<label class="control-label col-lg-2">Company</label>
								<div class="col-lg-10">
									<?php $field = 'company'; ?>
									<input class="form-control" autocomplete="off" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Nama</label>
								<div class="col-lg-10">
									<?php $field = 'nama'; ?>
									<input class="form-control" autocomplete="off" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
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
								<label class="control-label col-lg-2">Telp</label>
								<div class="col-lg-10">
									<?php $field = 'tlp'; ?>
									<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Status</label>
                                <div class="col-lg-10 form-inline">
                                    <?php $field = 'status'; ?>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=$master_data[$field]==1?'checked':(isset($_GET['trx'])?null:'checked')?> required />
                                        <label for="<?=$field?>1"> Aktif </label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=$master_data[$field]==0?'checked':null?> required />
                                        <label for="<?=$field?>0"> Tidak Aktif </label>
                                    </div>
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
