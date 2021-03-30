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
							
							<?php $caption_setting = $this->m_crud->get_data('Setting', 'as_group1, as_group2', "Kode = '1111'"); ?>
							<?php $caption_harga = $this->m_crud->get_data('harga', 'hrg1, hrg2, hrg3, hrg4', "Kode = '1111'"); ?>
							<div class="row">
                                <div class="col-lg-8">
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Kode Barang</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'kd_brg'; ?>
                                            <input class="form-control" type="text" maxlength="21" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Kode barang sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true"/>
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                            <b class="error" id="ntf_<?=$field?>"></b>
                                            <input type="hidden" id="param_<?=$field?>" value="0">
                                            <input type="hidden" id="temp_<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Barcode</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'barcode'; ?>
                                            <input class="form-control" type="text" maxlength="21" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Barcode sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                            <b class="error" id="ntf_<?=$field?>"></b>
                                            <input type="hidden" id="param_<?=$field?>" value="0">
                                            <input type="hidden" id="temp_<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Nama Barang</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'nm_brg'; ?>
                                            <input class="form-control" type="text" maxlength="20" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2"><?=$menu_group['as_deskripsi']?></label>
                                        <div class="col-lg-8">
                                            <?php $field = 'Deskripsi'; ?>
                                            <input class="form-control" type="text" maxlength="30" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', '<?=$menu_group['as_deskripsi']?> sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                            <b class="error" id="ntf_<?=$field?>"></b>
                                            <input type="hidden" id="param_<?=$field?>" value="0">
                                            <input type="hidden" id="temp_<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Kelompok</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'kel_brg';
                                            $option = null; $option[''] = '-- Kelompok --';
                                            //$option['all'] = 'All';
                                            $data_option = $this->m_crud->read_data('kel_brg', 'kel_brg, nm_kel_brg', null, 'nm_kel_brg asc');
                                            foreach($data_option as $row){ $option[$row['kel_brg']] = $row['nm_kel_brg'].' | '.$row['kel_brg']; }
                                            echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'onchange'=>'cek_group2()', 'id'=>$field, 'required'=>'required'));
                                            ?><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2"><?=$caption_setting['as_group1']?></label>
                                        <div class="col-lg-8">
                                            <?php $field = 'Group1';
                                            $option = null; $option[''] = '-- '.$caption_setting['as_group1'].' --';
                                            //$option['all'] = 'All';
                                            $data_option = $this->m_crud->read_data('Group1', 'Kode, Nama', null, 'Nama asc');
                                            foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                            echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                            ?>
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2"><?=$caption_setting['as_group2']?></label>
                                        <div class="col-lg-8">
                                            <?php $field = 'Group2';
                                            $option = null; $option[''] = '-- '.$caption_setting['as_group2'].' --';
                                            //$option['all'] = 'All';
                                            $data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
                                            foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                            echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                            ?>
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Satuan</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'satuan'; ?>
                                            <input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Harga Beli</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'hrg_beli'; $field_beli = 'hrg_beli'?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Harga Jual</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'hrg_jual_1'; $field_jual = 'hrg_jual_1'?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Margin</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'margin';?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field_jual)?set_value($field_jual):((isset($master_data[$field_jual]) && isset($master_data[$field_beli]) && $master_data[$field_beli] != 0)?round((($master_data[$field_jual]-$master_data[$field_beli]) / $master_data[$field_beli])*100, 2):null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Diskon</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'diskon';?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">PPN</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'ppn';?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Stock Min</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'stock_min'; ?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Kode Packing</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'kd_packing'; ?>
                                            <input class="form-control" type="text" maxlength="13" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Kode sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>"/>
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                            <b class="error" id="ntf_<?=$field?>"></b>
                                            <input type="hidden" id="param_<?=$field?>" value="0">
                                        </div>
                                    </div>
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label class="control-label col-lg-2">Qty Packing</label>
                                        <div class="col-lg-8">
                                            <?php $field = 'qty_packing'; ?>
                                            <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>"/>
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="kategori" id="kategori" value="Non Paket">
                                    <input type="hidden" name="Jenis" id="Jenis" value="Barang Dijual">
                                    <!--<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kategori</label>
									<div class="col-lg-8">
										<?php /*$field = 'kategori';
										$option = null; $option[''] = '-- Kategori --';
										$option['Paket'] = 'Paket';
										$option['Non Paket'] = 'Non Paket';
										//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
										//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
										*/?>
										<p style="font-size:10px; color:red;">Kategori Paket tidak akan mengurangi stock, sedangkan Non Paket akan mengurangi stock</p>
										<?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
									</div>
								</div>
								<div class="form-group " style="">
									<label class="control-label col-lg-2">Jenis Barang</label>
									<div class="col-lg-8">
										<?php /*$field = 'Jenis';
										$option = null; $option[''] = '-- Jenis Barang --';
										$option['Barang Dijual'] = 'Barang Dijual';
										$option['Barang Tidak Dijual'] = 'Barang Tidak Dijual';
										//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
										//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
										*/?>
										<?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
									</div>
								</div>-->
                                    <div class="form-group">
                                        <div class="pull-right col-lg-10">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
                                        </div>
                                    </div>
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
    function cek_simpan() {
        var kd_brg = parseInt($("#param_kd_brg").val());
        var barcode = parseInt($("#param_barcode").val());
        var deskripsi = parseInt($("#param_Deskripsi").val());

        if ((kd_brg + barcode + deskripsi) != 0) {
            $("#save").prop("disabled", true);
        } else {
            $("#save").prop("disabled", false);
        }
    }

function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	var temp = $("#temp_"+column).val();
	if(id != ''){
	    if (id != temp.toLowerCase() && id != temp.toUpperCase()) {
            $.ajax({
                type: 'GET',
                url: '<?=site_url()?>site/cek_data_2/' + btoa(table) + '/' + btoa(column) + '/' + btoa(id),
                //data: {delete_id : id},
                success: function (data) {
                    if (data == 1) {
                        $("#param_" + column).val(1);
                        $("#ntf_" + column).text(pesan);
                        cek_simpan();
                        //if(tipe=='error'){ alert('error'); }
                        //else if(tipe=='warning'){ alert('warning'); }
                    } else {
                        $("#param_" + column).val(0);
                        $("#ntf_" + column).text('');
                        cek_simpan();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Check Data Failed');
                }
            });
        } else {
            $("#param_" + column).val(0);
            $("#ntf_" + column).text('');
            cek_simpan();
        }
	}
}

$("#kel_brg").change(function () {
    var kode = $(this).val();
    if (kode != "") {
        $.ajax({
            url: "<?=base_url()?>site/max_kode_barang/" + btoa(kode),
            type: "GET",
            success: function (res) {
                $("#kd_brg").val(res);
            }
        });
    } else {
        $("#kd_brg").val("");
    }
});

/*$("#Group2").change(function () {
    var kode = $(this).val();
    $.ajax({
        url: "<=base_url()?>site/get_list_dropdown/" + btoa("kel_brg") + "/" + btoa("kel_brg id, nm_kel_brg name") + "/" + btoa("Group2") + "/" + btoa(kode) + "/" + btoa("-- Kelompok --"),
        type: "GET",
        success: function (res) {
            $("#kel_brg").html(res).val("").change();
        }
    });
});*/

function barcode_kd_brg(){
	$('#barcode').val($('#kd_brg').val());
}

function cek_group2(){
	var id = $('#kel_brg').val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/get_data/kel_brg/Group2/kel_brg/' + id,
			//data: {delete_id : id},
			success: function (data) { 
				$("#Group2").select2("val", data);
			}, 
			error: function (jqXHR, textStatus, errorThrown){ alert('Get Data Failed'); }
		});
	}
}

function hitung_margin(field1, field2, tipe, id = 0) {
	var harga_beli = parseFloat($("#hrg_beli").val());
	var hasil = '';

	if (!isNaN(harga_beli)) {
		if (tipe == 'hrg_jual') {
			if (harga_beli <= parseFloat($('#' + field1).val())) {
				hasil = ((1 - (harga_beli/parseFloat($('#' + field1).val()))) * 100).toFixed(2);
			}
		} else {
			if ($('#' + field1).val() > 100) {
				$('#' + field1).val(99)
			}
			if ($('#' + field1).val() < 0) {
				$('#' + field1).val(0)
			}

			hasil = (harga_beli / (1 - (parseFloat($('#' + field1).val()) / 100))).toFixed(2);
		}

		$('#' + field2).val(hasil);

		if (id == 0) {
			atur_sama(field2);
		}
	} else {
		if (tipe == 'hrg_jual') {
			$('#' + field1).val(0);
		} else {
			$('#' + field1).val(0);
		}
	}
}

function atur_sama(field){
	for(var i=1; i<=$('#jumlah_lokasi').val(); i++){
		if ($("#cek_lokasi"+i).is(":checked")) {
			$('#'+field+i).val($('#'+field).val());
		} 
	}
}

function checked_lokasi(x=0){
	if(x==0){
		var awal = 1; var akhir = $('#jumlah_lokasi').val();
	} else {
		var awal = x; var akhir = x;
	}
	
	for(var i=awal; i<=akhir; i++){
		if ($("#cek_lokasi"+i).is(":checked")) {
			$('#hrg_jual_1' + i).prop('readonly', false);
			$('#hrg_jual_2' + i).prop('readonly', false);
			$('#hrg_jual_3' + i).prop('readonly', false);
			$('#hrg_jual_4' + i).prop('readonly', false);
			$('#margin' + i).prop('readonly', false);
			$('#disc1' + i).prop('readonly', false);
			$('#ppn' + i).prop('readonly', false);
		} else { 
			$('#hrg_jual_1' + i).prop('readonly', true);
			$('#hrg_jual_2' + i).prop('readonly', true);
			$('#hrg_jual_3' + i).prop('readonly', true);
			$('#hrg_jual_4' + i).prop('readonly', true);
			$('#margin' + i).prop('readonly', true);
			$('#disc1' + i).prop('readonly', true);
			$('#ppn' + i).prop('readonly', true);
			$('#hrg_jual_1' + i).val('');
			$('#hrg_jual_2' + i).val('');
			$('#hrg_jual_3' + i).val('');
			$('#hrg_jual_4' + i).val('');
			$('#margin' + i).val('');
			$('#disc1' + i).val('');
			$('#ppn' + i).val('');
		} 
	}
}

$("#cek_lokasi").click(function () {
	if ($("#cek_lokasi").is(":checked")) {
		$(".cek_lokasi").prop('checked', true);
	} else {
		$(".cek_lokasi").prop('checked', false);
	}
	checked_lokasi();
});
</script>

