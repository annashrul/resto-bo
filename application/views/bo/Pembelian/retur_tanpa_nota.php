<style>
	th, td {
		font-size: 9pt;
	}

	.form-control {
		font-size: 9pt;
	}
</style>
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
								<div class="col-md-12">
									<div class="panel-body">
										<?=form_open(null,array('id'=>"form_head"))?>
										<div class="row">
											<input type="hidden" name="param" value="add_retur">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota Retur</label>
													<div class="col-sm-6">
														<input type="text" id="nota_sistem" name="nota_sistem" class="form-control" readonly value="">
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tgl Retur</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number();add_tmp_master()" name="tgl_retur" id="tgl_retur" type="text" value="<?=set_value('tgl_retur')?set_value('tgl_retur'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Lokasi</label>
                                                    <div class="col-sm-6">
                                                        <select name="lokasi" id="lokasi" onchange="trx_number(); hide_notif('alr_lokasi')" class="select2">
                                                            <option value="">Pilih</option>
                                                            <?php
                                                            foreach ($data_lokasi as $row) {
                                                                echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <b class="error" id="alr_lokasi"></b>
                                                    </div>
                                                </div>
											</div>
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Supplier</label>
													<div class="col-sm-6">
														<select type="text" name="supplier" id="supplier" onchange="add_tmp_master(); hide_notif('alr_supplier');" class="select2">
															<option value="">Pilih</option>
															<?php
															foreach ($data_supplier as $row) {
																echo "<option value=\"".$row['Kode']."\">".$row['Kode']." | ".$row['Nama']."</option>";
															}
															?>
														</select>
														<b class="error" id="alr_supplier"></b>
													</div>
												</div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Lokasi Cabang</label>
                                                    <div class="col-sm-6">
                                                        <select name="lokasi_cabang" id="lokasi_cabang" onchange="add_tmp_master(); hide_notif('alr_lokasi_cabang')" class="select2">
                                                            <option value="">Pilih</option>
                                                            <?php
                                                            foreach ($data_lokasi as $row) {
                                                                echo "<option value=\"".$row['Kode']."|".$row['serial']."\">".$row['Nama']."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <b class="error" id="alr_lokasi_cabang"></b>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 3px">
                                                    <label class="col-sm-4">Keterangan</label>
                                                    <div class="col-sm-6">
                                                        <textarea onblur="add_tmp_master()" class="form-control" id="keterangan" name="keterangan"></textarea>
                                                        <b class="error" id="alr_keterangan"></b>
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
												<select class="form-control input-sm" id="cat_cari" name="cat_cari">
													<option value="1">Kode Barang</option>
													<option value="2">Barcode</option>
													<option value="3"><?=$menu_group['as_deskripsi']?></option>
													<option value="4">Kode Packing</option>
												</select>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<input type="text" class="form-control input-sm" id="barcode" name="barcode">
													<!--<div class="input-group-btn">
														<button onclick="cari_barang()" type="button" id="cari_barcode" name="cari_barcode" class="btn btn-primary btn-sm"><i class="md md-search"></i></button>
													</div>-->
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
                                                <th>Barcode</th>
                                                <th><?=$menu_group['as_deskripsi']?></th>
                                                <th>Nama Barang</th>
                                                <th>Satuan</th>
                                                <th>Kondisi</th>
                                                <th>Ket</th>
                                                <th class="width-uang">Harga Beli</th>
                                                <th class="width-diskon">Stock</th>
                                                <th class="width-diskon">Qty Retur</th>
                                                <th class="width-uang">Nilai Retur</th>
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
									<button class="btn btn-primary" onclick="if (confirm('Akan menyimpan transaksi?')){simpan_transaksi()}" id="simpan" type="submit">Simpan</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="submit">Keluar</button>
									<!--<button class="btn btn-primary" id="tambah_return" type="submit">Tambah Return</button>
									<button class="btn btn-primary" id="rincian_return" type="submit">Rincian Return</button>-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="print_retur_pembelian<?=$i?>" class="hidden">
    <img style="height: 1cm; position: absolute" src="<?=base_url().'assets/images/site/'.$this->m_website->site_data()->logo?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td style="height: 1.5cm;" colspan="8" class="text-center">Nota Retur Pembelian</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="2%"></td>
            <td width="23%"></td>
            <td width="2%"></td>
            <td width="25%"></td>

            <td width="3%"></td>
            <td width="19%"></td>
            <td width="2%"></td>
            <td width="25%"></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 8pt !important">Tanggal</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 8pt !important"><?=substr($row['Tgl'],0,10)?></td>

            <td></td>
            <td style="font-size: 8pt !important">Retur Ke</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 8pt !important"><?=$row['Kode']." - ".$row['Nama']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 8pt !important">No. Transaksi</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 8pt !important"><?=$row['No_Retur']?></td>

            <td></td>
            <td style="font-size: 8pt !important">Nota Supplier</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 8pt !important"><?=$row['noNota']?></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">No</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kode Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Barcode</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Nama Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kelompok Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Keterangan</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Satuan</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Beli</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Sub Total</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;
        foreach($detail as $rows){
            $no++;
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=$rows['barcode']?></td>
                <td><?=$rows['nm_brg']?></td>
                <td><?=$rows['nm_kel_brg']?></td>
                <td><?=$rows['keterangan']?></td>
                <td><?=($rows['jml']+0)?></td>
                <td><?=$rows['satuan']?></td>
                <td class="text-right"><?=number_format($rows['hrg_beli'],2)?></td>
                <td class="text-right"><?=number_format($sub_total,2)?></td>
            </tr>
            <?php
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="border-top: solid; border-width: thin" colspan="6">TOTAL</td>
            <td style="border-top: solid; border-width: thin"><?=$tqty?></td>
            <td style="border-top: solid; border-width: thin" colspan="2"></td>
            <td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($total,2)?></td>
        </tr>
        </tfoot>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:left;" colspan="2">
                <u><?=number_to_words($total)?></u>
            </td>
        </tr>
        <tr>
            <td style="text-align:center;">
                Penerima
            </td>
            <td style="text-align:center;">
                Pengirim
            </td>
            <td style="text-align:center;">
                Admin
            </td>
        </tr>
        <tr>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script>
	$(document).ready(function () {
		$("#container_tgl_jatuh_tempo").hide();

		/*get master*/
		get_tmp_master();

		/*get detail*/
		get_tmp_detail(1);
	});

	function get_tmp_master() {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_tr_temp_m_retur' ?>",
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$("#nota_sistem").val(data['m1']);
				$('#tgl_retur').datepicker("setDate", data['m2']);
                $("#lokasi").val(data['m3']).change();
                $("#lokasi_cabang").val(data['m10']).change();
                $("#supplier").val(data['m4']).change();
                $("#keterangan").val(data['m8']);
				if (data['m9'] == 1 || data['m9'] == '' || data['m9'] == null) {
					document.getElementById('set_focus_barcode').checked = true;
				} else if(data['m9'] == 2) {
					document.getElementById('set_focus_qty').checked = true;
				}
            }
		});
	}

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var nota_sistem = $("#nota_sistem").val();

        if (nota_sistem != '') {
            $.ajax({
                url: "<?php echo base_url() . 'pembelian/add_tr_temp_m_retur' ?>",
                data: data,
                type: "POST",
                mimeType: "multipart/form-data",
                contentType: false,
                cache: false,
                processData: false,
                dataType: "JSON"
            });
        }
    }

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/update_tr_temp_m/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

    function to_barcode(event) {
		if (event.keyCode == 13) {
			$("#barcode").focus();
            $('html, body').animate({ scrollTop: 0 }, 'fast');
		}
	}

	function to_qty(event, id) {
		if (event.keyCode == 13) {
			$("#d9"+id).focus().select();
		}
	}

	function get_tmp_detail(param = 0) {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_tr_temp_d_retur/' ?>" + btoa('add'),
			type: "GET",
			dataType: "JSON",
			success: function (data) {
                $("#list_barang").html(data.list_barang);
				
				if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param != 1) {
						$("#d8" + $("#col").val()).focus().select();
					} else {
						$("#barcode").focus();
					}
				}
			}
		});
	}

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/update_tr_temp_d_retur/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(hapuskoma(value)),
            type: "GET"
        });
    }

	function hapus_barang(barcode) {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/delete_tr_temp_d_retur/' ?>" + btoa(barcode),
			type: "GET",
			success: function (data) {
				if (data) {
					get_tmp_detail();
				}
			}
		});
	}

	$("#barcode").keyup(function (event) {
		var cat_cari = $("#cat_cari").val();
		var nota_sistem = $("#nota_sistem").val();
		var barcode = $("#barcode").val();
		var lokasi = $("#lokasi").val();
		var supplier = $("#supplier").val();
		hide_notif("alr_barang");

		if (event.keyCode == 13) {
			if (lokasi == '') {
				$("#alr_lokasi").text("Lokasi wajib dipilih!");
			}

			if (supplier == '') {
				$("#alr_supplier").text("Supplier wajib dipilih!");
			}

			if (lokasi != '' && supplier != '' && barcode != '') {
				$.ajax({
					url: "<?php echo base_url() . 'pembelian/get_barang_retur/' ?>" + btoa(nota_sistem) + "/" + btoa(barcode) + "/" + btoa(lokasi) + "/" + btoa(supplier) + "/" + btoa(cat_cari),
					type: "GET",
					dataType: "JSON",
					success: function (data) {
						if (data.status == 1) {
							$("#barcode").val("");
							get_tmp_detail();
						} else if (data.status == 2) {
                            $("#alr_barang").text(data.notif);
							$("#barcode").val("");
						} else {
							$("#barcode").val("").focus();
							$("#alr_barang").text(data.notif);
						}
					}
				});
			}
		}
	});

	function hitung_barang(column, id, value, length) {
	    var d5 = 0; if (column != 'd5'){d5 = parseFloat(hapuskoma($("#d5"+id).val()));}else {d5 = parseFloat(hapuskoma(value));}
	    var d8 = 0; if (column != 'd8'){d8 = $("#d8"+id).val();}else {d8 = value;}

	    var nilai_retur = d5 * d8;

        $("#nilai_retur"+id).val(to_rp(nilai_retur.toFixed(2)));

        var total_qty_retur = 0;
        var total_nilai_retur = 0;

        for (var i = 1; i <= length; i++) {
            if (isNaN(parseInt(document.getElementById("d8" + i).value))) {
                var dx = 0;
            } else {
                dx = document.getElementById("d8" + i).value;
            }
            total_nilai_retur = total_nilai_retur + parseFloat(hapuskoma(document.getElementById("nilai_retur" + i).value));
            total_qty_retur = total_qty_retur + parseInt(dx);
        }

        $("#total_nilai_retur").text(to_rp(total_nilai_retur.toFixed(2)));
        $("#total_qty_retur").text(total_qty_retur);
	}

	function trx_number() {
		var tgl_retur = $("#tgl_retur").val();
		var get_lokasi = $("#lokasi").val();
		var lokasi = get_lokasi.split("|");

		if (tgl_retur != '' && get_lokasi != ''){
			$.ajax({
				url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("NB") + "/" + btoa(tgl_retur) + "/" + btoa(lokasi[1]),
				type: "GET",
				success: function (data) {
					$("#nota_sistem").val(data);
					add_tmp_master();
				}
			});
		}else {
			$("#nota_sistem").val("");
		}
	}

	function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'pembelian/delete_trans_retur' ?>",
            type: "GET",
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    }

    function validate()
    {
        if( document.getElementById('nota_sistem').value == "" )
        {
            document.getElementById('nota_sistem').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('tgl_retur').value == "" )
        {
            document.getElementById('tgl_retur').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi').value == "" )
        {
            document.getElementById('lokasi').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('lokasi_cabang').value == "" )
        {
            document.getElementById('lokasi_cabang').focus() ;
            $("#alr_lokasi_cabang").text("Lokasi cabang wajib dipilih");
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('supplier').value == "" )
        {
            document.getElementById('supplier').focus() ;
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            return false;
        }

        if( document.getElementById('keterangan').value == "" )
        {
            document.getElementById('keterangan').focus() ;
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
	    /*var nota_sistem = $("#nota_sistem").val();
	    var nilai_retur = $("#total_nilai_retur").text();

        if (nilai_retur == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
        }*/

        if (validate()) {
            $.ajax({
                url: "<?php echo base_url().'pembelian/trans_retur_tanpa_nota_x' ?>",
                type: "POST",
				data: $("#form_head").serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (data) {
					if (data != '10') {
						if (confirm("Transaksi Berhasil! Akan Mencetak Nota?")) {
                            cetak_transaksi('cetak/nota_pembelian', 'nota_retur_3ply', data.substring(1));
							//window.open('<?=base_url().'cetak/nota_retur_3ply/'?>' + btoa(data.substring(1)));
						} else {
                            location.reload();
                        }
					} else {
						alert("Transaksi Gagal!");
						//location.reload();
					}
                }
            });
        }
    }
</script>