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
							<?php isset($_GET['trx'])?$where=null:$where=" AND da.kd_brg_ass is null"; ?>
							<?=form_open($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group " style="margin-bottom:5px;">
                                        <label for="cname" class="col-lg-12">Barang Paket</label>
                                        <div class="col-lg-12">
                                            <?php $field = 'kd_brg_ass'; ?>
                                            <?php  $option = null; $option[''] = '-- Select --';
										    $data_option = $this->m_crud->join_data('barang br', 'br.kd_brg, br.nm_brg', array(array('table'=>'detail_assembly da', 'type'=>'LEFT')), array('da.kd_brg_ass=br.kd_brg'), "br.kategori = 'Paket' AND br.jenis = 'Barang Dijual'".$where, 'br.nm_brg asc', 'br.kd_brg, br.nm_brg');
										    foreach($data_option as $row) {
                                                $option[$row['kd_brg']] = $row['kd_brg'] . ' - ' . $row['nm_brg'];
                                            }

										    echo form_dropdown($field, $option, isset($master_data[$field])?$master_data[$field]:set_value($field), array('id' => $field, 'class' => 'select2', 'required' => 'required', 'onchange' => 'get_hrg_jual()'));
										    ?>
                                            <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary waves-effect waves-light pull-right" type="submit" name="save" id="save"><i class="fa fa-save"></i> Save</button>
                                </div>
                                <div class="col-md-9">
                                    <div class="row" style="margin-bottom: 10px">
                                        <div class="input-group">
                                            <input type="text" placeholder="Cari Kode Barang / Barcode" class="form-control" autocomplete="off" style="height: 40px;" id="kd_brg" name="kd_brg">
                                            <div class="input-group-btn">
                                                <button onclick="add_list()" id="cari" name="cari" style="height: 40px;" type="button" class="btn btn-primary"><i class="md md-search"></i></button>
                                            </div>
                                        </div>
                                        <b class="error" id="ntf_barang"></b>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="margin-bottom:5px;">
                                            <label for="cname" class="col-lg-12">Hpp Tambahan</label>
                                            <?php $field = 'hpp_tambahan'; ?>
                                            <div class="col-lg-12">
                                                <input type="text" name="<?=$field?>" id="<?=$field?>" value="<?=isset($master_data[$field])?number_format($master_data[$field]):set_value($field)?>" class="form-control" onblur="hitung()" onkeydown="return isNumber(event)" onkeyup="isMoney('hpp_tambahan')">
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab4" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 1%">Aksi</th>
                                                            <th>Kode Barang</th>
                                                            <th>Barcode</th>
                                                            <th>Nama Barang</th>
                                                            <th>Harga Beli</th>
                                                            <th>Qty</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="list_barang">
                                                        </tbody>
                                                        <tfoot>
                                                        <tr>
                                                            <th colspan="6">Total HPP</th>
                                                            <th><span id="total"></span></th>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                    <input type="hidden" id="max_barang" name="max_barang" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6" style="margin-bottom:5px;">
                                            <label for="cname" class="col-lg-12">Harga Jual</label>
                                            <?php $field = 'hrg_jual_1'; ?>
                                            <div class="col-lg-12">
                                                <input type="text" name="<?=$field?>" id="<?=$field?>" value="<?=isset($master_data[$field])?number_format($master_data[$field]):0?>" class="form-control" onkeydown="return isNumber(event)" onkeyup="isMoney('hrg_jual_1')">
                                            </div>
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
    var array_assembly = [];

    $(document).ready(function () {
        if ('<?=$this->uri->segment(3)?>' == 'edit') {
            var list = <?=json_encode($detail_data)?>;
            for (var i=0; i<list.length; i++) {
                var data = {
                    kd_brg: list[i].kd_brg,
                    barcode: list[i].barcode,
                    nm_brg: list[i].nm_brg,
                    qty: parseInt(list[i].qty),
                    hrg_beli: list[i].hrg_beli
                };
                array_assembly.push(data);
            }
            get_list();
        }
    }).on("keypress", ":input:not(textarea)", function(event) {
        return event.keyCode != 13;
    });

    function in_array(kd_brg) {
        var status = true;

        for (i=0; i<array_assembly.length; i++) {

            if (array_assembly[i].kd_brg == kd_brg) {
                document.getElementById("ntf_barang").innerText = "Barang Sudah Dimasukan!";
                status = false;
                break;
            } else {
                document.getElementById("ntf_barang").innerText = "";
                status = true;
            }
        }

        return status;
    }

    $("#kd_brg").keyup(function (event) {
        hide_notif('ntf_barang');
        if (event.keyCode == 13) {
            add_list();
        }
    });

    function add_list(kd_brg = null) {
        if (kd_brg == null) {
            kd_brg = document.getElementById("kd_brg").value;
        }

        if (kd_brg != '') {
            $.ajax({
                url: "<?=base_url('master_data/barang_assembly/')?>" + btoa(kd_brg),
                type: "GET",
                dataType: "JSON",                
				beforeSend: function () {
					$('#loading').show();
				},
				complete: function () {
					$('#loading').hide();
				},
                success: function (res) {
                    if (res.status == '1') {
                        if (in_array(res.list.kd_brg)) {
                            var data = {
                                kd_brg: res.list.kd_brg,
                                barcode: res.list.barcode,
                                nm_brg: res.list.nm_brg,
                                hrg_beli: res.list.hrg_beli,
                                qty: 1
                            };
                            array_assembly.push(data);
                        }

                        document.getElementById("kd_brg").value = "";
                        get_list();
                    } else {
                        document.getElementById("ntf_barang").innerText = "Barang Tidak Tersedia!";
                    }
                }
            });
        } else {
            document.getElementById("kd_brg").focus();
        }
    }

    function get_list() {
        var new_list = '';
        var hpp_tambahan = parseFloat(hapuskoma(document.getElementById("hpp_tambahan").value));
        if (isNaN(hpp_tambahan)) {
            hpp_tambahan = 0;
        }

        var total = 0;
        for (var x = 0; x < array_assembly.length; x++) {
            var jumlah = (array_assembly[x].hrg_beli*array_assembly[x].qty);
            total = total + jumlah;
            new_list += '<tr>' +
                '<td><button type="button" id="delete_list_'+(x)+'" onclick="delete_list('+(x)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                '<td>'+array_assembly[x].kd_brg+'<input type="hidden" id="kd_brg_'+x+'" name="kd_brg_'+x+'" value="'+array_assembly[x].kd_brg+'"></td>' +
                '<td>'+array_assembly[x].barcode+'<input type="hidden" id="barcode_'+x+'" name="barcode_'+x+'" value="'+array_assembly[x].barcode+'"></td>' +
                '<td>'+array_assembly[x].nm_brg+'<input type="hidden" id="nm_brg_'+x+'" name="nm_brg_'+x+'" value="'+array_assembly[x].nm_brg+'"></td>' +
                '<td>'+to_rp(array_assembly[x].hrg_beli)+'<input type="hidden" id="hrg_beli_'+x+'" name="hrg_beli_'+x+'" value="'+array_assembly[x].hrg_beli+'"></td>' +
                '<td><input autocomplete="off" type="text" oncotextmenu="return false" onfocus="$(this).select()" onkeydown="return isNumber(event)" onkeyup="check_list('+x+')" value="' + array_assembly[x].qty + '" class="form-control" id="qty_'+x+'" name="qty_'+x+'"><b class="error" id="ntf_qty_'+x+'"></b></td>' +
                '<td><span id="jumlah_'+x+'" name="jumlah_'+x+'">'+to_rp(jumlah)+'</span></td>' +
                '</tr>';

            max_data = x + 1;
        }

        document.getElementById("max_barang").value = max_data;
        document.getElementById("list_barang").innerHTML = new_list;
        $("#total").text(to_rp(total+hpp_tambahan));
    }

    function check_list(id) {
        var max_data = document.getElementById("max_barang").value;
        var hpp_tambahan = parseFloat(hapuskoma(document.getElementById("hpp_tambahan").value));
        var qty = document.getElementById("qty_"+id).value;
        var hrg_beli = document.getElementById("hrg_beli_"+id).value;

        if (isNaN(hpp_tambahan)) {
            hpp_tambahan = 0;
        }

        if (isNaN(qty) || qty <= 0) {
            document.getElementById("ntf_qty_"+id).innerText = "Qty harus lebih dari 0!";
            for (j=0; j<max_data; j++) {
                if (j != id) {
                    document.getElementById("qty_" + j).disabled = true;
                }
            }
            $("#jumlah_"+id).text(0);
            document.getElementById("save").disabled = true;
        } else {
            hide_notif("ntf_qty_"+id);
            for (j=0; j<max_data; j++) {
                if (j != id) {
                    document.getElementById("qty_" + j).disabled = false;
                }
            }
            document.getElementById("save").disabled = false;
            update_list(id);
            $("#jumlah_"+id).text(to_rp(qty*hrg_beli));
            var total = 0;
            for (var x = 0; x < array_assembly.length; x++) {
                total = total + (array_assembly[x].hrg_beli*array_assembly[x].qty);
            }
            $("#total").text(to_rp(total+hpp_tambahan));
        }
    }

    function get_hrg_jual() {
        $.ajax({
            url: "<?=base_url('api/get_brg')?>",
            type: "POST",
            data: {kd_brg: $("#kd_brg_ass").val()},
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/loading/load.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                var hrg_jual = parseFloat(res.hrg_jual_1);
                $("#hrg_jual_1").val(to_rp(hrg_jual))
            }
        });
    }

    function hitung() {
        var hpp_tambahan = parseFloat(hapuskoma(document.getElementById("hpp_tambahan").value));
        if (isNaN(hpp_tambahan)) {
            hpp_tambahan = 0;
        }
        var total = 0;
        for (var x = 0; x < array_assembly.length; x++) {
            total = total + (array_assembly[x].hrg_beli*array_assembly[x].qty);
        }

        $("#total").text(to_rp(total+hpp_tambahan));
    }

    function update_list(id) {
        array_assembly[id].qty = $("#qty_"+id).val();
    }

    function delete_list(id) {
        array_assembly.splice(id, 1);

        get_list();
    }
</script>

