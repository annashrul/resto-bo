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
												<th>Action</th><th>Minimal Deposit</th>
											</tr>
										</thead>
										<tbody>
                                        <?php
                                        $kode = $preference['Kode'];
                                        $poin_setting = json_decode($preference['deposit'], true);
                                        ?>
                                        <tr>
                                            <td style="width: 1%">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.base64_encode($kode), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td><?=number_format($poin_setting['minimal'])?></td>
                                        </tr>
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


