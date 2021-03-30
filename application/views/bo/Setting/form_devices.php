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

                            <?php
                            if (isset($_GET['trx'])) { ?>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Device ID</label>
                                <div class="col-lg-10">
                                    <?php
                                    $field = 'device_id';
                                    ?>
                                    <input type="text" class="form-control" value="<?=$master_data[$field]?>" readonly>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">MAC Address</label>
                                <div class="col-lg-10">
                                    <?php $field = 'mac_address'; ?>
                                    <input class="form-control" autocomplete="off" data-mask="**:**:**:**:**:**" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>"/>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <?php } else { ?>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Device ID</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'device_id';
                                        $option = null; $option[''] = '-- Device ID --';
                                        //$option['all'] = 'All';
                                        $data_option = $this->m_crud->read_data('device_id', 'device_id', "status='0'", 'device_id asc');
                                        foreach($data_option as $row){ $option[$row['device_id']] = $row['device_id']; }
                                        echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                        ?>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Jenis Device</label>
                                <div class="col-lg-10">
                                    <?php $field = 'jenis_device';
                                    $option = null;
                                    $option['Tab'] = 'Tab';
                                    $option['PC'] = 'PC';
                                    echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field));
                                    ?>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Lokasi Cabang</label>
                                <div class="col-lg-10">
                                    <?php $field = 'lokasi';
                                    $option = null; $option[''] = '-- Lokasi --';
                                    //$option['all'] = 'All';
                                    $data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
                                    $ok = '-';
                                    foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                    echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'onchange'=>"".(isset($_GET['trx']))?'get_kassa($(this).val(), \''.$_GET['trx'].'\')':'get_kassa($(this).val(), \'LQ==\')'."", 'required'=>'required'));
                                    ?>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Kassa</label>
                                <div class="col-lg-10">
                                    <?php $field = 'kassa';
                                    $option = null;  $option[''] = '-- Kassa --';
                                    $s = 'A';
                                    if (isset($_GET['trx'])) {
                                        while($s != '[')
                                        {
                                            if (!in_array($s, $k, true)) {
                                                $option[$s] = $s;
                                            }
                                            $s = chr(ord($s) + 1);
                                        }
                                    }
                                    echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'form-control', 'id'=>$field, 'required'=>'required'));
                                    ?>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Printer Model</label>
                                <div class="col-lg-10">
                                    <?php $field = 'printer_model';
                                    $option = null;
                                    $option['-'] = '-- Printer Model --';
                                    //$option['all'] = 'All';
                                    $data_option = $this->m_crud->read_data('data_printer', 'printer_model', null, 'printer_model asc', 'printer_model');
                                    foreach($data_option as $row){ $option[$row['printer_model']] = $row['printer_model']; }
                                    echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'form-control', 'id'=>$field, 'onchange'=>"get_printer_series($(this).val())"));
                                    ?>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Printer Series</label>
                                <div class="col-lg-10">
                                    <?php $field = 'printer_series'; ?>
                                    <?php
                                    $option = null;
                                    $option['-'] = '-- Printer Series --';
                                    //$option['all'] = 'All';
                                    $data_option = $this->m_crud->read_data('data_printer', 'printer_id, printer_series', isset($_GET['trx'])?"printer_model='".$master_data['printer_model']."'":null, 'printer_model asc');
                                    if (isset($_GET['trx'])) {
                                        foreach ($data_option as $row) {
                                            $option[$row['printer_id']] = $row['printer_series'];
                                        }
                                    }
                                    echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'form-control', 'id'=>$field));
                                    ?>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Printer Address</label>
                                <div class="col-lg-10">
                                    <?php $field = 'printer_address'; ?>
                                    <input class="form-control" autocomplete="off" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>"/>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Paper Size</label>
                                <div class="col-lg-10">
                                    <?php $field = 'paper';
                                    $option = null;
                                    $option['32'] = '32';
                                    $option['48'] = '48';
                                    echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'form-control', 'id'=>$field));
                                    ?>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
							<div class="form-group " style="margin-bottom:5px;">
								<label class="control-label col-lg-2">Auto Cutter</label>
								<div class="col-lg-10 form-inline">
									<?php $field = 'auto_cutter'; ?>
									<div class="radio radio-primary">
										<input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="true" <?=($master_data[$field]=='true')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
										<label for="<?=$field?>1"> Aktif </label>
									</div>
									<div class="radio radio-primary">
										<input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="false" <?=($master_data[$field]=='false')?'checked':null?> required />
										<label for="<?=$field?>0"> Tidak Aktif </label>
									</div>
									<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
								</div>
							</div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Open Drawer</label>
                                <div class="col-lg-10 form-inline">
                                    <?php $field = 'open_drawer'; ?>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="true" <?=($master_data[$field]=='true')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
                                        <label for="<?=$field?>1"> Aktif </label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="false" <?=($master_data[$field]=='false')?'checked':null?> required />
                                        <label for="<?=$field?>0"> Tidak Aktif </label>
                                    </div>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Scanner</label>
                                <div class="col-lg-10 form-inline">
                                    <?php $field = 'scanner'; ?>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="true" <?=($master_data[$field]=='true')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
                                        <label for="<?=$field?>1"> Aktif </label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="false" <?=($master_data[$field]=='false')?'checked':null?> required />
                                        <label for="<?=$field?>0"> Tidak Aktif </label>
                                    </div>
                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                </div>
                            </div>
                            <div class="form-group " style="margin-bottom:5px;">
                                <label class="control-label col-lg-2">Fast Pay</label>
                                <div class="col-lg-10 form-inline">
                                    <?php $field = 'fast_pay'; ?>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="true" <?=($master_data[$field]=='true')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
                                        <label for="<?=$field?>1"> Aktif </label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="false" <?=($master_data[$field]=='false')?'checked':null?> required />
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

    function get_printer_series(id) {
        $.ajax({
            url: "<?=site_url()?>setting/get_printer_series/" + btoa(id),
            type: "GET",
            success: function (res) {
                $("#printer_series").html(res);
            }
        });
    }

    function get_kassa(lokasi, id) {
        $.ajax({
            url: "<?=site_url()?>setting/get_kassa/" + btoa(lokasi) + "/" + id,
            type: "GET",
            success: function (res) {
                $("#kassa").html(res);
            }
        });
    }

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
