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
                                    <label class="control-label col-lg-2">Area</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'area';
                                        $option = null;
                                        $option[''] = 'Pilih Area';
                                        $data_option = $this->m_crud->join_data('area a', 'a.id_area, a.nama, l.nama nama_lokasi', 'lokasi l', 'a.lokasi=l.kode', 'l.'.$this->where_lokasi, 'a.nama asc');
                                        foreach($data_option as $row){ $option[$row['id_area']] = $row['nama'].' | '.$row['nama_lokasi']; }
                                        echo form_dropdown($field, $option, isset($master_data[$field])?$master_data[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                        ?>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nomor Meja Dari</label>
									<div class="col-lg-10">
										<?php $field = 'nama'; ?>
										<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
                                        <b style="color: red" id="ntf_nama"></b>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nomor Meja Sampai</label>
									<div class="col-lg-10">
										<?php $field = 'nama2'; ?>
										<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
                                        <b style="color: red" id="ntf_nama"></b>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kapasitas Meja</label>
									<div class="col-lg-10">
										<?php $field = 'kapasitas'; ?>
										<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Height</label>
									<div class="col-lg-10">
										<?php $field = 'height'; ?>
										<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?(float)$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Width</label>
									<div class="col-lg-10">
										<?php $field = 'width'; ?>
										<input class="form-control" type="number" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?(float)$master_data[$field]:set_value($field)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Bentuk Meja</label>
                                    <div class="col-lg-10">
                                        <?php $field = 'bentuk';
                                        $option = null;
                                        $option[''] = 'Pilih Bentuk Meja';
                                        $option['persegi'] = 'Persegi';
                                        $option['lingkaran'] = 'Lingkaran';
                                        $option['persegi_panjang'] = 'Persegi Panjang';
                                        echo form_dropdown($field, $option, isset($master_data[$field])?$master_data[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                        ?>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
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
function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/cek_data/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data) { 
				if(data==1){
				    $("#ntf_nama").text(pesan);
				    $("#save").prop('disabled', true);
					//alert(pesan);
					//if(tipe=='error'){ alert('error'); }
					//else if(tipe=='warning'){ alert('warning'); }
				} else {
                    $("#ntf_nama").text('');
                    $("#save").prop('disabled', false);
                }
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
		});
	}
}
</script>

