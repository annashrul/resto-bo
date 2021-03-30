<style>
    th, td {
        font-size: 9pt;
    }

    .form-control {
        font-size: 9pt;
    }
	
	body {
		font-family: Arial;
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
                                <div class="col-md-2">
									<label>Kode Barang</label>
									<!--<div class="input-group">
										<select class="form-control" id="cat_cari" name="cat_cari">
											<option value="1">Kode Barang</option>
											<option value="2">Barcode</option>
											<option value="3"><?=$menu_group['as_deskripsi']?></option>
										</select>
									</div>-->
									<input type="hidden" id="cat_cari" name="cat_cari" value="1">

									<input type="text" class="form-control" id="barcode" name="barcode" onkeyup="barcode_custom(event); return to_col(event, 'nama');" />
									<b class="error" id="alr_barang"></b>
                                </div>
								<div class="col-md-3">
									<label>Nama Barang</label>
									<input type="text" class="form-control" id="nama" name="nama" onkeyup="barcode_custom(event); return to_col(event, 'harga');" />
									<b class="error" id="alr_nama"></b>
                                </div>
								<div class="col-md-2">
									<label>Harga Jual</label>
									<input type="text" class="form-control" id="harga" name="harga" onkeydown="return isNumber(event);" onkeyup="isMoney('harga'); barcode_custom(event); return to_col(event, 'barcode');" /> 
									<b class="error" id="alr_nama"></b>
                                </div>
                            </div>
							<br />
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th style="width: 10px">No</th>
                                                <th>Aksi</th>
                                                <th>Barcode</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Harga Jual</th>
                                                <th>Qty</th>
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
                                    <button class="btn btn-primary" onclick="if (confirm('Akan mencetak barcode?')){cetak_barcode()}" id="simpan" type="submit">Cetak Barcode</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="submit">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?=base_url().'assets/'?>js/html2canvas.js"></script>
<script>
    $(document).ready(function () {
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail(1);
    });

    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_m' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#lokasi_barang").select2('val', data['m2']);
            }
        });
    }

    function add_tmp_master() {
        var lokasi = $("#lokasi_barang").val();

        $.ajax({
            url: "<?php echo base_url() . 'utility/add_tr_temp_m' ?>",
            data: {lokasi_:lokasi},
            type: "POST",
            dataType: "JSON"
        });
    }

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_m/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

    function get_tmp_detail(param = 0) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_d_custom' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                if(param != 1) {
                    $("#d7" + $("#col").val()).focus().select();
                } else {
                    $("#barcode").focus();
                }
				generate_barcode(1,$('#col').val());
            }
        });
    }
	
	function generate_barcode(i, jml){
		if(i<=jml){
			JsBarcode("#brcd"+i, $("#barcode"+i).text(), {
				width: 3,
				margin: 0,
				background: "transparent",
				textAlign: "left",
				textMargin: 0,
				height: 50,
				fontSize: 20,
				fontOptions: "bold",
				font: "arial"
			});

			html2canvas($("#generate_barcode"+i) , {
				onrendered: function (canvas) {
					var ctx = canvas.getContext('2d');

					ctx.webkitImageSmoothingEnabled = false;
					ctx.mozImageSmoothingEnabled = false;
					ctx.imageSmoothingEnabled = false;

					var dataURL = canvas.toDataURL("image/png");
					//dataURL = canvas.toDataURL("image");

					$.ajax({
						type: "POST",
						dataType: "json",
						url: "<?=base_url()?>utility/generate_barcode", //second file
						data: {data: dataURL, name: $("#nama"+i).text()/*, kode: 'Kode', article: 'Artikel'*/},
						success: function () {
							//window.close();
							$(".change_size"+i).css("font-size", "8pt");
							$(".change_size"+i).css("max-width", "120px");
							$(".change_size"+i).css("max-height", "50px");
							$("#generate_barcode"+i).css("max-width", "120px");
							$("#generate_barcode"+i).css("max-height", "50px");
							$("#generate_barcode"+i).css("padding", "0px");
							//$("#tdgb"+i).css("min-width", "150px");
							$("#tdgb"+i).css("height", "75px");
							$("#tdgb"+i).css("padding", "0px");
							
							generate_barcode((i+1),jml); 
							//$('#generate_barcode'+i).hide();
						}
					});
				}
			});
		}
	}
	
    function to_barcode(event) {
        if (event.keyCode == 13) {
            $("#barcode").focus();
        }
    }

    function to_qty(event, id) {
        if (event.keyCode == 13) {
            $("#d7"+id).focus().select();
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_d/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/delete_tr_temp_d_custom/' ?>" + btoa(barcode),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }

	function to_col(event, col){
		if(event.ctrlKey && event.keyCode==13) { $("#"+col).focus().select(); }
	}
	
    function barcode_custom(event){
        var cat_cari = $("#cat_cari").val();
        var barcode = $("#barcode").val();
        var nama = $("#nama").val();
        var harga = $("#harga").val();
		
		if((!event.ctrlKey) && event.keyCode==13 && barcode==''){ $('#alr_barang').text('Kode Barang Harus Diisi!'); $("#barcode").focus(); } else { $('#alr_barang').text(''); }
		
        if(event.keyCode==13 && barcode!='') {
			$.ajax({
				url: "<?php echo base_url() . 'utility/insert_tr_temp_d_custom/' ?>",
				type: "POST",
				data: {barcode:barcode, nama:nama, harga:harga},
				dataType: "JSON",
				success: function (data) {
					if (data.status == 1) {
						$("#barcode").val("");
						$("#nama").val("");
						$("#harga").val("");
						get_tmp_detail();
					} else if (data.status == 2) {
						alert(data.notif);
						$("#barcode").val("");
						$("#nama").val("");
						$("#harga").val("");
						get_tmp_detail();
					} else {
						$("#barcode").val("").focus();
						$("#alr_barang").text(data.notif)
					}
				}
			});
        }
    };

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'utility/delete_trans_custom' ?>",
            type: "GET",
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    }

    function cetak_barcode() {
        window.open('<?=base_url().'cetak/cetak_barcode_custom'?>');
    }
</script>
