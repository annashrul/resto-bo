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

            <?php
            $s1 = 0;
            $s2 = 0;
            $s3 = 0;
            $s4 = 0;
            $s5 = 0;

            $p1 = 0;
            $p2 = 0;
            $p3 = 0;
            $p4 = 0;
            $p5 = 0;

            foreach ($rating as $item) {
                if ((int)$item['response'] == 1) {
                    $s1 = $item['total'];
                } else if ((int)$item['response'] == 2) {
                    $s2 = $item['total'];
                } else if ((int)$item['response'] == 3) {
                    $s3 = $item['total'];
                } else if ((int)$item['response'] == 4) {
                    $s4 = $item['total'];
                } else if ((int)$item['response'] == 5) {
                    $s5 = $item['total'];
                }
            }

            $total = $s1+$s2+$s3+$s4+$s5;

            if ($total > 0) {
                $p1 = round($s1/$total, 2)*100;
                $p2 = round($s2/$total, 2)*100;
                $p3 = round($s3/$total, 2)*100;
                $p4 = round($s4/$total, 2)*100;
                $p5 = round($s5/$total, 2)*100;
            }

            ?>
            <div class="row" id="thisDiv">
                <!--Content Here-->
                <div class="col-md-6 col-sm-6 col-lg-2 col-lg-offset-1">
                    <div class="mini-stat clearfix bx-shadow">
                        <img src="<?=base_url().'assets/images/s1.png'?>" style="height: 20px">
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"><?=$s1?></span>
                            Sangat Buruk
                        </div>
                        <div class="tiles-progress">
                            <div class="m-t-20">
                                <h5 class="text-uppercase">Total <span class="pull-right" id="per_excl"><?=$p1?>%</span></h5>
                                <div class="progress progress-sm m-0">
                                    <div id="prog_excl" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$p1?>%;">
                                        <span id="sr_excl" class="sr-only"><?=$p1?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-lg-2">
                    <div class="mini-stat clearfix bx-shadow">
                        <img src="<?=base_url().'assets/images/s2.png'?>" style="height: 20px">
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"><?=$s2?></span>
                            Buruk
                        </div>
                        <div class="tiles-progress">
                            <div class="m-t-20">
                                <h5 class="text-uppercase">Total <span class="pull-right" id="per_good"><?=$p2?>%</span></h5>
                                <div class="progress progress-sm m-0">
                                    <div id="prog_good" class="progress-bar progress-bar-purple" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$p2?>%;">
                                        <span id="sr_good" class="sr-only"><?=$p2?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-lg-2">
                    <div class="mini-stat clearfix bx-shadow">
                        <img src="<?=base_url().'assets/images/s3.png'?>" style="height: 20px">
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"><?=$s3?></span>
                            Biasa Saja
                        </div>
                        <div class="tiles-progress">
                            <div class="m-t-20">
                                <h5 class="text-uppercase">Total <span class="pull-right" id="per_fair"><?=$p3?>%</span></h5>
                                <div class="progress progress-sm m-0">
                                    <div id="prog_fair" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$p3?>%;">
                                        <span id="sr_fair" class="sr-only"><?=$p3?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-lg-2">
                    <div class="mini-stat clearfix bx-shadow">
                        <img src="<?=base_url().'assets/images/s4.png'?>" style="height: 20px">
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"><?=$s4?></span>
                            Baik
                        </div>
                        <div class="tiles-progress">
                            <div class="m-t-20">
                                <h5 class="text-uppercase">Total <span class="pull-right" id="per_bad"><?=$p4?>%</span></h5>
                                <div class="progress progress-sm m-0">
                                    <div id="prog_bad" class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$p4?>%;">
                                        <span id="sr_bad" class="sr-only"><?=$p4?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-lg-2">
                    <div class="mini-stat clearfix bx-shadow">
                        <img src="<?=base_url().'assets/images/s5.png'?>" style="height: 20px">
                        <div class="mini-stat-info text-right text-muted">
                            <span class="counter"><?=$s5?></span>
                            Sangat Baik
                        </div>
                        <div class="tiles-progress">
                            <div class="m-t-20">
                                <h5 class="text-uppercase">Total <span class="pull-right" id="per_bad"><?=$p5?>%</span></h5>
                                <div class="progress progress-sm m-0">
                                    <div id="prog_bad" class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: <?=$p5?>%;">
                                        <span id="sr_bad" class="sr-only"><?=$p5?>%</span>
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
						<div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
							<?=form_open(strtolower($this->control) . '/' . $page, array('role'=>"form", 'class'=>""))?>
							<div class="row">
								<div class="col-sm-3" style="margin-bottom:10px">
							    	<label>Periode</label>
									<?php $field = 'field-date';?>
									<div id="daterange" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
                                <div class="col-sm-3">
									<div class="form-group">
										<label>Search</label>
                                        <?php $field = 'lokasi';
                                        $option = null; $option['-'] = 'Semua Lokasi';
                                        //$option['all'] = 'All';
                                        $data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
                                        foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                        echo form_dropdown($field, $option, isset($this->session->$field)?$this->session->$field:set_value($field), array('class' => 'select2', 'id'=>$field));
                                        ?>
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
									<div class="table-responsive">
										<table id="" class="table table-small-font table-striped table-bordered">
											<thead>
											<tr>
												<th>No</th>
												<th>Kode Transaksi</th>
												<th>Tanggal</th>
												<th>Lokasi</th>
												<th>Customer</th>
												<th>Rating</th>
												<th>Judul</th>
												<th>Komentar</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
											foreach($report as $row){
											    $no++;
											    $explode = explode('|', $row['comment']);
											    ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=substr($row['tgl'],0,19)?></td>
													<td><?=$row['nama_lokasi']?></td>
													<td><?=$row['nama_customer']?></td>
													<td><?=($row['response']+0)?></td>
													<td><?=$explode[0]?></td>
													<td><?=$explode[1]?></td>
												</tr>
											<?php } ?>
											</tbody>
											<!--
											<tfoot>
											<tr>
												<th colspan="7">TOTAL PER PAGE</th>
												<th></th>
											</tr>
											<tr>
												<th colspan="7">TOTAL</th>
												<th></th>
											</tr>
											</tfoot>
											-->
										</table>
									</div>
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

<?php /*$i =  0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-3"><b>Tanggal</b></div><div class="col-sm-9"><b> : </b><?=substr($row['tgl'],0,10)?></div>
							<div class="col-sm-3"><b>No. Nota</b></div><div class="col-sm-9"><b> : </b><?=$row['kd_trx']?></div>
							<div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['Lokasi']?></div>
						</div>
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_kasir']?></div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table id="datatable" class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>Kode Barang</th>
									<th>Nama Barang</th>
									<th>Open Price</th>
									<th>Harga Jual</th>
									<th>Qty</th>
									<th>Diskon</th>
									<th>Sub Total</th>
								</tr>
								</thead>
								<tbody>
								<?php $no = 0;
								$detail = $this->m_crud->read_data("Det_Trx dt, barang br", "dt.*, br.nm_brg, br.satuan", "dt.kd_brg=br.kd_brg AND dt.kd_trx = '".$row['kd_trx']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($rows['open_price']=='1'?'Y':'T').'.png'?>" /></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
										<td class="text-right"><?=number_format($rows['qty']).' '.$rows['satuan']?></td>
										<td class="text-right"><?=number_format($rows['dis_persen'])?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual']*$rows['qty']-$rows['dis_persen'])?></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_trx'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } */ ?>

<script>
	function after_change(val) {
        $.ajax({
            url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
            type: "GET"
        });
    }

    function delete_trans(kode) {
        if (confirm('Akan menghapus data?')) {
            var table_ = ['Master_Trx', 'Det_Trx', 'Kartu_stock'];
            var condition_ = ['kd_trx=\''+kode+'\'','kd_trx=\''+kode+'\'','kd_trx=\''+kode+'\''];

            if (otorisasi('penjualan', {table: table_, condition: condition_})) {
                $.ajax({
                    url: "<?php echo base_url() . 'site/delete_ajax_trx' ?>",
                    type: "POST",
                    data: {table: table_, condition: condition_},
                    success: function (res) {
                        if (res == true) {
                            alert("Data berhasil dihapus!");
                        } else {
                            alert("Data gagal dihapus!");
                        }
                        location.reload();
                    }
                });
            }
        }
    }
</script>