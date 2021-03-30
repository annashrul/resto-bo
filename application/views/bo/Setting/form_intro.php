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
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
									<?=form_open_multipart($this->control.'/'.$page.'/'.$this->uri->segment(3).'/'.$update)?>
									<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
										<div class="row" style="margin-bottom: 10px;">
											<div class="col-sm-12">
												<div class="col-sm-12">
                                                    <div class="row" style="margin-bottom: 10px">
                                                        <?php $label = 'tipe'; ?>
                                                        <label for="<?=$label?>" class="col-sm-2 control-label">Background Gambar</label>

                                                        <div class="col-sm-10">
                                                            <div class="checkbox checkbox-primary">
                                                                <input class="form-control" type="checkbox" id="<?=$label?>" name="<?=$label?>" value="1" <?=(set_value($label)=='1')?'checked':(isset($res_data[$label])&&$res_data[$label]=='foto'?'checked':null)?> />
                                                                <label for="<?=$label?>"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" id="cont_warna" style="margin-bottom: 10px">
                                                        <?php $label = 'warna'; ?>
                                                        <label for="<?=$label?>" class="col-sm-2 control-label">Warna</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="<?=$label?>" value="<?=set_value($label)?set_value($label):(isset($res_data['background'])&&$res_data['tipe']=='warna'?$res_data['background']:null)?>" class="form-control colorpicker" id="<?=$label?>" autocomplete="off" placeholder="Input Warna" />
                                                        </div>
                                                    </div>
                                                    <div class="row" id="cont_gambar" style="margin-bottom: 10px">
                                                        <?php $label = 'file_upload'; ?>
                                                        <label for="<?=$label?>" class="col-sm-2 control-label">Gambar</label>

                                                        <input type="hidden" id="<?=$label?>ed" name="<?=$label?>ed" />
                                                        <div class="col-sm-5">
                                                            <input type="file" id="<?=$label?>" name="<?=$label?>" onchange="return ValidateFileUpload()" accept="image/*">
                                                            <p class="error" id="alr_<?=$label?>"></p>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <img style="max-width:300px; max-height:300px;" src="<?=base_url().(isset($res_data['background'])&&$res_data['tipe']=='foto'?$res_data['background']:'assets/images/no_image.png')?>" id="result_image">
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 10px">
                                                        <?php $label = 'judul'; ?>
                                                        <label for="<?=$label?>" class="col-sm-2 control-label">Judul</label>

                                                        <div class="col-sm-10">
                                                            <input type="text" name="<?=$label?>" class="form-control" required value="<?=set_value($label)?set_value($label):(isset($res_data[$label])?$res_data[$label]:null)?>" id="<?=$label?>" autocomplete="off" placeholder="Judul" />
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 10px">
                                                        <?php $label = 'keterangan'; ?>
                                                        <label for="<?=$label?>" class="col-sm-2 control-label">Keterangan</label>

                                                        <div class="col-sm-10">
                                                            <input type="text" name="<?=$label?>" required value="<?=set_value($label)?set_value($label):(isset($res_data[$label])?$res_data[$label]:null)?>" class="form-control" id="<?=$label?>" autocomplete="off" placeholder="Keterangan" />
                                                        </div>
                                                    </div>
												</div>
											</div>
										</div>
										<div class="row" style="margin-bottom: 10px;, margin-top: 5px;">
											<div class="col-sm-12">
												<div class="form-group">
													<div class="col-sm-1 text-left">
														<button class="btn btn-primary" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button> 
													</div>
												</div>
											</div>
										</div>
									<?=form_close()?>
								</div>
							</div>
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
        if ('<?=isset($_GET['trx'])?>' != '') {
            if ('<?=$res_data['tipe']?>' == 'warna') {
                $("#cont_gambar").hide();
            } else {
                $("#cont_warna").hide();
            }
        } else {
            $("#cont_gambar").hide();
        }
    });

    $("#tipe").click(function () {
        if ($(this).is(":checked")) {
            if ($("#param").val() == 'add') {
                $('#file_upload').val('');
                $('#result_image').attr('src', '<?=$this->m_website->no_img()?>');
            }
            $("#cont_gambar").show();
            $("#cont_warna").hide();
        } else {
            $("#cont_gambar").hide();
            $("#cont_warna").show();
        }
    });

    function ValidateFileUpload() {
        var fuData = document.getElementById('file_upload');
        var FileUploadPath = fuData.value;
        var valid = 1;
        $("#alr_file_upload").text("");
        //$("#upload").prop("disabled", true);

        if (FileUploadPath == '') {
            //$("#alr_file_upload").text("Please upload an image");
            //alert("Please upload an image");
            //valid = 0;
        } else {
            var Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();

            if (Extension == "gif" || Extension == "png" || Extension == "bmp" || Extension == "jpeg" || Extension == "jpg") {
                if (fuData.files && fuData.files[0]) {
                    //var size = fuData.files[0].size;
                    //if(size > (2048 * 1000)){
                    //$("#alr_file_upload").text("Maximum file size exceeds");
                    //valid = 0;
                    //} else {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#result_image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(fuData.files[0]);
                    //$("#upload").prop("disabled", false);
                    //}
                }
            } else {
                //$("#alr_file_upload").text("Image only allows file types of GIF, PNG, JPG, JPEG and BMP.");
                //valid = 0;
            }
        }
        return valid;
    }
</script>
