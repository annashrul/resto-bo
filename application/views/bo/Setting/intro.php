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
                                <div class="col-sm-1" style="margin-top:25px;">
                                    <?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))?>
                                </div>
                            </div>
                            <?= form_close(); ?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>Action</th><th>Background</th><th>Judul</th><th>Keterangan</th>
											</tr>
										</thead>
										<tbody>
                                        <?php
                                        foreach ($res_data as $item) {
                                            if($item['tipe']=='foto'){
                                                $background = '<img style="max-height:100px;" src="' . base_url().$this->m_website->file_thumb($item['background']) . '" />';
                                            } else {
                                                $background = '<div style="width: 100px; height: 20px; background: '.$item['background'].'"></div>';
                                            }
                                            echo '
                                            <tr>
                                                <td style="width: 1%">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><div class="col-sm-12">'.anchor($content.'/edit/?trx='.base64_encode($item['id_intro']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12')).'</div></li>
                                                            <li><div class="col-sm-12">'.anchor($content.'/delete/?trx='.base64_encode($item['id_intro']), '<i class="fa fa-close"></i> Hapus', array('class'=>'btn btn-default col-sm-12', 'onclick'=>'return confirm(\'Akan menghapus data?\')')).'</div></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>'.$background.'</td>
                                                <td>'.$item['judul'].'</td>
                                                <td>'.$item['keterangan'].'</td>
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


