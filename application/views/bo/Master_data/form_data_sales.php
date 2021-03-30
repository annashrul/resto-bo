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
							<?=form_open($this->control.'/'.$page.$update, array('id'=>'form_data','class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" /><input type="hidden" id="param" value="'.$_GET['trx'].'">':'<input type="hidden" id="param">'; ?>
								
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama</label>
									<div class="col-lg-10">
										<?php $field = 'Nama'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>

                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Username</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'username'; ?>
                                        <input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>

                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Lokasi</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'lokasi';
                                        $option = null;
                                        $option[''] = 'Pilih Lokasi';
                                        $data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'nama_toko asc');
                                        foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                        echo form_dropdown($field, $option, isset($master_data[$field])?$master_data[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
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
function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}

$.validator.setDefaults({
    onkeyup: function () {
        var originalKeyUp = $.validator.defaults.onkeyup;
        var customKeyUp =  function (element, event) {
            if ($("#username")[0] === element) {
                return false;
            }
            else {
                return originalKeyUp.call(this, element, event);
            }
        }

        return customKeyUp;
    }()
});


$('#form_data').validate({
    onkeyup: function(element, event) {
        if ($(element).attr('name') == "username") {
            return false; // disable onkeyup for your element named as "name"
        } else { // else use the default on everything else
            if ( event.which === 9 && this.elementValue( element ) === "" ) {
                return;
            } else if ( element.name in this.submitted || element === this.lastElement ) {
                this.element( element );
            }
        }
    },
    ignore: [],
    rules: {
        username: {
            required: true,
            remote: {
                url: "<?=base_url().'Master_data/cek_username'?>",
                type: "post",
                beforeSend: function() {
                    swal({
                        title: 'Checking Username',
                        allowOutsideClick: false,
                        onOpen: () => {
                            swal.showLoading()
                        }
                    })
                },
                complete: function() {
                    swal.close();
                },
                data: {
                    param: function() {
                        return $("#param").val();
                    }
                }
            }
        },
        Nama: {
            required: true
        },
        lokasi: {
            required: true
        }
    },
    //For custom messages
    messages: {
        username:{
            required: "Username tidak boleh kosong!",
            remote: "Username sudah tersedia!"
        },
        Nama:{
            required: "Nama tidak boleh kosong!"
        },
        lokasi:{
            required: "Lokasi tidak boleh kosong!"
        }
    },
    errorElement : 'div',
    errorPlacement: function(error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error)
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form) {
        $(form)[0].submit();
    }
});
</script>
