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
							<form id="form_head">
							<div class="row">
								<div class="col-md-12">
									<div class="panel-body">
										<?php if(isset($_GET['trx'])){ echo'<input type="hidden" name="update" value="'.base64_decode($_GET['trx']).'" />'; } ?>
										<div class="row">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota PO</label>
													<div class="col-sm-6">
														<input type="text" id="nota_po" name="nota_po" class="form-control" readonly value="">
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tgl Awal</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onclick="add_tmp_master()" name="tgl_awal" id="tgl_awal" type="text" value="<?=set_value('tgl_awal')?set_value('tgl_awal'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tgl Order</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onclick="trx_number();add_tmp_master()" name="tgl_order" id="tgl_order" type="text" value="<?=set_value('tgl_order')?set_value('tgl_order'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tgl Expired</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onclick="trx_number();add_tmp_master()" name="tgl_kirim" id="tgl_kirim" type="text" value="<?=set_value('tgl_kirim')?set_value('tgl_kirim'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Lokasi</label>
													<div class="col-sm-6">
														<select name="lokasi" id="lokasi" onclick="trx_number(); add_tmp_master(); hide_notif('alr_lokasi')" class="select2">
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
													<label class="col-sm-4">Jenis Transaksi</label>
													<div class="col-sm-6">
														<select name="jenis_transaksi" id="jenis_transaksi" onclick="add_tmp_master()" class="form-control">
															<!--<option value="">Pilih</option>-->
															<option value="Tunai">Tunai</option>
															<option value="Kredit">Kredit</option>
															<option value="Konsinyasi">Konsinyasi</option>
														</select>
														<b class="error" id="alr_jenis_transaksi"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Supplier</label>
													<div class="col-sm-6">
														<select type="text" name="supplier" id="supplier" onclick="hapus_list(); add_tmp_master(); hide_notif('alr_supplier')" class="select2">
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
													<label class="col-sm-4">Catatan</label>
													<div class="col-sm-6">
														<textarea onblur="add_tmp_master()" class="form-control" id="catatan" name="catatan"></textarea>
														<b class="error" id="alr_catatan"></b>
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
														<input class="form-control" type="radio" onclick="update_tmp_master('m9', $(this).val())" id="<?=$field?>_barcode" name="<?=$field?>" value="1" checked required />
														<label for="<?=$field?>_barcode"> Barcode </label>
													</div>
													<div class="radio radio-primary">
														<input class="form-control" type="radio" onclick="update_tmp_master('m9', $(this).val())" id="<?=$field?>_qty" name="<?=$field?>" value="2" required />
														<label for="<?=$field?>_qty"> Qty </label>
													</div>
													<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-2">
												<select class="form-control" id="cat_cari" name="cat_cari">
													<option value="1">Kode Barang</option>
													<option value="2">Barcode</option>
													<option value="3"><?=$menu_group['as_deskripsi']?></option>
													<option value="4">Kode Packing</option>
												</select>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<input type="text" class="form-control" id="barcode" name="barcode">
													<div class="input-group-btn">
														<button onclick="add_barang_po_mingguan();" type="button" class="btn btn-primary"><i class="md md-search"> All</i></button>
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
												<th>Aksi</th>
												<th>Kode Barang</th>
												<th>Barcode</th>
												<th>Nama Barang</th>
												<th><?=$menu_group['as_deskripsi']?></th>
												<th>Stk Awl</th>
												<th>Stk Msk</th>
												<th>Jual</th>
												<th>Rtr</th>
												<th>Adj</th>
												<th>Mts</th>
												<th>Stk Akr</th>
												<!--<th data-priority="0">Satuan</th>-->
												<th>Harga Beli</th>
												<th>Harga Jual 1</th>
												<!--<th>Harga Jual 2</th>
												<th>Harga Jual 3</th>
												<th>Harga Jual 4</th>-->
												<th>Disc 1</th>
												<!--<th>Disc 2</th>
												<th>Disc 3</th>
												<th>Disc 4</th>-->
												<th>Jumlah</th>
												<th>GST/PPN</th>
												<th>Sub Total</th>
												<!--<th>Netto</th>
												<th data-priority="0">Konversi</th>-->
											</tr>
											</thead>
											<tbody id="list_barang">
											</tbody>
										</table>
									</div>
								</div>
							</div>
							</form>
							<div class="row">
								<div class="col-md-7">
									<button class="btn btn-primary" onclick="if (confirm('Akan menyimpan transaksi?')){simpan_transaksi()}" id="simpan" type="submit">Simpan</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
									<button class="btn btn-primary" onclick="if (confirm('Akan menutup transaksi?')){tutup_transaksi()}" id="keluar" type="submit">Keluar</button>
									<!--<button class="btn btn-primary" id="cek_min_stock" type="submit">Cek Min Stock</button>-->
								</div>
								<div class="col-md-5">
									<div class="row" style="margin-bottom: 3px">
										<label class="col-sm-4">Sub Total</label>
										<div class="col-sm-8">
											<input type="text" id="sub_total" name="sub_total" class="form-control text-right" readonly>
										</div>
									</div>
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
                        <table class="table table-bordered table_check" width="100%">
                            <thead>
                            <tr>
                                <th>Aksi</th>
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

<script>
	$(document).ready(function () {
		$("#container_tgl_jatuh_tempo").hide();

		/*get master*/
		get_tmp_master();
	});

	function get_tmp_master() {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_tr_temp_m_po' ?>",
			type: "GET",
			dataType: "JSON",
			success: function (data) {
				$("#nota_po").val(data['m1']);
				$('#tgl_awal').datepicker("setDate", data['m10']);
				$('#tgl_order').datepicker("setDate", data['m2']);
				$('#tgl_kirim').datepicker("setDate", data['m3']);
				$("#lokasi").val(data['m4']).change();
				$("#supplier").val(data['m5']).change();
				$("#catatan").val(data['m7']);
				if (data['m9'] == 1 || data['m9'] == '' || data['m9'] == null) {
					document.getElementById('set_focus_barcode').checked = true;
				} else if(data['m9'] == 2) {
					document.getElementById('set_focus_qty').checked = true;
				}
				
				/*get detail*/
				get_tmp_detail('load');
			}
		});
	}

    function add_tmp_master() {
        var data = new FormData($('#form_head')[0]);
        var nota_po = $("#nota_po").val();

        if (nota_po!= '') {
            $.ajax({
                url: "<?php echo base_url() . 'pembelian/add_tr_temp_m_po' ?>",
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
            url: "<?php echo base_url() . 'pembelian/update_tr_temp_m_po/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

	function get_tmp_detail(param = 0) {
		var lokasi = $('#lokasi').val();
		var tgl_awal = $('#tgl_awal').val();
		var tgl_akhir = $('#tgl_order').val();
		
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/get_tr_temp_d_po_mingguan' ?>",
			type: "POST",
			data:{lokasi:lokasi, tgl_awal:tgl_awal, tgl_akhir:tgl_akhir},
			dataType: "JSON",
			beforeSend: function () {
				$('#loading').show();
			},
			complete: function () {
				$('#loading').hide();
			},
			success: function (data) {
                $("#list_barang").html(data.list_barang);
                $("#sub_total").val(to_rp(data.sub_total));
				if(document.getElementById("set_focus_barcode").checked == true){
					$("#barcode").focus();
				} else if(document.getElementById("set_focus_qty").checked == true){
					if(param == 'load') {
						$("#barcode").focus();
					} else if(param==0) {
						$("#d9" + $("#col").val()).focus().select();
					} else {
						$("#d9" + param).focus().select();
					}
				}
			}
		});
	}
	
	function hapus_list(){
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/hapus_barang_po_mingguan' ?>",
			type: "POST",
			dataType: "JSON",
			beforeSend: function () {
				$('#loading').show();
			},
			complete: function () {
				$('#loading').hide();
			},
			success: function (data) {
				if(data.status==1){
					get_tmp_detail('load');
				} else {
					alert('Hapus list barang gagal. Mohon pilih ulang supplier');
				}
			}
		});
	}
	
	function add_barang_po_mingguan(){
		var lokasi = $('#lokasi').val();
		var supplier = $('#supplier').val();
		var tgl_awal = $('#tgl_awal').val();
		var tgl_akhir = $('#tgl_order').val();
		
		if(lokasi!='' && supplier!=''){
			$.ajax({
				url: "<?php echo base_url() . 'pembelian/add_barang_po_mingguan' ?>",
				type: "POST",
				data:{lokasi:lokasi, group1:supplier, tgl_awal:tgl_awal, tgl_akhir:tgl_akhir},
				dataType: "JSON",
		   		beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
				success: function (data) {
					if(data.status==1){
						get_tmp_detail('load');
					} else {
						alert('Get barang gagal. Mohon pilih ulang lokasi dan supplier');
					}
				}
			});
		}
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

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'pembelian/update_tr_temp_d_po/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(hapuskoma(value)),
            type: "GET"
        });
    }

	function hapus_barang(barcode) {
		$.ajax({
			url: "<?php echo base_url() . 'pembelian/delete_tr_temp_d_po/' ?>" + btoa(barcode),
			type: "GET",
			success: function (data) {
				if (data) {
					get_tmp_detail();
				}
			}
		});
	}

	function cari_barang() {
        var lokasi = $("#lokasi").val();
        var supplier = $("#supplier").val();
        hide_notif("alr_barang");

        if (lokasi == '') {
            $("#alr_lokasi").text("Lokasi wajib dipilih!");
        }

        if (supplier == '') {
            $("#alr_supplier").text("Supplier wajib dipilih!");
        }

        if (lokasi != '' && supplier != '') {
            $.ajax({
                url: "<?php echo base_url() . 'pembelian/get_list_barang' ?>",
                data: {supplier_: supplier},
                type: "POST",
                dataType: "JSON",
                success: function (data) {
                    $("#modal-container").modal('show');
                    $("#modal-label").text('Daftar Barang dari Supplier ' + data.supplier);
                    $("#list_barang_modal").html(data.list_barang);
                }
            });
        }
    }

    function add_barang() {
        var nota_po = $("#nota_po").val();
        var list = cek_checkbox_checked('barang');

        $.ajax({
            url: "<?php echo base_url() . 'pembelian/add_list_barang_po' ?>",
            data: {nota_po_: nota_po, list_: list},
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
		var nota_po = $("#nota_po").val();
		var barcode = $("#barcode").val();
		var lokasi = $("#lokasi").val();
		var supplier = $("#supplier").val();
		hide_notif("alr_barang");

		if (event.keyCode == 13) {
			if (lokasi == '') {
				$("#alr_lokasi").text("Lokasi wajib dipilih!");
			}

			/*if (supplier == '') {
				$("#alr_supplier").text("Supplier wajib dipilih!");
			}*/

			if (lokasi != '' && supplier != '') {
				$.ajax({
					url: "<?php echo base_url() . 'pembelian/get_barang_po/' ?>" + btoa(nota_po) + "/" + btoa(barcode) + "/" + btoa(lokasi) + "/" + btoa(supplier) + "/" + btoa(cat_cari),
					type: "GET",
					dataType: "JSON",
					success: function (data) {
						if (data.status == 1) {
							$("#barcode").val("").focus();
							get_tmp_detail(data.col_jumlah);
						} else if (data.status == 2) {
							alert(data.notif);
							$("#barcode").val("").focus();
							get_tmp_detail();
						} else {
							$("#barcode").val("").focus();
							$("#alr_barang").text(data.notif)
						}
					}
				});
			}
		}
	});

	function hitung_barang(column, id, value, length) {
	    var d3 = 0; if (column != 'd3'){d3 = parseFloat(hapuskoma($("#d3"+id).val()));}else {d3 = parseFloat(hapuskoma(value));}
	    var d4 = 0; if (column != 'd4'){d4 = $("#d4"+id).val();}else {d4 = value;}
	    var d5 = 0; if (column != 'd5'){d5 = $("#d5"+id).val();}else {d5 = value;}
	    var d6 = 0; if (column != 'd6'){d6 = $("#d6"+id).val();}else {d6 = value;}
	    var d7 = 0; if (column != 'd7'){d7 = $("#d7"+id).val();}else {d7 = value;}
	    var d8 = 0; if (column != 'd8'){d8 = $("#d8"+id).val();}else {d8 = value;}
	    var d9 = 0; if (column != 'd9'){d9 = $("#d9"+id).val();}else {d9 = value;}

	    var netto = d3 * d9;
	    var diskon = double_diskon(netto, [d4, d5, d6, d7]);
        var hitung_sub_total = hitung_ppn(diskon, 0, d8);

        $("#sub_total"+id).val(to_rp(hitung_sub_total.toFixed(2)));

        var sub_total = 0;
        for (var i=1; i<=length; i++) {
            sub_total = sub_total + parseFloat(hapuskoma(document.getElementById("sub_total"+i).value));
        }

        $("#sub_total").val(to_rp(sub_total.toFixed(2)));
	}

	function trx_number() {
		var tgl_order = $("#tgl_order").val();
		var get_lokasi = $("#lokasi").val();
		var lokasi = get_lokasi.split("|");

		if (tgl_order != '' && get_lokasi != ''){
			$.ajax({
				url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("PO") + "/" + btoa(tgl_order) + "/" + btoa(lokasi[1]),
				type: "GET",
				success: function (data) {
					$("#nota_po").val(data);
				}
			});
		}else {
			$("#nota_po").val("");
		}
	}

	function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'pembelian/delete_trans_po' ?>",
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
        if( document.getElementById('nota_po').value == "" )
        {
            document.getElementById('nota_po').focus() ;
            return false;
        }

        if( document.getElementById('tgl_order').value == "" )
        {
            document.getElementById('tgl_order').focus() ;
            return false;
        }

        if( document.getElementById('tgl_kirim').value == "" )
        {
            document.getElementById('tgl_kirim').focus() ;
            return false;
        }

        if( document.getElementById('lokasi').value == "" )
        {
            document.getElementById('lokasi').focus() ;
            return false;
        }

        if( document.getElementById('catatan').value == "" )
        {
            document.getElementById('catatan').focus() ;
            return false;
        }

        if( document.getElementById('supplier').value == "" )
        {
            document.getElementById('supplier').focus() ;
            return false;
        }

        if( document.getElementById('col').value == 0 )
        {
            document.getElementById('barcode').focus() ;
            return false;
        }
		
		
        return true;
    }

	function simpan_transaksi() {
	    /*var nota_po = $("#nota_po").val();
	    var col = $("#col").val();
		var supplier = $("#supplier").val();

        if (col == 0) {
            $("#alr_barang").text("Barang belum dimasukan!");
			$("#barcode").focus();
        }

		if (supplier == '') {
			$("#alr_supplier").text("Supplier wajib dipilih!");
			$("#supplier").focus();
		}*/

        if (validate()) {
            $.ajax({
                url: "<?php echo base_url().'pembelian/trans_po_mingguan' ?>",
                type: "POST",
                data: $("#form_head").serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
				success: function (data) {
                    if (data != 0) {
						if('<?=(isset($_GET['trx'])?$_GET['trx']:'')?>'==''){
							if (confirm('Transaksi Berhasil! Akan mencetak transaksi?')){ 
								//cetak_transaksi('cetak/nota_pembelian', 'pembelian_barang', data.substring(1));
								cetak_transaksi('purchase_order_report', 'none', data);
								//window.open("<?php //echo base_url() . 'pembelian/po_by_cabang_report/print/' ?>"+data,'_blank');
							} else {
								location.reload();
							}
						} else {
							alert("Transaksi Berhasil!");
							window.location = "<?=base_url().'pembelian/purchase_order_report'?>";
						}		
                    } else {
						alert('Gagal menyimpan. Periksa list barang anda!');
					}
                },
				//error: alert('Transaksi Gagal!')
            });
        }
    }
</script>