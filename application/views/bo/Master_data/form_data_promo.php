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
                                    <label class="control-label col-lg-2">Option</label>
                                    <div class="col-lg-8">
                                        <div class="form-inline">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox_member" name="member" value="1" type="checkbox">
                                                <label for="checkbox_member">
                                                    Hanya Member
                                                </label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox_periode" name="periode" value="1" type="checkbox">
                                                <label for="checkbox_periode">
                                                    Tanpa Periode
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Kategori</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'kategori';
                                        $option = null;
                                        $option['brg'] = 'Barang';
                                        $option['kel_brg'] = 'Kel. Barang';
                                        $option['supplier'] = 'Supplier';
                                        //$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
                                        //foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                        echo form_dropdown($field, $option, isset($master_data['cat_promo'])?$master_data['cat_promo']:set_value($field), array('class' => 'select2', 'id'=>$field, 'onchange'=>'change_category($(this).val())', 'required'=>'required'));
                                        ?>
                                    </div>
                                </div>
								<div class="form-group" id="container_barang" style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kode Barang</label>
									<div class="col-lg-8">
										<?php $field = 'kd_brg'; ?>
										<input class="form-control autocomplete_data" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=(isset($master_data['cat_promo']) && $master_data['cat_promo']=='brg')?$master_data['kode']:set_value($field)?>" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group" id="container_kel_brg" style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Kel. Barang</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'kel_brg';
                                        $option = null;
                                        $data_option = $this->m_crud->read_data('kel_brg, Group2', 'kel_brg, nm_kel_brg, Nama', 'kel_brg.Group2=Group2.Kode', 'nm_kel_brg asc');
                                        foreach($data_option as $row){ $option[$row['kel_brg']] = $row['nm_kel_brg'].' | '.$row['Nama']; }
                                        echo form_dropdown($field, $option, (isset($master_data['cat_promo']) && $master_data['cat_promo']=='kel_brg')?$master_data['kode']:set_value($field), array('class' => 'select2', 'id'=>$field));
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group" id="container_supplier" style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Supplier</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'supplier';
                                        $option = null;
                                        $data_option = $this->m_crud->read_data('Supplier', 'Kode, Nama', 'Nama <> \'\'', 'Nama asc');
                                        foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                        echo form_dropdown($field, $option, (isset($master_data['cat_promo']) && $master_data['cat_promo']=='supplier')?$master_data['kode']:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                        ?>
                                    </div>
                                </div>
								<div class="form-group cont_periode" style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Tanggal Mulai</label>
									<div class="col-lg-8">
                                        <?php $field = 'dariTgl'; ?>
                                        <div class="input-group">
                                            <input type="text" class="form-control datepicker_date" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?substr($master_data[$field],0,10):(set_value($field)?set_value($field):date("Y-m-d"))?>" required readonly>
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group cont_periode" style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Tanggal Selesai</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'sampaiTgl'; ?>
                                        <div class="input-group">
                                            <input type="text" class="form-control datepicker_date" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?substr($master_data[$field],0,10):(set_value($field)?set_value($field):date("Y-m-d"))?>" required readonly>
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
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
                                        $data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', null, 'Nama asc');
                                        foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
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
									<label class="control-label col-lg-2">Tipe Diskon</label>
									<div class="col-lg-8">
										<?php $field = 'pildiskon'; ?>
										<div class="radio radio-primary radio-inline">
											<input type="radio" onchange="change_diskon($(this).val())" id="<?=$field?>1" value="%" name="<?=$field?>" <?=(isset($master_data[$field]) && $master_data[$field]=='%')?'checked':''?>>
											<label for="<?=$field?>1"> % </label>
										</div>
										<div class="radio radio-primary radio-inline">
											<input type="radio" onchange="change_diskon($(this).val())" id="<?=$field?>2" value="money" name="<?=$field?>" <?=(isset($master_data[$field]) && $master_data[$field]=='money')?'checked':''?>>
											<label for="<?=$field?>2"> Rp </label>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Diskon</label>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-lg-9">
                                                <?php $field = 'diskon'; ?>
                                                <input type="number" class="form-control" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?($master_data[$field]+0):set_value($field)?>" required>
                                                <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                            </div>
                                            <!--<div class="col-lg-1 text-center" id="plus">
                                                <i class="ion-android-add"></i>
                                            </div>
                                            <div class="col-lg-4" id="d2">
                                                <?php /*$field = 'diskon2'; */?>
                                                <input type="number" class="form-control" placeholder="" id="<?/*=$field*/?>" name="<?/*=$field*/?>" value="<?/*=isset($master_data[$field])?($master_data[$field]+0):set_value($field)*/?>" required>
                                                <?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
                                            </div>-->
                                        </div>
                                    </div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Gambar</label>
                                    <div class="col-lg-8">
										<?php if(isset($master_data['gambar']) && $master_data['gambar']!=null){ ?>
											<input type="hidden" name="logo_gambar" value="<?=$master_data['gambar']?>">
											<img width="200" src="<?=$this->config->item('url').$master_data['gambar']?>" />
										<?php } ?>
										<input type="file" name="gambar" id="gambar" />
										<font color='red'><?php if(isset($error_gambar)){ echo $error_gambar; } ?></font>
									</div>
                                </div>
								
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" onclick="valid_form()" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
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
        var param = document.getElementById('kategori').value;
        if (param == 'kel_brg') {
            change_category('kel_brg');
        } else if (param == 'supplier') {
            change_category('supplier');
        } else {
            change_category('brg');
        }

        if ('<?=$this->uri->segment(3)?>' == 'edit') {
            if ('<?=$master_data['pildiskon']?>' == 'money') {
                $("#plus").hide();
                $("#d2").hide();
                $("#diskon2").prop('disabled', true);
            }
        }
    });

    $("#checkbox_periode").click(function () {
        if($("#checkbox_periode").is(':checked') ){
            document.getElementsByClassName('cont_periode')[0].style.display = 'none';
            document.getElementsByClassName('cont_periode')[1].style.display = 'none';
        }else{
            document.getElementsByClassName('cont_periode')[0].style.display = 'block';
            document.getElementsByClassName('cont_periode')[1].style.display = 'block';
        }
    });

    function change_diskon(id) {
        if (id == 'money') {
            //$("#plus").hide();
            //$("#d2").hide();
            //$("#diskon2").prop('disabled', true);
        } else {
            //$("#plus").show();
            //$("#d2").show();
            //$("#diskon2").prop('disabled', false);
        }
    }

    function change_category(param) {
        if (param == 'kel_brg') {
            $('#container_kel_brg').show();
            $('#container_supplier').hide();
            $('#container_barang').hide();
        } else if (param == 'supplier') {
            $('#container_supplier').show();
            $('#container_barang').hide();
            $('#container_kel_brg').hide();
        } else {
            $('#container_barang').show();
            $('#container_kel_brg').hide();
            $('#container_supplier').hide();
        }
    }

	function valid_form(){
		valid_kd_barang();
	}

	function valid_kd_barang(){
		var i = document.getElementById("kd_brg").split("|");
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1;
		var yyyy = today.getFullYear();

		if (i.validity.valueMissing == true){
			i.setCustomValidity("Don't empty");
		} else {
			var condition = "kd_brg='"+i[0]+"' AND sampaitgl<='"+yyyy+"/"+("0" + (mm)).slice(-2)+"/"+("0" + (dd)).slice(-2)+" 00:00:00'";
			$.ajax({
				url:"<?php echo base_url() . 'site/count_data/' ?>" + btoa("Promo") + "/" + btoa(condition),
				type:"GET",
				success: function (res) {
					if (res != 0){
						i.setCustomValidity("Promo available until ");
					} else {
						i.setCustomValidity("");
					}
				}
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

	var site = "<?=site_url()?>";
	$(function(){
		$('.autocomplete_data').autocomplete({
			serviceUrl: site+'site/search_autocomplete/barang/kd_brg-nm_brg/kd_brg-nm_brg'
		});
	});
</script>

