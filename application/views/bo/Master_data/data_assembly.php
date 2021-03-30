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
                                                    $option['br.kd_brg'] = 'Kode Barang';
                                                    $option['br.barcode'] = 'Barcode';
                                                    $option['br.nm_brg'] = 'Nama Barang';
													//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
													//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
													echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
													?>
												</div>
												<?php $field = 'any'; ?>
												<input class="form-control" style="height: 40px;" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
													$option = null;$option = null;
													$option['br.kd_brg'] = 'Kode Barang';
													$option['br.barcode'] = 'Barcode';
													$option['br.nm_brg'] = 'Nama Barang';
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
									<div class="col-sm-1" style="margin-top:25px;">
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
									<!--<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
									</div>-->
									<div class="col-sm-1" style="margin-top:25px;">
										<?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))?>
									</div>
								</div>
							<?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="">
										<table id="" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>No</th><th>Action</th><th>Kode Barang</th><th>Barcode</th><th>Nama Barang</th>
												</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 50):0); foreach($master_data as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td>
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
															<ul class="dropdown-menu" role="menu">
                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="detail('<?=$row['kd_brg']?>')"><i class="md md-visibility"></i> Detail</button> </div> </li>
																<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($row['kd_brg']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
																<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus('<?=$table?>', '<?='kd_brg_ass'?>', '<?=$row['kd_brg']?>')"><i class="fa fa-trash"></i> Delete</button></div></li>
															</ul>
														</div>
													</td>
													<td><?=$row['kd_brg']?></td>
													<td><?=$row['barcode']?></td>
													<td><?=$row['nm_brg']?></td>
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
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<div id="modalAssembly" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><b>Kode Barang</b></div><div class="col-sm-8"><b> : </b><b id="kd_brg"></b></div>
                        <div class="col-sm-4"><b>Barcode</b></div><div class="col-sm-8"><b> : </b><b id="barcode"></b></div>
                        <div class="col-sm-4"><b>Nama Barang</b></div><div class="col-sm-8"><b> : </b><b id="nm_brg"></b></div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody id="listAssembly">
                            </tbody>
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

<script>
    function hapus(table, column, id){
        if(confirm('Delete Data?')){
            $.ajax({
                //type:'POST',
                url:'<?=site_url()?>site/delete_ajax/' + table + '/' + column + '/' + id,
                //data: {delete_id : id},
                success: function (data) { window.location='<?=site_url().$this->control?>/<?=$page?>'; },
                error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
            });
        }
    }

    function detail(kode) {
        $.ajax({
            url: "<?=base_url('master_data/detail_assembly/')?>" + btoa(kode),
            type: "GET",
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/loading/load.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                $("#listAssembly").html(res.list);
                $("#kd_brg").text(res.master.kd_brg);
                $("#barcode").text(res.master.barcode);
                $("#nm_brg").text(res.master.nm_brg);
                $("#modalAssembly").modal('show');
            }
        });
    }
</script>

