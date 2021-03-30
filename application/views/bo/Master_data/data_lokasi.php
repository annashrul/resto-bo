<style>
    .cliente {
        margin-top:10px;
        border: #cdcdcd medium solid;
        border-radius: 10px;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
    }
</style>
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
							<?= form_open($content); ?>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'column';
													$option = null;
													$option['Lokasi.Kode'] = 'Kode';
													$option['Lokasi.Nama'] = 'Nama';
													$option['Lokasi.serial'] = 'Serial';
													$option['lokasi_ktg.nama'] = 'Kategori';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'any'; ?>
												<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />	
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Sort</label>
											<div class="input-group">
												<div class="input-group-btn">
													<?php $field = 'order_by';
													$option = null;
													$option['Kode'] = 'Kode';
													$option['Nama'] = 'Nama';
													$option['serial'] = 'Serial';
													$option['lokasi_ktg.nama'] = 'Kategori';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'order_sort';
												$option = null;
												$option['asc'] = 'Ascending';
												$option['desc'] = 'Descending';
												//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
												//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
												echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
												?>
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-6" style="margin-top:25px;">
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
                                        <?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'btn btn-primary'))?>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light" name="export"><i class="fa fa-upload"></i> Export</button>
									</div>
								</div>
							<?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="table-responsive">
										<table id="" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th>Action</th><th>Kode</th><th>Nama Lokasi</th><th>Nama Toko</th><th>Serial</th><th>Kategori</th><th>Alamat</th><th>Footer 1</th>
                                                    <th>Footer 2</th><th>Footer 3</th><th>Footer 4</th><th>Kota</th><th>Email</th><th>Web</th><th>Telepon</th><th>Tampil Di App Member</th><th>Gambar</th><!--<th>Server</th><th>Nama Database</th>-->
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0); foreach($master_data as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['Kode']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="fasilitas('<?=$row['Kode']?>')"><i class="fa fa-list"></i> Fasilitas</button></div></li>
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$table?>', '<?='Kode'?>', '<?=base64_encode($row['Kode'])?>')"><i class="fa fa-trash"></i> Delete</button></div></li>
															</ul>
														</div>
													</td>
													<td><?=$row['Kode']?></td>
													<td><?=$row['Nama']?></td>
													<td><?=$row['nama_toko']?></td>
													<td><?=$row['serial']?></td>
													<td><?=$row['kategori']?></td>
													<td><?=$row['Ket']?></td>
													<td><?=$row['Footer1']?></td>
													<td><?=$row['Footer2']?></td>
													<td><?=$row['Footer3']?></td>
													<td><?=$row['Footer4']?></td>
													<td><?=$row['kota']?></td>
													<td><?=$row['email']?></td>
													<td><?=$row['web']?></td>
													<td><?=$row['phone']?></td>
                                                    <td><img width="25px" src="<?=base_url().'assets/images/status-'.($row['status_show']==1?'Y':'T').'.png'?>" /></td>
                                                    <td><img width="100px" src="<?=base_url().$row['gambar']?>"></td>
													<!--<td><?/*=$row['server']*/?></td>
													<td><?/*=$row['db_name']*/?></td>-->
												</tr>
											<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="pull-right">
										<?= $this->pagination->create_links() ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div> <!-- End Row -->
			
		</div> <!-- container -->
				   
	</div> <!-- content -->

</div>

<div id="modal_fasilitas" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Data Fasilitas</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-5"><b>Kode Lokasi</b></div><div class="col-sm-7"><b> : </b><span id="m_kd_lokasi"></span></div>
                        <div class="col-sm-5"><b>Nama Lokasi</b></div><div class="col-sm-7"><b> : </b><span id="m_nm_lokasi"></span></div>
                    </div>
                </div>
                <hr/>
                <div class="row" style="margin-bottom: 10px">
                    <button type="button" class="btn btn-primary waves-effect" onclick="tambah_fasilitas()">Tambah Fasilitas</button>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 1%">No</th>
                                <th>Aksi</th>
                                <th>Fasilitas</th>
                                <th>Foto</th>
                            </tr>
                            </thead>
                            <tbody id="list_fasilitas"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal_form_fasilitas" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Data Fasilitas</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="form_fasilitas">
                        <input type="hidden" name="lokasi" id="fasilitas_lokasi">
                        <input type="hidden" name="param" id="param" value="add">
                        <input type="hidden" name="hapus_gambar" id="hapus_gambar" value="[]">
                        <input type="hidden" name="gambar_sekarang" id="gambar_sekarang" value="[]">
                        <input type="hidden" name="id_fasilitas" id="id_fasilitas">
                        <div class="form-group " style="margin-bottom:10px;">
                            <label class="control-label col-lg-2">Fasilitas</label>
                            <div class="col-lg-10">
                                <select class="form-control" id="fasilitas" name="fasilitas">
                                </select>
                            </div>
                        </div>
                        <div class="form-group " style="margin-bottom:5px;">
                            <label class="control-label col-lg-2">Gambar Fasilitas</label>
                            <div class="col-lg-10">
                                <input type="file" multiple name="gambar[]" accept="image/gif,image/jpg,image/jpeg,image/png">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" id="list_gambar"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect" onclick="simpan_fasilitas()">Simpan</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
function hapus(table, column, id){
	if(confirm('Delete Data?')){
		$.ajax({
			//type:'POST',
			url:'<?=site_url().$this->control?>/delete_barang/' + table + '/' + column + '/' + id,
			//url:"<?=site_url()?>site/delete_ajax2/" + table + "/" + btoa(column + " = '" +atob(id)+ "'") + "/" + btoa("select kd_brg from kartu_stock where kd_brg = '"+atob(id)+"' and keterangan <> 'Input Barang'"),
			//data: {delete_id : id},
			success: function (data) { 
				if(data==1){ 
					window.location='<?=site_url().$this->control?>/<?=$page?>';
				} else {
					alert('Delete Failed. Data sudah digunakan transaksi');
				}
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
		});
	}
}

var tmp_gambar = [];

function hapus_gambar(id, key) {
    $("#"+id).remove();
    tmp_gambar.push(key);

    $("#hapus_gambar").val(JSON.stringify(tmp_gambar));
}

function edit_fasilitas(id_fasilitas) {
    var id = $("#m_kd_lokasi").text();
    $("#modal_form_fasilitas").modal('show');
    setTimeout(function () {
        $('body').addClass('modal-open');
    }, 1000);
    $("#param").val('edit');
    $.ajax({
        url: "<?=base_url().'master_data/edit_fasilitas'?>",
        type: "POST",
        data: {id: id_fasilitas, lokasi: id},
        dataType: "JSON",
        beforeSend: function() {
            $("#fasilitas").html('<option value="">Loading...</option>');
        },
        success: function (res) {
            $("#fasilitas_lokasi").val(id);
            $("#id_fasilitas").val(id_fasilitas);
            $("#fasilitas").html(res.list);
            $("#list_gambar").html(res.list_gambar);
            $("#gambar_sekarang").val(res.gambar_sekarang);
        }
    });
}

function hapus_fasilitas(id_fasilitas) {
    var id = $("#m_kd_lokasi").text();
    if (confirm("Akan menghapus data?")) {
        $.ajax({
            url: "<?=base_url() . 'master_data/hapus_fasilitas'?>",
            type: "POST",
            data: {id: id_fasilitas},
            dataType: "JSON",
            beforeSend: function () {
                $('body').append('<div class="first-loader"><img src="<?=base_url() . '/assets/images/spin.svg'?>"></div>');
            },
            success: function (res) {
                fasilitas(id, 'load');
            }
        });
    }
}

function tambah_fasilitas() {
    var id = $("#m_kd_lokasi").text();
    $("#modal_form_fasilitas").modal('show');
    setTimeout(function () {
        $('body').addClass('modal-open');
    }, 1000);
    $("#param").val('add');
    $.ajax({
        url: "<?=base_url().'master_data/fasilitas_tersedia'?>",
        type: "POST",
        data: {id: id},
        dataType: "JSON",
        beforeSend: function() {
            $("#fasilitas").html('<option value="">Loading...</option>');
        },
        success: function (res) {
            $("#fasilitas_lokasi").val(id);
            $("#fasilitas").html(res.list);
        }
    });
}

function simpan_fasilitas() {
    var myForm = document.getElementById('form_fasilitas');
    if ($("#fasilitas").val() == '') {
        swal({
            'type':'error',
            'title':'Peringatan',
            'text':'Fasilitas tidak boleh kosong'
        });
    } else {
        $.ajax({
            url: "<?=base_url() . 'master_data/simpan_fasilitas'?>",
            type: "POST",
            data: new FormData(myForm),
            mimeType: "multipart/form-data",
            contentType: false,
            processData: false,
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                fasilitas($("#fasilitas_lokasi").val(), 'load');
                $("#modal_form_fasilitas").modal('hide');
            }
        });
    }
}

function fasilitas(id, load=null) {
    $.ajax({
        url: "<?=base_url().'master_data/get_fasilitas'?>",
        type: "POST",
        data: {id: id},
        dataType: "JSON",
        beforeSend: function() {
            $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
        },
        complete: function() {
            $('.first-loader').remove();
        },
        success: function (res) {
            $("#list_fasilitas").html(res.list);
            $("#m_kd_lokasi").text(res.kode);
            $("#m_nm_lokasi").text(res.nama);
            if (load == null) {
                $("#modal_fasilitas").modal('show');
                $('body').addClass('modal-open');
            }
        }
    })
}

$("#modal_form_fasilitas").on("hide.bs.modal", function () {
    document.getElementById("form_fasilitas").reset();
    $("#list_gambar").html('');
    $("#modal_fasilitas").modal('show');
    setTimeout(function () {
        $('body').addClass('modal-open');
    }, 1000);
}).on("show.bs.modal", function () {
    $("#modal_fasilitas").modal('hide');
});
</script>

