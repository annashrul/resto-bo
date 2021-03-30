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
							<?=form_open(strtolower($this->control) . '/' . $page, array('role'=>"form", 'class'=>""))?>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
									</div>
								</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>No</th>
												<th>Kode Member</th>
												<th>Nama Member</th>
												<th>Deposit</th>
											</tr>
										</thead>
										<tbody>
											<?php
                                            $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0);
                                            $total = 0;
											foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$row['kode']?></td>
													<td><?=$row['nama']?></td>
													<td class="text-right"><?=number_format($row['saldo'])?></td>
												</tr>
											<?php
											$total = $total + $row['saldo'];
											} ?>
										</tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="3">TOTAL PER PAGE</th>
                                            <th class="text-right"><?=number_format($total)?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">TOTAL</th>
                                            <th class="text-right"><?=number_format($total_saldo['saldo'])?></th>
                                        </tr>
                                        </tfoot>
									</table>
									<div class="pull-right">
										<?php
										    echo $this->pagination->create_links();
										?>
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

<script>
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script