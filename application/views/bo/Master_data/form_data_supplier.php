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
							<?=isset($_GET['trx'])?'<input type="hidden" id="tmp_alamat" name="tmp_alamat" value="'.$master_data['alamat'].'" />':''; ?>
							<div class="col-lg-4">
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Kode</label>
									<div class="col-lg-9">
										<?php $field = 'kode'; ?>
										<input class="form-control" autocomplete="off" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Nama</label>
									<div class="col-lg-9">
										<?php $field = 'Nama'; ?>
										<input class="form-control" autocomplete="off" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Status</label>
									<div class="col-lg-9 form-inline">
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
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Cara Bayar</label>
									<div class="col-lg-9 form-inline">
										<?php $field = 'carabyr'; ?>
										<div class="radio radio-primary">
											<input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="Kredit" <?=($master_data[$field]=='Kredit')?'checked':(isset($_GET['trx'])?null:'checked')?> required />
											<label for="<?=$field?>1"> Kredit </label>
										</div>
										<div class="radio radio-primary">
											<input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="Tunai" <?=($master_data[$field]=='Tunai')?'checked':null?> required />
											<label for="<?=$field?>0"> Tunai </label>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Alamat</label>
									<div class="col-lg-9">
										<?php $field = 'Alamat'; (isset($master_data[$field]))?$alamat = explode('|', $master_data[$field]):null; ?>
										<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$alamat[0]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Kota</label>
									<div class="col-lg-9">
										<?php $field = 'kota'; (isset($master_data[$field]))?$alamat = explode('|', $master_data[$field]):null; ?>
										<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$alamat[0]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Telp</label>
									<div class="col-lg-9">
										<?php $field = 'telp'; ?>
										<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Kontak</label>
									<div class="col-lg-9">
										<?php $field = 'kontak'; (isset($master_data[$field]))?$alamat = explode('|', $master_data[$field]):null; ?>
										<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$alamat[0]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">No. Kontak</label>
									<div class="col-lg-9">
										<?php $field = 'no_kontak'; ?>
										<input class="form-control" type="text" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-3">Email</label>
									<div class="col-lg-9">
										<?php $field = 'email'; ?>
										<input class="form-control" type="email" autocomplete="off" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
									</div>
								</div>
							</div>
							<div class="col-lg-8">
                                <ul class="nav nav-tabs tabs">
                                    <li class="active tab">
                                        <a href="#rekening" data-toggle="tab" aria-expanded="false">
                                            <span class="visible-xs">Rekening</span>
                                            <span class="hidden-xs">Rekening</span>
                                        </a>
                                    </li>
                                </ul> 
                                <div class="tab-content" style="max-height: 480px; overflow: auto; overflow-x: hidden;"> 
                                    <div class="tab-pane active" id="rekening">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Aksi</th>
                                                <th>Bank</th>
                                                <th>Rekening</th>
                                                <th>Atas Nama</th>
                                                <th>BI Code</th>
                                                <th>Branch Name</th>
                                            </tr>
                                            </thead>
                                            <tbody id="list_rekening">
                                            </tbody>
                                            <input type="hidden" id="max_data" name="max_data" value="0">
                                        </table>
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
	$(document).ready(function () {
        if ('<?=$this->uri->segment(3)?>' == 'edit') {
            $.ajax({
                url: "<?=base_url().'master_data/get_supplier_rek/'.$_GET['trx']?>",
                type: "GET",
                dataType: "JSON",
                success: function (res) {
                    if (res.status>0) {
                        for (var i = 0; i<res.list.length; i++) {
                            var data = {bank: res.list[i]['bank'], rek: res.list[i]['rekening'], an: res.list[i]['an'], bi: res.list[i]['bi_code'], branch: res.list[i]['branch_name']};
                            array_rekening.push(data);
                        }
                        add_list('-');
                    } else {
                        add_list('x');
                    }
                }
            });
        } else {
            add_list('-');
        }
    }).on("keypress", ":input:not(textarea)", function(event) {
        return event.keyCode != 13;
    });
	
	var array_rekening = [];
    var data_bank = <?=$json_data_bank?>;
	
	function select_bank(name, data_option, select=''){
		var data = '';
		var selected;
		
		data += '<select class="select2" id="'+name+'" name="'+name+'">';
		data += '<option value="">Pilih</option>';
		for (var i=0; i<data_option.length; i++) {
			if(select!='' && select==data_option[i]['Nama']){ selected = 'selected'; } else { selected = ''; }
			data += '<option value="'+data_option[i]['Nama']+'" '+selected+'>'+data_option[i]['Nama']+'</option>';
		}
		data += '</select>';
			
		return data;
	}
	
	function update_array_rekening(){
		array_rekening = [];
		var bank;
		var rek;
		var an;
		var bi;
		var branch;
		var data;
		
		for(var i=0; i<$("#max_data").val(); i++){
			bank = $("#bank_"+i).val();
			rek = $("#rek_"+i).val();
			an = $("#an_"+i).val();
			bi = $("#bi_"+i).val();
			branch = $("#branch_"+i).val();
			if (bank != '' && rek != '' && an != '' && bi != '' && branch != '') {
				data = {bank: bank, rek: rek, an: an, bi: bi, branch: branch};
				array_rekening.push(data);
			}
		}
	}
	
	function add_list(id) {
        var new_list = '';
        var max_data = parseInt(document.getElementById("max_data").value);
		if(id!='-' && id!='x'){ update_array_rekening(); }

        if (id != '-' && id != 'x') {
            var bank = $("#bank_"+id).val();
            var rek = $("#rek_"+id).val();
            var an = $("#an_"+id).val();
            var bi = $("#bi_"+id).val();
            var branch = $("#branch_"+id).val();

            if (bank != '' && rek != '' && an != '' && bi != '' && branch != '') {
                var data = {bank: bank, rek: rek, an: an, branch: branch, bi: bi};
                array_rekening.push(data);

                for (var x = 0; x < array_rekening.length; x++) {
                    new_list += '<tr>' +
                        '<td><button type="button" id="add_rekening_' + x + '" onclick="add_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_rekening_' + x + '" onclick="remove_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                        '<td>' + select_bank('bank_'+x, data_bank, array_rekening[x].bank) + '</td>' +
						'<td><input type="text" value="' + array_rekening[x].rek + '" class="form-control" id="rek_' + x + '" name="rek_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_rekening[x].an + '" class="form-control" id="an_' + x + '" name="an_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_rekening[x].bi + '" class="form-control" id="bi_' + x + '" name="bi_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_rekening[x].branch + '" class="form-control" id="branch_' + x + '" name="branch_' + x + '"></td>' +
                        '</tr>';

                    max_data = x + 1;
                }
			} else {
                return false;
            }
        } else {
            if ('<?=$this->uri->segment(3)?>' == 'edit' && id != 'x') {
                for (var x = 0; x < array_rekening.length; x++) {
                    new_list += '<tr>' +
                        '<td><button type="button" id="add_rekening_' + x + '" onclick="add_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_rekening_' + x + '" onclick="remove_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                        '<td>' + select_bank('bank_'+x, data_bank, array_rekening[x].bank) + '</td>' +
						'<td><input type="text" value="' + array_rekening[x].rek + '" class="form-control" id="rek_' + x + '" name="rek_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_rekening[x].an + '" class="form-control" id="an_' + x + '" name="an_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_rekening[x].bi + '" class="form-control" id="bi_' + x + '" name="bi_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_rekening[x].branch + '" class="form-control" id="branch_' + x + '" name="branch_' + x + '"></td>' +
                        '</tr>';

                    max_data = x + 1;
                }
			}
        }

        new_list += '<tr>' +
            '<td><button type="button" id="add_rekening_'+(max_data)+'" onclick="add_list('+(max_data)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_rekening_'+(max_data)+'" onclick="remove_list('+(max_data)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
            '<td>' + select_bank('bank_'+max_data, data_bank) + '</td>' +
			'<td><input type="text" onkeydown="return isNumber(event,\'+\')" class="form-control" id="rek_'+(max_data)+'" name="rek_'+(max_data)+'"><b class="error" id="ntf_sampai"></b></td>' +
            '<td><input type="text" class="form-control" id="an_'+(max_data)+'" name="an_'+(max_data)+'"></td>' +
            '<td><input type="text" class="form-control" id="bi_'+(max_data)+'" name="bi_'+(max_data)+'"></td>' +
            '<td><input type="text" class="form-control" id="branch_'+(max_data)+'" name="branch_'+(max_data)+'"></td>' +
            '</tr>';

        document.getElementById("max_data").value = max_data;
        document.getElementById("list_rekening").innerHTML = new_list;

        disable_form(max_data);
    }

    function remove_list(id) {
        var new_list = '';
        var max_data = 0;

        array_rekening.splice(id, 1);

        for (var x = 0; x < array_rekening.length; x++) {
            new_list += '<tr>' +
                '<td><button type="button" id="add_rekening_'+x+'" onclick="add_list('+x+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_rekening_'+x+'" onclick="remove_list('+x+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                '<td>' + select_bank('bank_'+x, data_bank, array_rekening[x].bank) + '</td>' +
				'<td><input type="text" value="'+array_rekening[x].rek+'" class="form-control" id="rek_'+x+'" name="rek_'+x+'"></td>' +
                '<td><input type="text" value="'+array_rekening[x].an+'" class="form-control" id="an_'+x+'" name="an_'+x+'"></td>' +
                '<td><input type="text" value="'+array_rekening[x].bi+'" class="form-control" id="bi_'+x+'" name="bi_'+x+'"></td>' +
                '<td><input type="text" value="'+array_rekening[x].branch+'" class="form-control" id="branch_'+x+'" name="branch_'+x+'"></td>' +
                '</tr>';

            max_data = x+1;
        }

        new_list += '<tr>' +
            '<td><button type="button" id="add_rekening_'+(max_data)+'" onclick="add_list('+(max_data)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_rekening_'+(max_data)+'" onclick="remove_list()" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
            '<td>' + select_bank('bank_'+max_data, data_bank) + '</td>' +
			'<td><input type="text" onkeydown="return isNumber(event, \'+\')" class="form-control" id="rek_'+(max_data)+'" name="rek_'+(max_data)+'"><b class="error" id="ntf_sampai"></b></td>' +
            '<td><input type="text" class="form-control" id="an_'+(max_data)+'" name="an_'+(max_data)+'"></td>' +
            '<td><input type="text" class="form-control" id="bi_'+(max_data)+'" name="bi_'+(max_data)+'"></td>' +
            '<td><input type="text" class="form-control" id="branch_'+(max_data)+'" name="branch_'+(max_data)+'"></td>' +
            '</tr>';

        document.getElementById("max_data").value = max_data;
        document.getElementById("list_rekening").innerHTML = new_list;

        disable_form(max_data);
    }

    function valid_qty(id) {
        var dari = parseInt($("#q1_"+id).val());
        var sampai = parseInt($("#q2_"+id).val());

        if (sampai < dari) {
            $("#ntf_sampai").text("Qty 2 harus lebih besar dari Qty 1!");
            $("#save").prop('disabled', true);
            $("#add_harga_"+id).prop('disabled', true);
        } else if (isNaN(sampai)) {
            $("#ntf_sampai").text("Qty 2 harus lebih dari 0!");
            $("#save").prop('disabled', true);
            $("#add_harga_"+id).prop('disabled', true);
        } else {
            hide_notif('ntf_sampai');
            $("#save").prop('disabled', false);
            $("#add_harga_"+id).prop('disabled', false);
        }
    }

    function disable_form(id) {
        var x = 0;
        var y = 0;
        for (x; x<id; x++) {
            $("#remove_rekening_"+x).show();
            $("#add_rekening_"+x).hide();
            $("#bank_"+x).prop('readonly', false);
            $("#rek_"+x).prop('readonly', false);
            $("#an_"+x).prop('readonly', false);
            $("#bi_"+x).prop('readonly', false);
            $("#branch_"+x).prop('readonly', false);
            y = x;
        }

        $("#remove_rekening_"+y).show();
        $("#remove_rekening_"+id).hide();
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
