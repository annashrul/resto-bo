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
									<label for="cemail" class="control-label col-lg-2">Judul</label>
									<div class="col-lg-10">
										<?php $field = 'judul'; ?>
										<input class="form-control" onblur="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Kode sudah digunakan!')" id="<?=$field?>" type="text" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        <b class="error" id="ntf_<?=$field?>"></b>
                                    </div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label for="cemail" class="control-label col-lg-2">Gambar</label>
									<div class="col-lg-10">
                                        <?php if(isset($master_data['foto']) && $master_data['foto']!=null && $master_data['foto']!='-'){ ?>
                                            <input type="hidden" name="logo_gambar" value="<?=$master_data['gambar']?>">
                                            <img width="200" src="<?=base_url().$master_data['foto']?>" />
                                        <?php } ?>
										<?php $field = 'foto'; ?>
										<input class="form-control" type="file" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" <?=isset($_GET['trx'])?'':'required aria-required="true"'?> />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label for="cemail" class="control-label col-lg-2">Sumber</label>
									<div class="col-lg-10">
										<?php $field = 'sumber'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label for="cemail" class="control-label col-lg-2">Isi</label>
									<div class="col-lg-10">
										<?php $field = 'deskripsi'; ?>
                                        <textarea class="form-control" id="<?=$field?>" name="<?=$field?>" required aria-required="true"><?=isset($master_data[$field])?$master_data[$field]:set_value($field)?></textarea>
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
    set_ckeditor('deskripsi');

    function cek_data(table, column, tipe, pesan){
        var id = $('#'+column).val();
        if(id!=''){
            $.ajax({
                type:'GET',
                url:'<?=site_url()?>site/cek_data_2/' + btoa(table) + '/' + btoa(column) + '/' + btoa(id),
                //data: {delete_id : id},
                success: function (data) {
                    if(data==1){
                        $("#ntf_"+column).text(pesan);
                        //if(tipe=='error'){ alert('error'); }
                        //else if(tipe=='warning'){ alert('warning'); }
                    } else {
                        $("#ntf_"+column).text('');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
            });
        }
    }
</script>