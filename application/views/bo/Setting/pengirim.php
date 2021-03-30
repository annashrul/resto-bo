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
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Action</th><th>Provinsi</th><th>Kabupatan/Kota</th><th>Kecamatan</th>
											</tr>
										</thead>
										<tbody>
                                        <?php
                                        $kode = $preference['Kode'];
                                        $get_alamat = $this->m_crud->get_join_data("kecamatan_rajaongkir kr", "kr.kecamatan, kto.tipe, kto.kota, po.provinsi", array("kota_rajaongkir kto", "provinsi_rajaongkir po"), array("kto.kota_id=kr.kota", "po.provinsi_id=kto.provinsi"), "kr.kecamatan_id='".$preference['kecamatan_pengirim']."'");

                                        echo '
                                            <tr>
                                                <td style="width: 1%">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><div class="col-sm-12"><a href="javascript:" onclick="edit_kecamatan(\''.$kode.'\')" class="btn btn-default col-sm-12"><i class="fa fa-edit"></i> Edit</a></div></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>'.$get_alamat['provinsi'].'</td>
                                                <td>'.$get_alamat['tipe'].' '.$get_alamat['kota'].'</td>
                                                <td>'.$get_alamat['kecamatan'].'</td>
                                            </tr>
                                        ';
                                        ?>
										</tbody>
									</table>
								</div>
							</div>
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

<div id="modal_slider" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="label_slider">Edit Lokasi Pengiriman</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><b>Provinsi</b></div><div class="col-sm-8"><b> : </b><span id="c_prov"><?=$get_alamat['provinsi']?></span></div>
                        <div class="col-sm-4"><b>Kabupaten/Kota</b></div><div class="col-sm-8"><b> : </b><span id="c_kot"><?=$get_alamat['tipe'].' '.$get_alamat['kota']?></span></div>
                        <div class="col-sm-4"><b>Kecamatan</b></div><div class="col-sm-8"><b> : </b><span id="c_kec"><?=$get_alamat['kecamatan']?></span></div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form id="form_lokasi">
                            <label>Ubah Lokasi</label>
                            <input type="hidden" id="kecamatan_id" name="kecamatan_id">
                            <input type="text" style="margin-bottom: 10px" class="form-control" id="kecamatan_pengirim" name="kecamatan_pengirim">
                            <button id="tambah" onclick="simpan_lokasi()" class="btn btn-primary"><span class="fa fa-plus"></span> Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function edit_kecamatan(data, open=null) {
        $("#modal_slider").modal('show');
    }

    function simpan_lokasi() {
        var myForm = document.getElementById('form_lokasi');
        if($("#kecamatan_id").val() == '') {
            alert('Lokasi belum dipilih')
        } else {
            $.ajax({
                url: "<?=base_url() . 'Setting/get_kecamatan/simpan'?>",
                type: "POST",
                data: new FormData(myForm),
                mimeType: "multipart/form-data",
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $("#tambah").html('<span class="fa fa-circle-o-notch fa-spin"></span> Loading').attr('disabled', true);
                },
                complete: function () {
                    $("#tambah").html('<span class="fa fa-plus"></span> Simpan').attr('disabled', false);
                    myForm.reset();
                },
                success: function (res) {
                    location.reload();
                }
            })
        }
    }

    $("#kecamatan_pengirim").autocomplete({
        minChars: 3,
        serviceUrl: '<?=base_url().'setting/get_kecamatan'?>',
        type: 'post',
        dataType: 'json',
        response: function(event, ui) {
            // ui.content is the array that's about to be sent to the response callback.
            if (ui.content.length === 0) {
                $("#empty-message").text("No results found");
            } else {
                $("#empty-message").empty();
            }
        },
        onSelect: function (suggestion) {
            if (suggestion.kecamatan_id != 'not_found') {
                set_pengirim(suggestion);
            } else {
                $("#cari").val('').focus();
            }
        }
    });

    function set_pengirim(data) {
        $("#c_kec").text(data.kecamatan);
        $("#c_kot").text(data.kota);
        $("#c_prov").text(data.provinsi);
        $("#kecamatan_id").val(data.kecamatan_id);
    }
</script>