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
								<?php $field = 'user_id'; ?>
								<input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:($this->m_crud->max_data('user_akun', 'user_id') + 1)?>" required aria-required="true" />

								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama</label>
									<div class="col-lg-10">
										<?php $field = 'nama'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Username</label>
									<div class="col-lg-10">
										<?php $field = 'username'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" <?=isset($_GET['trx'])?'readonly':null;?> onchange="cek_data('<?=$table?>','<?=$field?>', 'error', 'Username sudah digunakan. Ganti username lain!')" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
                                        <b style="color: red" id="ntf_username"></b>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Password</label>
									<div class="col-lg-10">
										<?php $field = 'password'; ?>
										<input class="form-control" type="password" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" <?=isset($_GET['trx'])?'placeholder="Kosongkan jika tidak diganti"':'required aria-required="true"'?> />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Confirm Password</label>
									<div class="col-lg-10">
										<?php $field = 'conf_password'; ?>
										<input class="form-control" type="password" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" <?=isset($_GET['trx'])?'placeholder="Kosongkan jika tidak diganti"':'required aria-required="true"'?> />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Password Otorisasi</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'password_otorisasi'; ?>
                                        <input class="form-control" type="password" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" <?=isset($_GET['trx'])?'placeholder="Kosongkan jika tidak diganti"':''?> />
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
								<div class="form-group ">
									<label class="control-label col-lg-2">User Level</label>
									<div class="col-lg-10">
										<?php $field = 'user_lvl';
										$option = null; $option[''] = '-- User Level --';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('user_lvl', 'id, lvl', "id <> 1", 'lvl asc');
//										$data_option = $this->m_crud->read_data('user_lvl', 'id, lvl', null, 'lvl asc');
										foreach($data_option as $row){ $option[$row['id']] = $row['lvl']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
										?>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Lokasi</label>
                                    <div class="col-lg-8">
                                        <?php
                                        $array_lokasi = array();
                                        if (isset($master_data['lokasi'])) {
                                            $data_lokasi = json_decode($master_data['lokasi'], true);
                                            for ($i = 0; $i < count($data_lokasi['lokasi_list']); $i++) {
                                                array_push($array_lokasi, $data_lokasi['lokasi_list'][$i]['kode']);
                                            }
                                        }
                                        $field = 'lokasi[]';
                                        $option = null;
                                        $data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko', null, 'nama_toko asc');
                                        foreach($data_option as $row){ $option[$row['Kode']] = $row['nama_toko']; }
                                        echo form_multiselect($field, $option, $array_lokasi, array('class' => 'select2', 'id'=>'lokasi'));
                                        ?>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox" type="checkbox">
                                            <label for="checkbox">
                                                Select All
                                            </label>
                                        </div>
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
								<?php $access_menu = array(
									'Cashier'=>array( //0-10
										0=>'Diskon Item',
										1=>'Item Void',
										2=>'Item Correct',
										3=>'Open Member',
										4=>'Input Member',
										5=>'All Void',
										6=>'Diskon Total Persen',
										7=>'Diskon Total Rp',
										8=>'Daftar Sales',
										9=>'Receipt Amount',
										10=>'Paid Out',
										11=>'Open Price',
										12=>'Kredit',
										13=>'Hold',
										14=>'Recall',
										15=>'Open Drawer',
										16=>'Reprint',
										17=>'Switch Harga Jual',
										18=>'Topup Pulsa Elektrik',
										19=>'Kasir Report',
										20=>'Retur Penjualan',
										21=>'Qty',
										22=>'Stock Minus',
										23=>'Input Barang Not Found',
										24=>'Sinkronisasi',
										25=>'Stock Opname',
										26=>'Retur Produk',
										27=>'Penerimaan Alokasi',
										28=>'Cancel Order',
										//29=>'Edit Assembly',
										//30=>'Delete Assembly',
										31=>'Order',
										32=>'Expedisi'
									)
								); ?>
								<input type="hidden" id="jumlah" name="jumlah" value="32" />
								<div class="form-group ">
									<label class="control-label col-lg-2">Cashier Access</label>
									<div class="col-lg-10">
										<?php foreach($access_menu as $row => $value){ ?>
											<div class="col-lg-2">
												<?php $field = $row; ?>
												<div class="checkbox checkbox-primary">
													<input class="form-control" type="checkbox" id="<?=str_replace(' ', '_', $field)?>" name="<?=$field?>" value="1" />
													<label for="<?=$field?>" style="color:red"> <?=$row?></label>
												</div>
												<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
											</div>
											<?php if(is_array($value)){ ?>
												<div class="col-lg-12 form-inline">
													<?php foreach($value as $rows => $values){ ?>
														<?php $field = $rows; ?>
														<div class="col-lg-3 checkbox checkbox-primary">
															<input class="form-control <?=str_replace(' ', '_', $row)?>" type="checkbox" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)==1)?'checked':((isset($menu_pos['Otorisasi'])&&substr($menu_pos['Otorisasi'],$field,1)==1)?'checked':null)?> />
															<label for="<?=$field?>"> <?=$values?></label>
														</div><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													<?php } ?>
												</div>
											<?php } ?>
											<script>
											$("#<?=str_replace(' ', '_', $row)?>").click(function () {
												if ($("#<?=str_replace(' ', '_', $row)?>").is(":checked")) {
													$(".<?=str_replace(' ', '_', $row)?>").prop('checked', true);
												} else {
													$(".<?=str_replace(' ', '_', $row)?>").prop('checked', false);
												}
											});
											</script>
										<?php } ?>
									</div>
								</div>

								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" onclick="valid_form()" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
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

<Script type="text/javascript">
function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/cek_data/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data) {
				if(data==1){
				    $("#ntf_username").text(pesan);
				    $("#save").prop('disabled', true);
					//alert(pesan);
					//if(tipe=='error'){ alert('error'); }
					//else if(tipe=='warning'){ alert('warning'); }
				} else {
				    $("#ntf_username").text('');
                    $("#save").prop('disabled', false);
                }
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
		});
	}
}

$("#checkbox").click(function(){
    if($("#checkbox").is(':checked') ){
        $("#lokasi > option").prop("selected","selected");
        $("#lokasi").trigger("change");
    }else{
        $("#lokasi > option").removeAttr("selected");
        $("#lokasi").trigger("change");
    }
});

function valid_form(){
	valid_conf_password();
}

function valid_conf_password(){
var i = document.getElementById("conf_password");
var password = $('#password').val();
var conf_password = $('#conf_password').val();

	if (i.validity.valueMissing == true){
		i.setCustomValidity("Don't empty");
	}
	else if (password!=conf_password){
		i.setCustomValidity("Not match");
	}
	else{
		i.setCustomValidity("");
	}
}
</script>

