<style>
	th, td {
		font-size: 9pt;
	}

	.form-control {
		font-size: 9pt;
	}
</style>
<!-- Page-Title -->
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

			<!-- Main Content -->
			<?=form_open($content)?>
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota System</label>
													<div class="col-sm-6">
														<?php $field = 'nota_sistem'; ?>
														<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control" readonly value="<?=set_value($field)?set_value($field):null?>">
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tanggal</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number();" name="tanggal" id="tanggal" type="text" value="<?=set_value('tanggal')?set_value('tanggal'):date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Lokasi</label>
													<div class="col-sm-6">
														<?php $field = 'lokasi';
														$option = null; $option[''] = 'Pilih';
														$data_option = $data_lokasi;
														foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
														echo form_dropdown($field, $option, set_value($field)?set_value($field):null, array('class' => 'select2', 'onchange'=>'trx_number()', 'id'=>$field, 'required'=>'required'));
														?><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Jenis Pembayaran</label>
													<div class="col-sm-6">
														<?php $field = 'cara_byr';
														$option = null; $option[''] = 'Pilih';
														$option['Tunai'] = 'Tunai';
														$option['Transfer'] = 'Transfer';
														$option['Cek/Giro'] = 'Cek/Giro';
														//$data_option = $data_lokasi;
														//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
														echo form_dropdown($field, $option, set_value($field)?set_value($field):null, array('class' => 'select2', 'onchange'=>'trx_number()', 'id'=>$field, 'required'=>'required'));
														?><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													</div>
												</div>
												<div id="non_tunai">
													<div class="row" style="margin-bottom: 3px">
														<label class="col-sm-4">Bank</label>
														<div class="col-sm-6">
															<?php $field = 'bank';
															$option = null; //$option[''] = 'Pilih';
															$data_option = $data_bank;
															foreach($data_option as $row){ $option[$row['Nama']] = $row['Nama']; }
															echo form_dropdown($field, $option, set_value($field)?set_value($field):null, array('class' => 'select2', 'onchange'=>'trx_number()', 'id'=>$field, 'required'=>'required'));
															?><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row" style="margin-bottom: 3px">
														<label class="col-sm-4">Tanggal Pencairan</label>
														<div class="col-sm-6">
															<div class="input-group date">
																<div class="input-group-addon">
																	<i class="fa fa-calendar"></i>
																</div>
																<input class="form-control pull-right datepicker_date_from" readonly onchange="trx_number();" name="tanggal_cair" id="tanggal_cair" type="text" value="<?=set_value('tanggal_cair')?set_value('tanggal_cair'):date("Y-m-d")?>">
															</div>
														</div>
													</div>
													<div class="row" style="margin-bottom: 3px">
														<label class="col-sm-4">No Cek / Giro</label>
														<div class="col-sm-6">
															<?php $field='nogiro'; ?>
															<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control" value="<?=set_value($field)?set_value($field):null?>">
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Nota Pembelian</label>
													<div class="col-sm-6">
														<div class="input-group">
															<?php $field = 'nota_beli';
															/*$option = null; $option[''] = 'Pilih';
															$data_option = $data_supplier;
															foreach($data_option as $row){ $option[$row['Kode']] = $row['Kode'].' | '.$row['Nama']; }
															echo form_dropdown($field, $option, set_value($field)?set_value($field):null, array('class' => 'select2', 'onchange'=>'', 'id'=>$field, 'required'=>'required'));
															*/ ?>
															<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control autocomplete_data" value="<?=set_value($field)?set_value($field):(isset($data_pembelian[0]['no_faktur_beli'])?$data_pembelian[0]['no_faktur_beli']:null)?>" required>
															<div class="input-group-btn">
																<button type="submit" id="cari" name="cari" class="btn btn-primary"><i class="md md-search"></i></button>
															</div>
														</div>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Supplier</label>
													<div class="col-sm-6">
														<?php $field = 'supplier' ?>
														<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control" readonly value="<?=set_value($field)?set_value($field):(isset($data_pembelian[0]['Nama'])?$data_pembelian[0]['Nama']:null)?>">
                                                        <?=form_error('ket', '<div class="error" style="color:red;">', '</div>')?>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tanggal Jatuh Tempo</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<?php $field='tgl_jatuh_tempo'; ?>
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right" readonly name="<?=$field?>" id="<?=$field?>" type="text" value="<?=set_value($field)?set_value($field):(isset($data_pembelian[0][$field])?substr($data_pembelian[0][$field],0,10):null)?>">
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Keterangan</label>
													<div class="col-sm-6">
														<input type="text" id="ket" name="ket" class="form-control" value="<?=set_value('ket')?set_value('ket'):null?>">
                                                        <?=form_error('ket', '<div class="error" style="color:red;">', '</div>')?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr/>
							<!--<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="table-responsive">
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th style="width: 10px">No</th><th>Nota Pembelian</th><th>Hutang</th><th>Retur</th><th>Jumlah Hutang</th>
												</tr>
											</thead>
											<tbody id="list_barang">
												<?php $no = 0; foreach($data_pembelian as $row){ $no++; ?>
												<?php //$hutang = ($row['nilai_pembelian']-$row['disc']) + (($row['PPN']/100) * $row['nilai_pembelian']); ?>
												<?php $hutang = $row['nilai_pembelian']; ?>
												<?php $retur = $row['jumlah_retur']; ?>
												<?php $bayar = $row['jumlah_bayar']; ?>
												<?php $jumlah_hutang = $hutang - $bayar; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$row['no_faktur_beli']?></td>
													<td><?=number_format($hutang, 2)?></td>
													<td><?=number_format($retur, 2)?></td>
													<td><?=number_format($jumlah_hutang, 2)?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>-->
							<div class="row">
								<div class="col-md-12">
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Hutang</label>
													<div class="col-sm-6">
														<?php $field = 'hutang'; ?>
														<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control" value="<?=isset($hutang)?number_format($hutang, 0):null?>">
													</div>
												</div>
												<!--<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Retur</label>
													<div class="col-sm-6">
														<?php /*$field = 'retur'; */?>
														<input type="text" id="<?/*=$field*/?>" name="<?/*=$field*/?>" class="form-control" readonly value="<?/*=isset($retur)?number_format($retur, 0):null*/?>">
													</div>
												</div>-->
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Bayar</label>
													<div class="col-sm-6">
														<?php $field = 'bayar'; ?>
														<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control"  value="<?=isset($bayar)?number_format($bayar, 0):null?>">
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Jumlah Hutang</label>
													<div class="col-sm-6">
														<?php $field = 'jumlah_hutang'; ?>
														<input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control"  value="<?=isset($hutang)?number_format($jumlah_hutang, 0):null?>">
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Jumlah Pembayaran</label>
													<div class="col-sm-6">
														<?php $field='jumlah_bayar'; ?>
														<input type="text" id="<?=$field?>" name="<?=$field?>" onkeyup="hitung_pembulatan()" onfocus="$(this).select()" class="form-control text-right angka_nominal" value="<?=set_value($field)?set_value($field):0?>" />
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Pembulatan</label>
													<div class="col-sm-1">
														<?php $field = 'cek_pembulatan'; ?>
														<div class="checkbox checkbox-primary">
															<input class="form-control" type="checkbox" onclick="hitung_pembulatan()" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)==1)?('checked'):null?> />
															<label for="<?=$field?>"></label>
														</div>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													</div>
													<div class="col-sm-5">
														<?php $field='pembulatan'; ?>
														<input type="text" readonly id="<?=$field?>" name="<?=$field?>" class="form-control text-right" value="<?=set_value($field)?set_value($field):0?>" />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr/>
							<div class="row">
								<div class="col-md-7">
									<button class="btn btn-primary" onclick="if(confirm('Akan menyimpan transaksi?')){submit_transaksi('simpan')}" type="button">Simpan</button>
									<button class="btn btn-primary" onclick="if(confirm('Akan membatalkan transaksi?')){window.location='<?=base_url().$content?>'}" type="button">Batal</button>
									<button class="btn btn-primary" onclick="if(confirm('Akan menutup transaksi?')){window.location='<?=base_url().'site/dashboard'?>'}" type="button">Keluar</button>
									
									<button hidden id="simpan" name="simpan" type="submit">Simpan</button>
									
									<!--<button class="btn btn-primary" id="tambah_return" type="submit">Tambah Return</button>
									<button class="btn btn-primary" id="rincian_return" type="submit">Rincian Return</button>-->
								</div>
								<div class="col-md-5">
									<div class="pull-right">
										<!--
										<div class="row" style="margin-bottom: 3px">
											<label class="col-sm-4">Discount</label>
											<div class="col-sm-3">
												<input onblur="update_tmp_master('m10', $('#discount_harga').val())" onkeyup="hitung_total(1); konversi_diskon('persen')" type="number" id="discount_persen" name="discount_persen" class="form-control" placeholder="%">
											</div>
											<div class="col-sm-5">
												<input onblur="update_tmp_master('m10', $(this).val())" onkeyup="hitung_total(); konversi_diskon('harga')" type="number" id="discount_harga" name="discount_harga" class="form-control text-right" placeholder="Rp">
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<label class="col-sm-4">Pajak %</label>
											<div class="col-sm-3">
												<input onblur="update_tmp_master('m11', $(this).val())" onkeyup="hitung_total()" type="number" id="pajak" name="pajak" class="form-control" placeholder="%">
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<div class="col-sm-8">
												<input type="hidden" id="total" name="total" class="form-control text-right" readonly>
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<div class="col-sm-8">
												<input type="hidden" id="total_return" name="total_return" class="form-control text-right" readonly>
											</div>
										</div>
										<div class="row" style="margin-bottom: 3px">
											<label class="col-sm-4">Grand Total</label>
											<div class="col-sm-8">
												<input type="text" id="grand_total" name="grand_total" class="form-control text-right" readonly>
											</div>
										</div>-->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?=form_close()?>
		</div>
	</div>
</div>


<?php 
if(isset($_POST['cara_byr']) && ($_POST['cara_byr']=='Cek/Giro')){
	echo '<script>$("#non_tunai").show();</script>';
} else {
	echo '<script>$("#non_tunai").hide();</script>';
}
?>

<script>
var site = "<?=site_url()?>";
$(function(){
	$('.autocomplete_data').autocomplete({
		serviceUrl: site+'pembelian/search_nota_beli'
	});	
});

function trx_number() {
	var tanggal = $("#tanggal").val();
	//var get_lokasi = $("#lokasi").val();
	var lokasi = $("#lokasi").val(); //get_lokasi.split("|");
	
	if (tanggal != '' && lokasi != ''){
		$.ajax({
			url: "<?php echo base_url().'site/max_kode/' ?>" + btoa("BH") + "/" + btoa(tanggal) + "/" + btoa(lokasi),
			type: "GET",
			success: function (data) {
				$("#nota_sistem").val(data);
			}
		});
	}else {
		$("#nota_sistem").val("");
	}
}

$("#cara_byr").change(function () {
	var jenis_transaksi = $(this).val();

	if (jenis_transaksi == "Cek/Giro") {
		$("#non_tunai").show();
	}else {
		$("#non_tunai").hide();
	}
});

function hitung_pembulatan(){
	var jumlah_hutang = $("#jumlah_hutang").val(); if(jumlah_hutang==''){ jumlah_hutang = 0; } jumlah_hutang = parseFloat(hapuskoma(jumlah_hutang));
	var jumlah_bayar = $("#jumlah_bayar").val(); if(jumlah_bayar==''){ jumlah_bayar = 0; } jumlah_bayar = parseFloat(hapuskoma(jumlah_bayar));
	var bulat = jumlah_bayar - jumlah_hutang;
	
	if ($("#cek_pembulatan").is(":checked") || jumlah_bayar > jumlah_hutang) {
		$("#cek_pembulatan").prop("checked", true)
		//$("#pembulatan").val(to_rp(bulat));
		$("#pembulatan").val(bulat);
	} else {
		$("#pembulatan").val(0);
	}
	
	if(jumlah_bayar > jumlah_hutang){ $("#alr_jumlah_bayar").text("Jumlah Pembayaran melebihi Jumlah Hutang! Transaksi tetap bisa dilanjut."); } else { $("#alr_jumlah_bayar").text(""); }
}

function submit_transaksi(btn){
	$("#"+btn).click();
}
</script>
