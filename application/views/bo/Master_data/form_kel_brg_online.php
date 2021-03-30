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
                            <?php $caption = $this->m_crud->get_data('Setting', 'as_group2', "Kode = '1111'"); ?>
                                <div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama Kelompok</label>
									<div class="col-lg-10">
										<?php $field = 'nama'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Status</label>
									<div class="col-lg-10">
										<?php $field = 'status'; ?>
										<input type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=(isset($master_data[$field])&&$master_data[$field]==1)?'checked':null?> required aria-required="true" /> Aktif 
										<input type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=(isset($master_data[$field])&&$master_data[$field]==0)?'checked':null?> required aria-required="true" /> Tidak Aktif
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>

                                <div class="row" style="margin-bottom:5px;">
                                    <label class="col-lg-2 control-label">Gambar</label>
                                    <div class="col-lg-10">
                                        <?php if(isset($master_data['gambar']) && $master_data['gambar']!=null && $master_data['gambar']!='-'){ ?>
                                            <input type="hidden" name="logo_gambar" value="<?=$master_data['gambar']?>">
                                            <img width="200" src="<?=base_url().$master_data['gambar']?>" />
                                        <?php } ?>
                                        <input type="file" name="gambar" id="gambar" />
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

    $("#Group2").change(function () {
        var kode = $(this).val();
        if (kode != '') {
            $.ajax({
                url: "<?=base_url()?>site/max_kode_kelompok/" + btoa(kode),
                type: "GET",
                success: function (res) {
                    $("#kel_brg").val(res);
                }
            });
        } else {
            $("#kel_brg").val("");
        }
    });
</script>

