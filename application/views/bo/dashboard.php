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
				<?php 
				//echo 'tr_temp_m<br/>'; print_r($this->m_crud->read_data('tr_temp_m', '*', "(m1='DN' or (SUBSTRING(m1,1,3) in ('DN-'))) and m2='".$this->user."'", null, null));
				//echo '<br/><br/>tr_temp_d<br/>'; print_r($this->m_crud->read_data('tr_temp_d', '*', "d1 = 'DN' or (SUBSTRING(d1,1,3) in ('DN-'))"));
				//echo '<br/><br/>master_delivery_note<br/>'; print_r($this->m_crud->read_data('master_delivery_note', '*'));
				//echo '<br/><br/>det_delivery_note<br/>'; print_r($this->m_crud->read_data('det_delivery_note', '*'));
				/*echo '<br/><br/>det order<br/>'; print_r($this->m_crud->read_data('det_order', '*', "no_order = 'MO-1802050001-H'"));
				echo '<br/><br/>master receive order<br/>'; print_r($this->m_crud->read_data('master_receive_order', '*', "no_order = 'MO-1802050001-H'"));
				echo '<br/><br/>det receive order<br/>'; print_r($this->m_crud->read_data('det_receive_order', '*', "no_receive_order = 'RO-1802050002-G'"));
				echo '<br/><br/>master mutasi<br/>'; print_r($this->m_crud->read_data('master_mutasi', '*', "no_faktur_mutasi = 'G171016C0003'", null, null, 1));
				echo '<br/><br/>det mutasi<br/>'; print_r($this->m_crud->read_data('det_mutasi', '*', "no_faktur_mutasi = 'MU-1802050055-G'"));
				echo '<br/><br/>master po<br/>'; print_r($this->m_crud->read_data('master_po', '*', "no_po='PO-1802070002-G'", null, null, 1));
				echo '<br/><br/>detail po<br/>'; print_r($this->m_crud->read_data('detail_po', '*', null, null, null, 1));
				echo '<br/><br/>detail po cabang<br/>'; print_r($this->m_crud->read_data('detail_po_cabang', '*', "no_po='PO-1802070001-G'", null, null, 1));
				echo '<br/><br/>detail qty po cabang<br/>'; print_r($this->m_crud->read_data('detail_qty_po_cabang', '*', "no_po='PO-1802070001-G'", null, null, 1));
				*/
				
				//print_r($this->m_crud->read_data('master_promo','*'));
				//print_r($this->db->query("SELECT diskon, diskon2 FROM master_promo WHERE kode = '01000033' and dariTgl <= '2018-01-26 00:00:00' and sampaiTgl >= '2018-01-26 00:00:00'"));
				/*
				$limit = 1000;
				$count_barang = 250; //$this->m_crud->count_data("barang, Group2, kel_brg", "kd_brg", "ltrim(rtrim(barang.Group2))=ltrim(rtrim(Group2.Kode)) AND ltrim(rtrim(barang.kel_brg))=ltrim(rtrim(kel_brg.kel_brg)) AND ltrim(rtrim(Group1))=ltrim(rtrim('".$id."'))");
				$interval_brg = (int)($count_barang/$limit); if($interval_brg < ($count_barang/$limit)){ $interval_brg++; }
				echo $interval_brg.'<br/><br/>';
				for($i=1; $i<=$interval_brg; $i++){
					$limit_start = ($i-1)*$limit+1; 
					$limit_end = ($limit*$i);
					echo 'start:'.$limit_start.' - end:'.$limit_end.'<br/><br/>';
				}
				*/ ?>
				<div class="row" style="margin-bottom: 10px">
					<div class="col-md-3">
						<label>Periode</label>
						<?php $field = 'field-date';?>
						<div id="daterange" style="cursor: pointer;">
							<div class="input-group">
								<input type="text" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
								<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label>Lokasi</label>
							<?php $field = 'lokasi';
							$option = null; $option['-'] = 'Semua Lokasi';
							//$option['all'] = 'All';
							$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'Nama asc');
							foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
							echo form_dropdown($field, $option, isset($this->session->$field)?$this->session->$field:set_value($field), array('class' => 'select2', 'id'=>$field));
							?>
						</div>
					</div>

				</div>

                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-2">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="st">0</div>
                                <span class="text-muted">SUB TOTAL</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-2">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="dsc">0</div>
                                <span class="text-muted">DISCOUNT</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-2">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="net">0</div>
                                <span class="text-muted">NET SALES</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-2">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="tax">0</div>
                                <span class="text-muted">TAX</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-2">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="srv">0</div>
                                <span class="text-muted">SERVICE</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-2">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="penjualan">0</div>
                                <span class="text-muted">GROSS SALES</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="transaksi">0</div>
                                <span class="text-muted">NUMBER OF TRANSACTION</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6">
                        <div class="panel panel-border panel-primary widget-s-1">
                            <div class="panel-heading"> </div>
                            <div class="panel-body text-center" style="margin-top: -25px">
                                <div class="h4 text-primary" id="avg">0</div>
                                <span class="text-muted">AVG. SALE PER TRANSACTION</span>
                            </div>
                        </div>
                    </div>
                </div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<canvas id="grafik_omset_bulan" height="100"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<canvas id="grafik_transaksi_bulan" height="100"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-4">
										<canvas id="grafik_penjualan_hari" height="250"></canvas>
									</div>
									<div class="col-md-8">
										<canvas id="grafik_penjualan_jam" height="100"></canvas>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-6">
                                        <ul class="nav nav-tabs tabs">
                                            <li class="active tab">
                                                <a href="#volume-1" data-toggle="tab" aria-expanded="false">
                                                    <span class="visible-xs"><i class="fa fa-home"></i></span>
                                                    <span class="hidden-xs">Qty</span>
                                                </a>
                                            </li>
                                            <li class="tab">
                                                <a href="#sales-1" data-toggle="tab" aria-expanded="false">
                                                    <span class="visible-xs"><i class="fa fa-user"></i></span>
                                                    <span class="hidden-xs">Sales</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="volume-1">
                                                <canvas id="produk_teratas" height="220"></canvas>
                                            </div>
                                            <div class="tab-pane" id="sales-1">
                                                <canvas id="produk_teratas2" height="220"></canvas>
                                            </div>
                                        </div>
									</div>

									<div class="col-md-6">
                                        <ul class="nav nav-tabs tabs">
                                            <li class="active tab">
                                                <a href="#volume-2" data-toggle="tab" aria-expanded="false">
                                                    <span class="visible-xs"><i class="fa fa-home"></i></span>
                                                    <span class="hidden-xs">Qty</span>
                                                </a>
                                            </li>
                                            <li class="tab">
                                                <a href="#sales-2" data-toggle="tab" aria-expanded="false">
                                                    <span class="visible-xs"><i class="fa fa-user"></i></span>
                                                    <span class="hidden-xs">Sales</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="volume-2">
                                                <canvas id="kategori_teratas" height="220"></canvas>
                                            </div>
                                            <div class="tab-pane" id="sales-2">
                                                <canvas id="kategori_teratas2" height="220"></canvas>
                                            </div>
                                        </div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="nav nav-tabs tabs">
                                            <li class="active tab">
                                                <a href="#volume-3" data-toggle="tab" aria-expanded="false">
                                                    <span class="visible-xs"><i class="fa fa-home"></i></span>
                                                    <span class="hidden-xs">Qty</span>
                                                </a>
                                            </li>
                                            <li class="tab">
                                                <a href="#sales-3" data-toggle="tab" aria-expanded="false">
                                                    <span class="visible-xs"><i class="fa fa-user"></i></span>
                                                    <span class="hidden-xs">Sales</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="volume-3">
                                                <canvas id="supplier_teratas" height="220"></canvas>
                                            </div>
                                            <div class="tab-pane" id="sales-3">
                                                <canvas id="supplier_teratas2" height="220"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div> <!-- container -->

	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<script>
	$(document).ready(function () {
		get_data($("#field-date").val());
	});

	$("#lokasi").change(function () {
		$.ajax({
			url: "<?php echo base_url().'site/set_session/' ?>" + btoa('lokasi') + '/' + btoa($(this).val()),
			type: "GET",
			success: function () {
				location.reload();
			}
		});
	});

	function get_data(date) {
		var lokasi = $("#lokasi").val();
		$.ajax({
			url: "<?php echo base_url().'site/get_dashboard/' ?>" + btoa(date) + '/' + btoa(lokasi),
			type: "GET",
			dataType: "JSON",
			success: function (res) {
				console.log(res);
                draw(res.gross_sales.label, res.gross_sales.data);
                draw2(res.gross_sales2.label, res.gross_sales2.data);
                draw3(res.top_item.label, res.top_item.data);
                draw4(res.top_cat.label, res.top_cat.data);
                draw5(res.top_item.label2, res.top_item.data2);
                draw6(res.top_cat.label2, res.top_cat.data2);
                draw7(res.top_supp.label, res.top_supp.data);
                draw8(res.top_supp.label2, res.top_supp.data2);
                draw9(res.report_bulan.label, res.report_bulan.data_omset);
                draw10(res.report_bulan.label, res.report_bulan.data_transaksi);
                $("#penjualan").html(res.head.penjualan);
                $("#transaksi").html(res.head.transaksi);
                $("#net").html(res.head.net);
                $("#avg").html(res.head.avg);
                $("#tax").html(res.head.tax);
                $("#srv").html(res.head.srv);
                $("#dsc").html(res.head.dsc);
                $("#st").html(res.head.st);
			}
		});
	}

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET",
			success: function () {
				location.reload();
			}
		});
	}

	function draw(labels_, data_) {
		var penjualan = document.getElementById("grafik_penjualan_jam");
		new Chart(penjualan, {
			type: 'line',
			data: {
				labels: labels_,
				datasets: [{
					data: data_,
					backgroundColor: [
						'rgba(255, 99, 132, 0.2)'
					],
					borderColor: [
						'rgba(255,99,132,1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				title: {
					display: true,
					text: 'HOURLY GROSS SALES AMOUNT',
					fontSize: 16
				},
				legend: {
					display: false
				},
				tooltips: {
					callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        },
						labelColor: function(tooltipItem, chart) {
							return {
								borderColor: 'rgba(255, 99, 132, 0.2)',
								backgroundColor: 'rgba(255,99,132,1)'
							}
						}
					}
				},
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
							userCallback: function(value, index, values) {
								// Convert the number to a string and splite the string every 3 charaters from the end
								value = value.toString();
								value = value.split(/(?=(?:...)*$)/);

								// Convert the array to a string and format the output
								value = value.join(',');
								return value;
							}
                        }
                    }]
                }
			}
		});
	}

	function draw2(labels_, data_) {
		var penjualan = document.getElementById("grafik_penjualan_hari");
		new Chart(penjualan, {
			type:"bar",
			data: {
				labels:labels_,
				datasets:[ {
					data:data_,
					backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"],
					borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)"],
                    borderWidth:1
				}]},
			options:{
				title: {
					display: true,
					text: 'DAY OF THE WEEK GROSS SALES AMOUNT',
					fontSize: 16
				},
				legend: {
					display: false
				},
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        }
                    }
                },
				scales:{
					yAxes:[{
						ticks:{
							beginAtZero:true,
							userCallback: function(value, index, values) {
								// Convert the number to a string and splite the string every 3 charaters from the end
								value = value.toString();
								value = value.split(/(?=(?:...)*$)/);

								// Convert the array to a string and format the output
								value = value.join(',');
								return value;
							}
						}
					}],
                    xAxes: [{
                        ticks: {
                        }
                    }]
				}
			}
		});
	}

	function draw3(labels_, data_) {
		var produk_teratas = document.getElementById("produk_teratas");
		new Chart(produk_teratas, {
			type:"horizontalBar",
			data: {
				labels:labels_,
				datasets:[ {
					data:data_,
					fill:false,
					backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
					borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],"borderWidth":1
				}]},
			options:{
				title: {
					display: true,
					text: 'TOP '+labels_.length+' ITEM VOLUME',
					fontSize: 16
				},
				legend: {
					display: false
				},
				scales:{
					xAxes:[{
						ticks:{
							beginAtZero:true
						}
					}]
				}
			}
		});
	}

	function draw4(labels_, data_) {
		var kategori_teratas = document.getElementById("kategori_teratas");
		new Chart(kategori_teratas, {
			type:"horizontalBar",
			data: {
				labels:labels_,
				datasets:[ {
					data:data_,
					fill:false,
					backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
					borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],"borderWidth":1
				}]},
			options:{
				title: {
					display: true,
					text: 'TOP '+labels_.length+' CATEGORY VOLUME',
					fontSize: 16
				},
				legend: {
					display: false
				},
				scales:{
					xAxes:[{
						ticks:{
							beginAtZero:true
						}
					}]
				}
			}
		});
	}

    function draw5(labels_, data_) {
        var produk_teratas = document.getElementById("produk_teratas2");
        new Chart(produk_teratas, {
            type:"horizontalBar",
            data: {
                labels:labels_,
                datasets:[ {
                    data:data_,
                    fill:false,
                    backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
                    borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],"borderWidth":1
                }]},
            options:{
                title: {
                    display: true,
                    text: 'TOP '+labels_.length+' ITEM SALES',
                    fontSize: 16
                },
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        }
                    }
                },
                scales:{
                    xAxes:[{
                        ticks:{
                            beginAtZero:true,
                            userCallback: function(value, index, values) {
                                // Convert the number to a string and splite the string every 3 charaters from the end
                                value = value.toString();
                                value = value.split(/(?=(?:...)*$)/);

                                // Convert the array to a string and format the output
                                value = value.join(',');
                                return value;
                            }
                        }
                    }]
                }
            }
        });
    }

    function draw6(labels_, data_) {
        var kategori_teratas = document.getElementById("kategori_teratas2");
        new Chart(kategori_teratas, {
            type:"horizontalBar",
            data: {
                labels:labels_,
                datasets:[ {
                    data:data_,
                    fill:false,
                    backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
                    borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],"borderWidth":1
                }]},
            options:{
                title: {
                    display: true,
                    text: 'TOP '+labels_.length+' CATEGORY SALES',
                    fontSize: 16
                },
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        }
                    }
                },
                scales:{
                    xAxes:[{
                        ticks:{
                            beginAtZero:true,
                            userCallback: function(value, index, values) {
                                // Convert the number to a string and splite the string every 3 charaters from the end
                                value = value.toString();
                                value = value.split(/(?=(?:...)*$)/);

                                // Convert the array to a string and format the output
                                value = value.join(',');
                                return value;
                            }
                        }
                    }]
                }
            }
        });
    }

    function draw7(labels_, data_) {
        var supplier_teratas = document.getElementById("supplier_teratas");
        new Chart(supplier_teratas, {
            type:"horizontalBar",
            data: {
                labels:labels_,
                datasets:[ {
                    data:data_,
                    fill:false,
                    backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
                    borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],"borderWidth":1
                }]},
            options:{
                title: {
                    display: true,
                    text: 'TOP '+labels_.length+' SUPPLIER VOLUME',
                    fontSize: 16
                },
                legend: {
                    display: false
                },
                scales:{
                    xAxes:[{
                        ticks:{
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    }

    function draw8(labels_, data_) {
        var supplier_teratas = document.getElementById("supplier_teratas2");
        new Chart(supplier_teratas, {
            type:"horizontalBar",
            data: {
                labels:labels_,
                datasets:[ {
                    data:data_,
                    fill:false,
                    backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)"],
                    borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)"],"borderWidth":1
                }]},
            options:{
                title: {
                    display: true,
                    text: 'TOP '+labels_.length+' SUPPLIER SALES',
                    fontSize: 16
                },
                legend: {
                    display: false
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        }
                    }
                },
                scales:{
                    xAxes:[{
                        ticks:{
                            beginAtZero:true,
                            userCallback: function(value, index, values) {
                                // Convert the number to a string and splite the string every 3 charaters from the end
                                value = value.toString();
                                value = value.split(/(?=(?:...)*$)/);

                                // Convert the array to a string and format the output
                                value = value.join(',');
                                return value;
                            }
                        }
                    }]
                }
            }
        });
    }
	
	function draw9(labels_, data_) {
		var omset_bulan = document.getElementById("grafik_omset_bulan");
		new Chart(omset_bulan, {
			type:"bar",
			data: {
				labels:labels_,
				datasets:[ {
					label:'Bulan Sebelum',
					data:data_.sebelum,
					fill:false,
					backgroundColor:"rgba(255, 99, 132, 0.2)",
					borderColor:"rgb(255, 99, 132)",
                    borderWidth:1
				},{
					label:'Bulan Sekarang',
					data:data_.sekarang,
					fill:false,
					backgroundColor:"rgba(54, 162, 235, 0.2)",
					borderColor:"rgb(54, 162, 235)",
                    borderWidth:1
				}]},
			options:{
				title: {
					display: true,
					text: 'MONTHLY SALES AMOUNT',
					fontSize: 16
				},
				legend: {
					display: true
				},
				/*tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        }
                    }
                },*/
				scales:{
					yAxes:[{
						ticks:{
							beginAtZero:true,
							userCallback: function(value, index, values) {
								// Convert the number to a string and splite the string every 3 charaters from the end
								value = value.toString();
								value = value.split(/(?=(?:...)*$)/);

								// Convert the array to a string and format the output
								value = value.join(',');
								return value;
							}
						}
					}],
                    xAxes: [{
                        ticks: {
                        }
                    }]
				}
			}
		});
	}
	
	function draw10(labels_, data_) {
		var transaksi_bulan = document.getElementById("grafik_transaksi_bulan");
		new Chart(transaksi_bulan, {
			type:"bar",
			data: {
				labels:labels_,
				datasets:[ {
					label:'Bulan Sebelum',
					data:data_.sebelum,
					fill:false,
					backgroundColor:"rgba(255, 99, 132, 0.2)",
					borderColor:"rgb(255, 99, 132)",
                    borderWidth:1
				},{
					label:'Bulan Sekarang',
					data:data_.sekarang,
					fill:false,
					backgroundColor:"rgba(54, 162, 235, 0.2)",
					borderColor:"rgb(54, 162, 235)",
                    borderWidth:1
				}]},
			options:{
				title: {
					display: true,
					text: 'MONTHLY TRANSACTION',
					fontSize: 16
				},
				legend: {
					display: true
				},
				/*tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var value = data.datasets[0].data[tooltipItem.index];
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/);

                            value = value.join(',');
                            return value;
                        }
                    }
                },*/
				scales:{
					yAxes:[{
						ticks:{
							beginAtZero:true,
							userCallback: function(value, index, values) {
								// Convert the number to a string and splite the string every 3 charaters from the end
								value = value.toString();
								value = value.split(/(?=(?:...)*$)/);

								// Convert the array to a string and format the output
								value = value.join(',');
								return value;
							}
						}
					}],
                    xAxes: [{
                        ticks: {
                        }
                    }]
				}
			}
		});
	}
</script>

