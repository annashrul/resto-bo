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
										<label>Tanggal Periode</label>
										<?php $field = 'tgl_periode'; ?>
										<div class="input-group">
											<input type="text" class="form-control datepicker_date" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y-m-d"))?>">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'Nama asc');
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
                                    <a class="btn btn-primary waves-effect waves-light" href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode(json_encode(array('tgl_periode' => isset($this->session->search['tgl_periode'])?$this->session->search['tgl_periode']:'', 'lokasi' => isset($this->session->search['lokasi'])?$this->session->search['lokasi']:'')))?>" target="_blank">Export</a>
								</div>
							</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="row">
									<?php
                                    if (isset($result['sales_report_by_financial'])) {
                                        for ($i = 0; $i < count($result['sales_report_by_financial']); $i++) {
                                            ?>
                                            <div class="col-lg-4">
                                                <div class="panel panel-border panel-info">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title">
                                                            Kassa <?= $result['sales_report_by_financial'][$i]['Kassa'] ?></h3>
                                                    </div>
                                                    <div class="panel-body">
                                                        <table width="100%" border="0">
                                                            <tr>
                                                                <td>Total Sales</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['total_sales']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Discount Item</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['diskon_item']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Discount Total</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['diskon_total']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Net Omset</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['net_omset']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Tax</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['tax_total']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Total Omset</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['total_omset']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Cash</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['cash_total']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Edc Seetle</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['edc_total']) ?></th>
                                                            </tr>
                                                            <!---->
                                                            <tr>
                                                                <td style="border-top: solid; border-width: thin">
                                                                    Receive Amount
                                                                </td>
                                                                <th style="border-top: solid; border-width: thin; text-align: right"><?= number_format($result['sales_report_by_financial'][$i]['receive_amount']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Other Income</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['other_income']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Total Income</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['total_income']) ?></th>
                                                            </tr>
                                                            <!---->
                                                            <tr>
                                                                <td style="border-top: solid; border-width: thin">Cash
                                                                    In Hand
                                                                </td>
                                                                <th style="border-top: solid; border-width: thin; text-align: right"><?= number_format($result['sales_report_by_financial'][$i]['cash_in_hand']) ?></th>
                                                            </tr>
                                                            <!---->
                                                            <tr>
                                                                <td style="border-top: solid; border-width: thin">
                                                                    Return
                                                                </td>
                                                                <th style="border-top: solid; border-width: thin; text-align: right"><?= number_format($result['sales_report_by_financial'][$i]['return']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Paid Out</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['paid_out']) ?></th>
                                                            </tr>
                                                            <tr>
                                                                <td>Total Outcome</td>
                                                                <th class="pull-right"><?= number_format($result['sales_report_by_financial'][$i]['total_outcome']) ?></th>
                                                            </tr>
                                                            <!---->
                                                            <tr>
                                                                <td style="border-top: solid; border-width: thin">Total
                                                                    Cash Sales
                                                                </td>
                                                                <th style="border-top: solid; border-width: thin; text-align: right"><?= number_format($result['sales_report_by_financial'][$i]['total_cash_sales']) ?></th>
                                                            </tr>
                                                            <!---->
                                                            <tr>
                                                                <td style="border-top: solid; border-width: thin">
                                                                    Cashier Cash
                                                                </td>
                                                                <th style="border-top: solid; border-width: thin; text-align: right"><?= number_format($result['sales_report_by_financial'][$i]['cashier_cash']) ?></th>
                                                            </tr>
                                                            <!---->
                                                            <tr>
                                                                <td style="border-top: solid; border-width: thin">
                                                                    Status
                                                                </td>
                                                                <th style="border-top: solid; border-width: thin; text-align: right"><?= $result['sales_report_by_financial'][$i]['status_report'] ?></th>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
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
