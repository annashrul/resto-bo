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
							<?=form_open_multipart($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama Bank</label>
									<div class="col-lg-10">
										<?php $field = 'Nama'; ?>
										<input class="form-control" type="text" onchange="cek_data('<?=$table?>','<?=$field?>', 'error', 'Nama Bank sudah digunakan. Ganti Nama lain!')" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
                                        <b style="color: red" id="ntf_nama"></b>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">EDC</label>
									<div class="col-lg-10 form-inline">
										<?php $field = 'EDC'; ?>
										<div class="radio radio-primary">
											<input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=((isset($master_data[$field])&&$master_data[$field]==1)?'checked':null)?> required />	
											<label for="<?=$field?>1"> Ada </label>
										</div>
										<div class="radio radio-primary">
											<input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=((isset($master_data[$field])&&$master_data[$field]==0)?'checked':null)?> required />	
											<label for="<?=$field?>0"> Tidak Ada </label>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Charge Debit</label>
									<div class="col-lg-10">
										<?php $field = 'Charge_Debit'; ?>
										<input class="form-control" type="number" step="any" min="0" max="100" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?($master_data[$field]+0):set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Charge Kredit</label>
									<div class="col-lg-10">
										<?php $field = 'Charge_Kredit'; ?>
										<input class="form-control" type="number" step="any" min="0" max="100" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?($master_data[$field]+0):set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nomor Rekening</label>
									<div class="col-lg-10">
										<?php $field = 'norek'; ?>
										<input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Atas Nama</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'atas_nama'; ?>
                                        <input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
                                        <b style="color: red" id="ntf_<?=$field?>"></b>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Status</label>
                                    <div class="col-lg-10 form-inline">
                                        <?php $field = 'status'; ?>
                                        <div class="radio radio-primary">
                                            <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=((isset($master_data[$field])&&$master_data[$field]==1)?'checked':null)?> required />
                                            <label for="<?=$field?>1"> Aktif </label>
                                        </div>
                                        <div class="radio radio-primary">
                                            <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=((isset($master_data[$field])&&$master_data[$field]==0)?'checked':null)?> required />
                                            <label for="<?=$field?>0"> Tidak Aktif </label>
                                        </div>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:5px;">
                                    <label class="col-lg-2 control-label">Gambar</label>
                                    <div class="col-lg-10">
                                        <?php if(isset($master_data['foto']) && $master_data['foto']!=null && $master_data['foto']!='-'){ ?>
                                            <input type="hidden" name="logo_foto" value="<?=$master_data['foto']?>">
                                            <img width="200" src="<?=base_url().$master_data['foto']?>" />
                                        <?php } ?>
                                        <input type="file" name="foto" id="foto" />
                                        <font color='red'><?php if(isset($error_logo)){ echo $error_logo; } ?></font>
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
function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/cek_data/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data) { 
				if(data==1){
				    $("#ntf_nama").text(pesan);
				    $("#save").prop('disabled', true);
					//alert(pesan);
					//if(tipe=='error'){ alert('error'); }
					//else if(tipe=='warning'){ alert('warning'); }
				} else {
                    $("#ntf_nama").text('');
                    $("#save").prop('disabled', false);
                }
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
		});
	}
}
</script>

