<style>
    @media print {
        .print-nota {
            display: block;
            font-family: "Calibri" !important;
            margin: 0;
        }

        @page {
            size: 21.59cm 13.97cm;
        }

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
                                            <label>Status</label>
                                            <?php $field = 'status';
                                            $option = null;
                                            $option[''] = 'Semua Status';
                                            $option['0'] = 'Pending';
                                            $option['1'] = 'Success';
                                            $option['2'] = 'Failed';
                                            $option['3'] = 'Waiting Payment';
                                            echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                            ?>
                                        </div>
                                    </div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="Nama Member/Kode Order" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1">
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
												<th style="width: 1%">No</th>
												<th>Tanggal</th>
												<th>Kode Order</th>
												<th>Nama Customer</th>
												<th>Transaksi</th>
												<th>Harga</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
											foreach($report as $row){
												$no++;
												$display = '';
                                                if ($row['status'] == 0) {
                                                    $status = '<div class="panel panel-warning" style="margin-bottom: -1px"><div class="panel-heading text-center">Pending</div></div>';
                                                } else if ($row['status'] == 1) {
                                                    $status = '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Success</div></div>';
                                                } else if ($row['status'] == 2) {
                                                    $status = '<div class="panel panel-danger" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Failed</div></div>';
                                                } else {
                                                    $status = '<div class="panel panel-info" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Waiting Payment</div></div>';
                                                }
												?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl'],0,19)?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=$row['nama_member']?></td>
													<td><?=$row['transaksi']?></td>
													<td><?=number_format($row['nominal'])?></td>
													<td><?=$status?>
													</td>
												</tr>
											<?php } ?>
										</tbody>
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
    function confirm_deposit(id_member, id_deposit) {
        if (confirm("Akan menyimpan transaksi?")) {
            $.ajax({
                url: "<?=base_url() . 'penjualan/sukses_deposit'?>",
                type: "POST",
                data: {id_member: id_member, id_deposit: id_deposit},
                dataType: "JSON",
                beforeSend: function() {
                    $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
                },
                complete: function() {
                    $('.first-loader').remove();
                },
                success: function (res) {
                    alert(res.pesan);
                    if (res.status) {
                        location.reload();
                    }
                }
            });
        }
    }
	function cancel_deposit(id_member, id_deposit) {
	    if (confirm("Akan membatalkan transaksi?")) {
            $.ajax({
                url: "<?=base_url() . 'api_member/cancel_deposit'?>",
                type: "POST",
                data: {id_member: id_member, id_deposit: id_deposit},
                dataType: "JSON",
                beforeSend: function() {
                    $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
                },
                complete: function() {
                    $('.first-loader').remove();
                },
                success: function (res) {
                    alert(res.pesan);
                    if (res.status) {
                        location.reload();
                    }
                }
            });
        }
    }
    function bukti_tf(gambar, nominal, det_bank) {
        var decode = JSON.parse(atob(det_bank));
        sweetImage(gambar, decode.atas_nama+' ('+decode.bank+'-'+decode.no_rek+') ~ Rp '+to_rp(nominal, '-'))
    }
    function after_change(val) {
        $.ajax({
            url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
            type: "GET"
        });
    }
</script>