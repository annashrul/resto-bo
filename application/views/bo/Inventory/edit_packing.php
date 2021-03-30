<!-- Page-Title -->
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

            <!-- Main Content -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <input type="hidden" id="banyak_data">
                                <div class="col-md-12">
                                    <div class="panel-body">
										<?=form_open(null, array('id'=>'form_head'))?>
                                            <input type="hidden" name="param" id="param" value="edit">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row hidden" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Kode Packing</label>
                                                        <div class="col-sm-6">
                                                            <?php $field = 'kd_packing'; ?>
                                                            <input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control" readonly value="">
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Tgl Packing</label>
                                                        <div class="col-sm-6">
                                                            <?php $field = 'tgl_packing'; ?>
                                                            <div class="input-group date">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input class="form-control pull-right" readonly name="<?=$field?>" id="<?=$field?>" type="text" value="<?=set_value($field)?set_value($field):date("Y-m-d")?>">
                                                                <!--custom_front_date('pembelian', $(this).val())-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Faktur Alokasi</label>
                                                        <div class="col-sm-6">
                                                            <?php $field = 'no_faktur_mutasi'; ?>
                                                            <select id="<?=$field?>" name="<?=$field?>" onchange="trx_number();" class="select2" style="width: 100%">
                                                                <option value="-">Pilih</option>
                                                                <?php
                                                                foreach ($data_mutasi as $row) {
                                                                    echo "<option value=\"".$row['no_faktur_mutasi']."\">".$row['no_faktur_mutasi']."</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <b class="error" id="alr_alokasi"></b>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 3px">
                                                        <label class="col-sm-4">Pengirim</label>
                                                        <div class="col-sm-6">
                                                            <?php $field = 'pengirim'; ?>
                                                            <input type="text" id="<?=$field?>" name="<?=$field?>" onblur="add_tmp_master()" onkeyup="hide_notif('alr_pengirim')" class="form-control" value="">
                                                            <b class="error" id="alr_pengirim"></b>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
							<div class="row">
								<div class="col-md-12">
									<div class="panel-heading">
										<div class="row">
											<div class="col-md-1">
												<label>Set Focus</label>
											</div>
											<div class="col-md-3">
												<div class="form-inline">
													<?php $field = 'set_focus'; ?>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="add_tmp_master()" id="<?=$field?>_barcode" name="<?=$field?>" value="1" checked required />
														<label for="<?=$field?>_barcode"> Barcode </label>
													</div>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="add_tmp_master()" id="<?=$field?>_qty" name="<?=$field?>" value="2" required />
														<label for="<?=$field?>_qty"> Qty </label>
													</div>
													<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
												</div>
											</div>
										</div>
										<?=form_close()?>
										<div class="row">
											<div class="col-md-2">
												<select class="form-control" id="cat_cari" name="cat_cari">
													<option value="1">Kode Barang</option>
													<option value="2">Barcode</option>
													<option value="3"><?=$menu_group['as_deskripsi']?></option>
													<!--<option value="4">Nama Barang</option>-->
												</select>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<input type="text" class="form-control" id="barcode" name="barcode">
													<div class="input-group-btn">
														<button onclick="cari_barang()" id="cari" name="cari" type="button" class="btn btn-primary"><i class="md md-search"></i></button>
													</div>
												</div>
												<b class="error" id="alr_barang"></b>
											</div>
										</div>
									</div>
								</div>
							</div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th style="width: 10px">No</th>
                                                <th width="60px">Aksi</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Satuan</th>
                                                <th class="width-uang" style="text-align: left">Harga Beli</th>
                                                <th class="width-uang" style="text-align: left">Harga Jual</th>
                                                <th class="width-diskon" style="text-align: left">Qty Alokasi</th>
                                                <th class="width-diskon" style="text-align: left">Qty Packing</th>
                                            </tr>
                                            </thead>
                                            <tbody id="list_barang">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <button class="btn btn-primary" onclick="simpan_transaksi()" id="simpan" type="submit">Simpan</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="submit">Keluar</button>
                                    <!--<button class="btn btn-primary" id="arsip_pembelian" type="submit">Arsip Pembelian</button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-container" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label"></h4>
            </div>
            <div class="modal-footer">
                <input onclick="add_barang()" type="button" class="btn btn-primary pull-right" value="Pilih">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table_check">
                            <thead>
                            <tr>
                                <th>
                                    <?php $field = 'cek_lokasi'; ?>
                                    <div class="checkbox checkbox-primary">
                                        <input class="form-control" type="checkbox" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>">
                                        <label for="<?=$field?>">All</label>
                                    </div>
                                </th>
                                <th>Kode Barang</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th><?=$menu_group['as_deskripsi']?></th>
                            </tr>
                            </thead>
                            <tbody id="list_barang_modal"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php $div='printpacking'; $tqty = 0; ?>
<div id="<?=$div?>" class="hidden">
    <div style="margin:0mm 0mm 0mm 0mm; width:104mm; border: 0px solid black;">
        <?php $packing = array(
            0 => array('barcode'=>$row['kd_packing'], 'ket'=>$row['operator'].' / '.$tqty.' / '.$row['pengirim'], 'date'=>substr($row['tgl_packing'],0,19))
        ); ?>
        <?php foreach($packing as $row => $value){ ?>
            <div style="position:relative; float:left; margin:0mm 0mm 0mm 0mm; border:0px solid black; width:102mm; height:50mm;">
                <center>
                    &nbsp;
                    <div class="label_colom" draggable="true" id="dragme" style="margin-left:0mm; margin-top:5mm; width:100%; font-size:7px; color:black;">
                        <?php $divbarcode=$div.'barcode'.$row; $canvasbarcode=$div.'1canvas'.$row; ?>
                        <script>
                            $(function(){
                                generateBarcode({'divbarcode':'<?=$divbarcode?>', 'canvasbarcode':'<?=$canvasbarcode?>', 'value':'<?=$value['barcode']?>', 'width':1, 'height':80, 'fontSize':14, 'addQuietZone':0});
                            });
                        </script>
                        <div id="<?=$divbarcode?>" class="<?=$divbarcode?>"></div><canvas id="<?=$canvasbarcode?>" width="170" height="100"></canvas>
                    </div>
                    <div class="label_colom" draggable="true" id="dragme" style="margin-left:0mm; margin-top:4mm; width:100%; font-size:12px; color:black;">
                        <?=$value['date']?>
                    </div>
                    <div class="label_colom" draggable="true" id="dragme" style="margin-left:0mm; margin-top:1mm; width:100%; font-size:18px; color:black;">
                        <?=$value['ket']?>
                    </div>
                </center>
            </div>
            <?php if((($row+1) % 1) == 0){ ?><div style="clear:both;"></div><?php } ?>
        <?php } ?>
    </div>
</div>

<script>
    var socket;
    $(document).ready(function () {
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail(1);
    });

    function to_barcode(event) {
        if (event.keyCode == 13) {
            $("#barcode").focus();
        }
    }

    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/get_tr_temp_m_packing/' ?>" + btoa('edit'),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $("#kd_packing").val(data.temp['m1']);
                    $('#tgl_packing').val(data.temp['m2']);
                    $("#no_faktur_mutasi").select2("val",data.temp['m3']);
                    $("#pengirim").val(data.temp['m4']);
					if (data.temp['m9'] == 1 || data.temp['m9'] == '' || data.temp['m9'] == null) {
                        document.getElementById('set_focus_barcode').checked = true;
                    } else if(data.temp['m9'] == 2) {
                        document.getElementById('set_focus_qty').checked = true;
                    }
                }
            }
        });
    }

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var kd_packing = $("#kd_packing").val();

        if (kd_packing != '') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/add_tr_temp_m_packing' ?>",
                data: data,
                type: "POST",
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: "JSON",
                success: function (res) {
                    get_tmp_detail();
                }
            });
        }
    }

    function get_tmp_detail(param=0, col=0) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/get_tr_temp_d_packing/' ?>" + btoa('edit'),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#banyak_data").val(data.length);
                if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param != 1) {
						if (param == 2) {
							$("#d10" + col).focus().select();
						} else {
							$("#d10" + $("#col").val()).focus().select();
						}
					} else {
						$("#barcode").focus();
					}
				}	
            }
        });
    }

    function hitung_barang(column, id, value, length) {
        var d9 = 0; if (column != 'd9'){d9 = $("#d9"+id).val();}else {d9 = value;}
        var d10 = 0; if (column != 'd10'){d10 = $("#d10"+id).val();}else {d10 = value;}

        if (parseInt(d10) <= 0 || isNaN(parseInt(d10))) {
            $("#d10" + id).val(1);
        } else if (parseInt(d10) > parseInt(d9)) {
            $("#d10" + id).val(d9);
        } else {
            $("#d10" + id).val(d10);
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/update_tr_temp_d_packing/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value) + "/" + btoa('edit'),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'inventory/delete_tr_temp_d_packing/' ?>" + btoa(barcode) + "/" + btoa('edit'),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }

    function cari_barang() {
        var no_faktur_mutasi = $("#no_faktur_mutasi").val();

        hide_notif("alr_barang");

        if (no_faktur_mutasi == '-') {
            $("#alr_alokasi").text("Kode alokasi wajib dipilih!");
        }

        if (no_faktur_mutasi != '-') {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/get_list_barang_packing' ?>",
                data: {no_faktur_mutasi_: no_faktur_mutasi, param_: 'edit'},
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    $("#cek_lokasi").prop('checked', false);
                    $("#modal-container").modal('show');
                    $("#modal-label").text('List Barang Belum di Packing');
                    $("#list_barang_modal").html(data.list_barang);
                }
            });
        }
    }

    function add_barang() {
        var kd_packing = $("#kd_packing").val();
        var no_faktur_mutasi = $("#no_faktur_mutasi").val();
        var list = cek_checkbox_checked('barang');

        $.ajax({
            url: "<?php echo base_url() . 'inventory/add_list_barang_packing' ?>",
            data: {kd_packing_: kd_packing, no_faktur_mutasi_:no_faktur_mutasi, list_: list, param_: 'edit'},
            type: "POST",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                    $("#modal-container").modal('hide');
                }
            }
        });
    }

    $("#barcode").keyup(function (event) {
        var cat_cari = $("#cat_cari").val();
        var kd_packing = $("#kd_packing").val();
        var no_faktur_mutasi = $("#no_faktur_mutasi").val();
        var barcode = $("#barcode").val();
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (no_faktur_mutasi == '') {
                $("#alr_alokasi").text("Faktur alokasi wajib dipilih!");
            }

            if (no_faktur_mutasi != '') {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/get_barang_packing/' ?>" + btoa(kd_packing) + "/" + btoa(no_faktur_mutasi) + "/" + btoa(barcode) + "/" + btoa(cat_cari) + "/" + btoa('edit'),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            if (data.barang == 'tersedia') {
                                $("#barcode").val("");
                                get_tmp_detail(2, data.col); //focus ke qty barang yg diinput
                                //get_tmp_detail(1); //focus ke barcode
                            } else { 
                                $("#barcode").val("");
                                //get_tmp_detail(1); //focus ke barcode
                                get_tmp_detail(); //focus ke qty barang yg diinput
                            }
                        } else {
                            alert(data.notif);
                            $("#barcode").val("").focus();
                        }
                    }
                });
            }
        }
    });

    function trx_number() {
        var no_faktur_mutasi = $("#no_faktur_mutasi").val();
        hide_notif("alr_alokasi");

        if (tgl_packing != '' && no_faktur_mutasi != '-'){
            $.ajax({
                url: "<?php echo base_url().'inventory/max_kode_packing/' ?>" + btoa(no_faktur_mutasi),
                type: "GET",
                success: function (data) {
                    $("#kd_packing").val(data);
                    add_tmp_master();
                }
            });
        }else {
            $("#kd_packing").val("");
        }
    }

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'inventory/delete_trans_packing/' ?>" + btoa('edit'),
            type: "GET",
            success: function (data) {
                window.location = "<?php echo base_url().'inventory/packing_report' ?>";
            }
        });
    }

    function validate()
    {
        if( document.getElementById('kd_packing').value == "" )
        {
            document.getElementById('kd_packing').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('tgl_packing').value == "" )
        {
            document.getElementById('tgl_packing').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('no_faktur_mutasi').value == "" )
        {
            document.getElementById('no_faktur_mutasi').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('pengirim').value == "" )
        {
            document.getElementById('pengirim').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('col').value == 0 )
        {
            document.getElementById('barcode').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        return true;
    }

    function simpan_transaksi() {
        /*var banyak_data = $("#banyak_data").val();
        var pengirim = $("#pengirim").val();

        if (banyak_data == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
        }

        if (pengirim == '') {
            $("#alr_pengirim").text("Pengirim Wajib diisi!");
        }*/

        /*if (sub_total == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
        }*/

        if (validate()) {
            if (confirm('Akan menyimpan transaksi?')) {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/trans_packing_x' ?>",
                    type: "POST",
                    data: $("#form_head").serialize(),
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            alert("Transaksi Berhasil!");
                            if (confirm("Akan mencetak barcode?")) {
                                window.open('<?=base_url().'cetak/barcode_packing/'?>' + btoa(data.kd_packing));
                            }
                            send_notif(data.kd_packing, data.lokasi_asal, data.lokasi_tujuan, data.item);
                        } else {
                            alert("Transaksi Gagal!");
                        }
                    }
                });
            }
        }
    }

    function send_notif(kd_packing, lokasi_asal, lokasi_tujuan, item) {
        socket = io.connect("http://<?=$_SERVER['HTTP_HOST']?>:3000");
        socket.emit('sendNotification', {message : '-', kode_packing : kd_packing, lokasi : lokasi_tujuan, total_items : item, From : lokasi_asal}, function (err, responseData) {
            if (!err) {
                window.location = "<?php echo base_url().'inventory/packing_report' ?>";
            }
        });
    }

    $("#cek_lokasi").click(function () {
        if ($("#cek_lokasi").is(":checked")) {
            $(".cek_lokasi").prop('checked', true);
        } else {
            $(".cek_lokasi").prop('checked', false);
        }
    });
</script>