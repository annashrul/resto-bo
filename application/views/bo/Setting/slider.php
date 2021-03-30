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
                            <?php
                            $data_slide = array(
                                'berita' => array(array('id'=>'1', 'foto'=>'assets/images/no_images.png')),
                                'menu' => array(array('id'=>'1', 'foto'=>'assets/images/no_images.png')),
                                'best' => array(array('id'=>'1', 'foto'=>'assets/images/no_images.png'))
                            );

                            //echo json_encode($data_slide);
                            ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Action</th><th>Menu</th><th>Gambar</th>
											</tr>
										</thead>
										<tbody>
                                        <?php
                                        $kode = $preference['Kode'];
                                        $res_slide = json_decode($preference['slider'], true);

                                        foreach ($res_slide as $key => $item) {
                                            $gambar = '';
                                            $data_gambar = $item;

                                            if (count($data_gambar) > 0) {
                                                foreach ($data_gambar as $item) {
                                                    $gambar .= '
                                                    <img src="'.base_url().$item['foto'].'" style="height: 50px">
                                                    ';
                                                }
                                            }

                                            echo '
                                            <tr>
                                                <td style="width: 1%">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><div class="col-sm-12"><a href="javascript:" onclick="edit_slide(\''.$key.'\')" class="btn btn-default col-sm-12"><i class="fa fa-edit"></i> Edit</a></div></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>'.($key=='berita'?'Whats New':($key=='menu'?'Menu':'Best Product')).'</td>
                                                <td>'.$gambar.'</td>
                                            </tr>
                                            ';
                                        }
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
                <h4 class="modal-title" id="label_slider"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="form_slider">
                            <label>Tambah Gambar</label>
                            <input type="file" id="gambar" name="gambar">
                            <input type="hidden" id="param" name="param">
                            <button id="tambah" onclick="simpan_slide()" class="btn btn-primary"><span class="fa fa-plus"></span> Simpan</button>
                        </form>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Hapus</th>
                            </tr>
                            </thead>
                            <tbody id="list_slider">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function edit_slide(data, open=null) {
        $.ajax({
            url: "<?=base_url().'Setting/edit_slide/get_list'?>",
            type: "POST",
            data: {param: data},
            dataType: "JSON",
            beforeSend: function () {
                if (open != null) {
                    $("#list_slider").html("<tr><td colspan='2' class='text-center'><span class='fa fa-circle-o-notch fa-spin'></span> Loading</td></tr>");
                } else {
                    $('#loading').show();
                }
            },
            complete: function () {
                if (open == null) {
                    $('#loading').hide();
                }
            },
            success: function (res) {
                $("#list_slider").html(res.list);
                $("#label_slider").text('Edit Slider');
                $("#param").val(data);
                if (open == null) {
                    $("#modal_slider").modal('show');
                }
            }
        })
    }

    function hapus_slide(data) {
        $.ajax({
            url: "<?=base_url().'Setting/edit_slide/hapus'?>",
            type: "POST",
            data: {param: $("#param").val(), id: data},
            dataType: "JSON",
            beforeSend: function () {
                $("#hapus"+data).html('<span class="fa fa-circle-o-notch fa-spin"></span> Loading').attr('disabled', true);
            },
            success: function (res) {
                edit_slide($("#param").val(), 'yes')
            }
        })
    }

    function simpan_slide() {
        var myForm = document.getElementById('form_slider');
        $.ajax({
            url: "<?=base_url().'Setting/edit_slide/simpan'?>",
            type: "POST",
            data: new FormData(myForm),
            mimeType: "multipart/form-data",
            contentType: false,
            processData: false,
            dataType: "JSON",
            beforeSend: function () {
                $("#tambah").html('<span class="fa fa-circle-o-notch fa-spin"></span> Loading').attr('disabled', true);
            },
            complete: function () {
                $("#tambah").html('<span class="fa fa-plus"></span> Simpan').attr('disabled', false);
                myForm.reset();
            },
            success: function (res) {
                edit_slide($("#param").val(), 'yes')
            }
        })
    }
</script>