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
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama Kas</label>
									<div class="col-lg-10">
										<?php $field = 'Nama'; ?>
										<input class="form-control" type="text" onchange="cek_data('<?=$table?>','<?=$field?>', 'error', 'Nama Bank sudah digunakan. Ganti Nama lain!')" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div <?=($this->uri->segment(3)=='edit')?'hidden':''?> class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Tipe Kas</label>
									<div class="col-lg-10 form-inline">
										<?php $field = 'tipe'; ?>
										<div class="radio radio-primary">
											<input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="Master_Kas_Masuk" <?=((base64_decode($_GET['tabel'])=='Master_Kas_Masuk')?'checked':null)?> required />
											<label for="<?=$field?>1"> Kas Masuk </label>
										</div>
										<div class="radio radio-primary">
											<input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="Master_Kas_Keluar" <?=((base64_decode($_GET['tabel'])=='Master_Kas_Keluar')?'checked':null)?> required />
											<label for="<?=$field?>0"> Kas Keluar </label>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Jenis Kas</label>
                                    <div class="col-lg-10 form-inline">
                                        <?php $field = 'Jns_Kas'; ?>
                                        <div class="radio radio-primary">
                                            <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="KAS KECIL" <?=((isset($master_data[$field])&&$master_data[$field]=='KAS KECIL')?'checked':null)?> required />
                                            <label for="<?=$field?>1"> Kas Kecil </label>
                                        </div>
                                        <div class="radio radio-primary">
                                            <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="KAS BESAR" <?=((isset($master_data[$field])&&$master_data[$field]=='KAS BESAR')?'checked':null)?> required />
                                            <label for="<?=$field?>0"> Kas Besar </label>
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

