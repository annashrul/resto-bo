<style>
	.font_head {
		font-size: 10pt !important;
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
								<!--<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php /*$field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										*/?>
									</div>
								</div>-->
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
								<div class="col-md-12 col-sm-12 col-xs-12" id="table-resposive">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th class="font_head" rowspan="2" style="width: 10px">No</th>
                                                <th class="font_head" rowspan="2">Kode</th>
                                                <th class="font_head" rowspan="2">Supplier</th>
                                                <th class="text-center font_head" colspan="2">Current Stock</th>
                                                <th class="text-center font_head" colspan="2">Receive</th>
                                                <th class="text-center font_head" colspan="5">Sales</th>
                                                <th class="font_head" rowspan="2">Current Assets</th>
                                                <th class="font_head" rowspan="2">Pilihan</th>
                                            </tr>
                                            <tr>
                                                <th class="font_head">Qty</th>
                                                <th class="font_head">Value</th>
                                                <th class="font_head">Qty</th>
                                                <th class="font_head">Value</th>
                                                <th class="font_head">Qty</th>
                                                <th class="font_head">Val Cost</th>
                                                <th class="font_head">Val Price</th>
                                                <th class="font_head">Margin Rp</th>
                                                <th class="font_head">Margin %</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 20):0);
                                            $csq=0; $csv=0; $rq=0; $rv=0; $sq=0; $svc=0; $svp=0;
                                            foreach($report as $row){ $no++; ?>
                                                <tr>
                                                    <td class="font_head"><?=$no?></td>
                                                    <td class="font_head"><?=$row['kode']?></td>
                                                    <td class="font_head"><?=$row['Nama']?></td>
                                                    <td class="font_head"><?=($row['qty_stock']+0)?></td>
                                                    <td class="text-right font_head"><?=number_format($row['value_stock'], 2)?></td>
                                                    <td class="font_head"><?=($row['qty_beli']+0)?></td>
                                                    <td class="text-right font_head"><?=number_format($row['total_pembelian'], 2)?></td>
                                                    <td class="font_head"><?=($row['qty_jual']+0)?></td>
                                                    <td class="text-right font_head"><?=number_format($row['value_beli'], 2)?></td>
                                                    <td class="text-right font_head"><?=number_format($row['value_jual'], 2)?></td>
                                                    <td class="text-right font_head"><?=number_format($row['value_jual']-$row['value_beli'], 2)?></td>
                                                    <td class="font_head"><?=(($row['value_beli']>0 && $row['value_beli']<$row['value_jual'])?round((1 - ($row['value_beli']/$row['value_jual']))*100, 2):'0')?></td>
                                                    <td><?=$row['value_beli']!=0?round($row['value_stock']/$row['value_jual']):0?></td>
                                                    <td><button class="btn btn-primary btn-sm" id="detail" name="detail" onclick="detail_budget('<?=$row['kode']?>')"><i class="md md-visibility"></i> Detail</button></td>
                                                </tr>
                                                <?php
                                                $csq = $csq + (int)$row['qty_stock'];
                                                $csv = $csv + $row['value_stock'];
                                                $rq = $rq + (int)$row['qty_beli'];
                                                $rv = $rv + $row['total_pembelian'];
                                                $sq = $sq + (int)$row['qty_jual'];
                                                $svc = $svc + $row['value_beli'];
                                                $svp = $svp + $row['value_jual'];
                                            } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th class="font_head" colspan="3">TOTAL PER PAGE</th>
                                                <th class="font_head"><?=$csq?></th>
                                                <th class="text-right font_head"><?=number_format($csv, 2)?></th>
                                                <th class="font_head"><?=$rq?></th>
                                                <th class="text-right font_head"><?=number_format($rv, 2)?></th>
                                                <th class="font_head"><?=$sq?></th>
                                                <th class="text-right font_head"><?=number_format($svc, 2)?></th>
                                                <th class="text-right font_head"><?=number_format($svp, 2)?></th>
                                                <th class="text-right font_head"><?=number_format($svp-$svc, 2)?></th>
                                                <th class="font_head"><?=($svc>0 && $svc<$svp)?round((($svp-$svc) / $svc)*100, 2):'0'?></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th class="font_head" colspan="3">TOTAL</th>
                                                <th class="font_head"><?=$tcsq?></th>
                                                <th class="text-right font_head"><?=number_format($tcsv, 2)?></th>
                                                <th class="font_head"><?=$trq?></th>
                                                <th class="text-right font_head"><?=number_format($trv, 2)?></th>
                                                <th class="font_head"><?=$tsq?></th>
                                                <th class="text-right font_head"><?=number_format($tsvc, 2)?></th>
                                                <th class="text-right font_head"><?=number_format($tsvp, 2)?></th>
                                                <th class="text-right font_head"><?=number_format($tsvp-$tsvc, 2)?></th>
                                                <th class="font_head"><?=($tsvc>0 && $tsvc<$tsvp)?round((($tsvp-$tsvc) / $tsvc)*100, 2):'0'?></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                            </tfoot>
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

<div id="detail_budget" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><b>Kode Supplier</b></div><div class="col-sm-8"><b> : </b></div>
                        <div class="col-sm-4"><b>Nama Supplier</b></div><div class="col-sm-8"><b> : </b></div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th class="font_head" rowspan="2" style="width: 10px">No</th>
                                    <th class="font_head" rowspan="2">Kode Barang</th>
                                    <th class="font_head" rowspan="2">Barcode</th>
                                    <th class="font_head" rowspan="2">Nama Barang</th>
                                    <th class="text-center font_head" colspan="2">Current Stock</th>
                                    <th class="text-center font_head" colspan="2">Receive</th>
                                    <th class="text-center font_head" colspan="5">Sales</th>
                                    <th class="font_head" rowspan="2">Current Assets</th>
                                </tr>
                                <tr>
                                    <th class="font_head">Qty</th>
                                    <th class="font_head">Value</th>
                                    <th class="font_head">Qty</th>
                                    <th class="font_head">Value</th>
                                    <th class="font_head">Qty</th>
                                    <th class="font_head">Val Cost</th>
                                    <th class="font_head">Val Price</th>
                                    <th class="font_head">Margin Rp</th>
                                    <th class="font_head">Margin %</th>
                                </tr>
                                </thead>
                                <tbody id="list_budget">
                                </tbody>
                            </table>
                        </div>
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
    function detail_budget(kode) {
        var periode = $("#field-date").val();
        var lokasi = $("#lokasi").val();
        $.ajax({
            url: "<?php echo base_url().'pembelian/detail_budget/' ?>" + btoa(kode) + "/" + btoa(periode) + "/" + btoa(lokasi),
            type: "GET",
            dataType: "JSON",
            success: function (res) {
                $("#detail_budget").modal('show');
            }
        });
    }

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>