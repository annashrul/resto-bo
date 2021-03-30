<style>
	.bootstrap-datetimepicker-widget tr:hover {
		background-color: #808080;
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
							<?php isset($_GET['pr'])?$update='?pr='.$_GET['pr']:$update=null; ?>
							<?=form_open(strtolower($this->control) . '/' . $page.$update, array('role'=>"form", 'class'=>""))?>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-md-3">
									<label>Periode</label>
									<?php $field = 'field-date';?>
									<div id="daterange" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
								<div class="col-md-1">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
								</div>
								<div class="col-md-1">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
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
											<th style="width: 10px">No</th>
											<th>Tanggal</th>
											<th>Omset Penjualan</th>
											<th>Diskon</th>
											<th>Grand Total</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); $op = 0; $di = 0; $gt = 0; foreach($report as $row){ $no++; ?>
											<tr>
												<td><?=$no?></td>
												<td><?=substr($row['tanggal'], 0, 10)?></td>
												<td class="text-right"><?=number_format($row['omset'],2)?></td>
												<td class="text-right"><?=number_format($row['diskon_nominal'],2)?></td>
												<td class="text-right"><?=number_format($row['omset']-$row['diskon_nominal'],2)?></td>
											</tr>
											<?php
										$op = $op + $row['omset'];
										$di = $di + $row['diskon_nominal'];
										$gt = $gt + $row['omset']-$row['diskon_nominal'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="2">TOTAL PER PAGE</th>
											<th class="text-right"><?=number_format($op, 2)?></th>
											<th class="text-right"><?=number_format($di, 2)?></th>
											<th class="text-right"><?=number_format($gt, 2)?></th>
										</tr>
										<tr>
											<th colspan="2">TOTAL</th>
											<th class="text-right"><?=number_format($top, 2)?></th>
											<th class="text-right"><?=number_format($tdi, 2)?></th>
											<th class="text-right"><?=number_format($tgt, 2)?></th>
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
<script type="text/javascript">
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}

	/*$(document).ready(function () {
		var myURL = document.location;
		var url = new URL(myURL);
		var pr = url.searchParams.get("pr");

		if (pr == 'mg') {
			$("#periode").val("mg");
			$('input[name=tanggal]').datepicker( {
				format: "yyyy-mm-dd",
				autoclose: true
			}).on('show', function(e){

				var tr = $('body').find('.datepicker-days table tbody tr');

				tr.mouseover(function(){
					$(this).addClass('week');
				});

				tr.mouseout(function(){
					$(this).removeClass('week');
				});

				calculate_week_range(e);

			}).on('hide', function(e){
				console.log('date changed');
				calculate_week_range(e);
			});

			var calculate_week_range = function(e){

				var input = e.currentTarget;

				// remove all active class
				$('body').find('.datepicker-days table tbody tr').removeClass('week-active');

				// add active class
				var tr = $('body').find('.datepicker-days table tbody tr td.active.day').parent();
				tr.addClass('week-active');

				// find start and end date of the week

				var date = e.date;
				var start_date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
				var end_date = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 6);

				// make a friendly string

				var friendly_string = start_date.getFullYear() + '-' + ("0" + (start_date.getMonth() + 1)).slice(-2) + '-' + ("0" + start_date.getDate()).slice(-2)  + ' to '
					+ end_date.getFullYear() + '-' + ("0" + (end_date.getMonth() + 1)).slice(-2) + '-' + ("0" + end_date.getDate()).slice(-2);

				console.log(friendly_string);

				$(input).val(friendly_string);

			}
		} else if (pr == 'bl') {
			$("#periode").val("bl");
			$('input[name=tanggal]').datepicker( {
				format: "MM, yyyy",
				minViewMode: 1,
				autoclose: true
			} );
		} else if (pr == 'th') {
			$("#periode").val("th");
			$('input[name=tanggal]').datepicker( {
				format: "yyyy",
				minViewMode: 2,
				autoclose: true
			} );
		} else if (pr == 'cs') {
			$("#periode").val("cs");
		} else {
			$("#periode").val("hr");
			$('input[name=tanggal]').datepicker( {
				format: "yyyy-mm-dd",
				minViewMode: 3,
				autoclose: true
			} );
		}
	});


	$("#periode").change(function () {
		var myURL = document.location.toString().split('?');
		var periode = $(this).val();

		$.ajax({
			url: "<php echo base_url().'site/unset_session/search' ?>",
			type: "GET"
		});

		if (periode == 'hr') {
			document.location = myURL[0] + "?pr=hr";
		} else if (periode == 'mg') {
			document.location = myURL[0] + "?pr=mg";
		} else if (periode == 'bl') {
			document.location = myURL[0] + "?pr=bl";
		} else if (periode == 'cs') {
			document.location = myURL[0] + "?pr=cs";
		} else {
			document.location = myURL[0] + "?pr=th";
		}
	});

	$("#tanggal").change(function () {
		var month = $(this).val().toString().split(',');
	});

	function getMonthFromString(mon){
		var d = Date.parse(mon + "1, 2012");
		if(!isNaN(d)){
			return new Date(d).getMonth() + 1;
		}
		return -1;
	}*/
</script>