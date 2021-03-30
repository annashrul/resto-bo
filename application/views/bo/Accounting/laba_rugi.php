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
                                <div class="col-lg-3"></div>
                                <div class="col-lg-6">
                                    <div class="panel panel-border panel-info">
                                        <div class="panel-heading">
                                            <?php $field = 'field-date';?>
                                            <h3 class="panel-title">Periode <?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?></h3>
                                        </div>
                                        <div class="panel-body">
                                            <table width="100%" border="0">
                                                <tr>
                                                    <th>Pendapatan</th>
                                                </tr>
                                                <tr>
                                                    <td>Total Penjualan</td>
                                                    <th class="pull-right"><?=number_format($result['penjualan'])?></th>
                                                </tr>
                                                <tr>
                                                    <td>Diskon Pembelian</td>
                                                    <th class="pull-right"><?=number_format($result['dis_penjualan'])?></th>
                                                </tr>
                                                <tr>
                                                    <td>Kas Masuk</td>
                                                    <th class="pull-right"><?=number_format($result['kas_masuk'])?></th>
                                                </tr>
                                                <?php $total_pendapatan = $result['penjualan']+$result['dis_penjualan']+$result['kas_masuk'] ?>
                                                <tr>
                                                    <th>Total Pendapatan</th>
                                                    <th style=" text-align: right"><?=number_format($total_pendapatan)?></th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <th style="border-top: solid; border-width: thin">Beban</th>
                                                    <th style="border-top: solid; border-width: thin"></th>
                                                </tr>
                                                <tr>
                                                    <td>HPP</td>
                                                    <th style=" text-align: right"><?=number_format($result['hpp'])?></th>
                                                </tr>
                                                <tr>
                                                    <td>Diskon Penjualan</td>
                                                    <th class="pull-right"><?=number_format($result['dis_penjualan'])?></th>
                                                </tr>
                                                <tr>
                                                    <td>Kas Keluar</td>
                                                    <th class="pull-right"><?=number_format($result['kas_keluar'])?></th>
                                                </tr>
                                                <?php $total_beban = $result['hpp']+$result['dis_penjualan']+$result['kas_keluar'] ?>
                                                <tr>
                                                    <th>Total Beban</th>
                                                    <th style=" text-align: right"><?=number_format($total_beban)?></th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <th style="border-top: solid; border-width: thin">Laba</th>
                                                    <th style="border-top: solid; border-width: thin; text-align: right"><?=number_format($total_pendapatan-$total_beban)?></th>
                                                </tr>
                                            </table>
                                        </div>
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
</script>