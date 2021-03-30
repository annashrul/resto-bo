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
							<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
							<?=form_open($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
								
								<div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-1">User Level</label>
                                    <div class="col-lg-11">
										<?php $field = 'lvl'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<?php $menu_group = $this->m_crud->get_data('Setting', 'as_group1, as_group2', "Kode = '1111'")?>
								<?php $access_menu = array(
									'Pengaturan'=>array( //0-10
										0=>'Perusahaan',
										//1=>'Device',
										2=>'Poin',
                                        3=>'Intro',
                                        4=>'Slider',
                                        5=>'Lokasi Pengirim',
                                        6=>'Deposit'
									), 'Master Data'=>array( //11-30
										11=>'User Level',
										12=>'User List',
										13=>'Kategori Lokasi',
                                        287=>'Data Fasilitas',
										14=>'Data Lokasi',
										15=>$menu_group['as_group2'],
										16=>$menu_group['as_group1'],
										17=>'Kelompok Barang',
										18=>'Data Barang',
										19=>'Barang Harga',
										285=>'Kelompok Barang Online',
										286=>'Data Barang Online',
										288=>'Area',
										289=>'Meja',
										//284=>'Barang Limit Stock',
										//28=>'Harga Bertingkat',
										29=>'Data Paket',
										20=>'Data Bank',
										21=>'Data Kas',
										22=>'Data Promo',
										//23=>'Konversi',
										24=>'Tipe Customer',
										25=>'Data Customer',
										26=>'Data Sales/SPG',
										27=>'Data Supplier',
										30=>'Kitchen Printer',
                                        //281-300
                                        281=>'Data Compliment',
                                        282=>'Data Berita',
                                        283=>'Data Kurir',
									), 'Inventory'=>array( //31-50
										//37=>'Delivery Note',
										31=>'Alokasi',
										32=>'Adjusment',
										//33=>'Packing',
										//34=>'Approval Mutasi',
                                        //35=>'Approve Order',
                                        //36=>'Expedisi'
									), 'Pembelian'=>array( //51-70
										//55=>'PO Cabang',
										//54=>'PO Pusat',
										//56=>'PO Mingguan',
										51=>'Purchase Order',
										52=>'Pembelian Barang',
										53=>'Retur Tanpa Nota'
									), 'Penjualan'=>array( //191-200
                                        191=>'Penjualan Barang',
                                        192=>'Pesanan Online',
                                        193=>'Request Deposit'
                                    ), 'Hutang'=>array( //241-250
                                        241=>'Bayar Hutang',
                                        //242=>'Kontra Bon',
                                        //243=>'Bayar Kontra Bon'
                                    ), 'Piutang'=>array( //251-260
                                        251=>'Bayar Piutang'
                                    )/*, 'Retur Cabang'=>array( //71-90
                                        72=>'Retur Cabang',
										71=>'Penerimaan Retur Cabang'
									)*/, 'Laporan'=>array( //91-280
										//Konsinyasi 91-110
										//91=>'Persediaan Konsinyasi',
										//92=>'Penjualan Konsinyasi',
										//Inventory 111-130
										//121=>'Delivery Note',
										111=>'Alokasi',
                                        //113=>'Alokasi By Pembelian',
                                        //116=>'Branch Mutasi',
                                        112=>'Stock',
                                        114=>'Stock Opname',
                                        117=>'Adjusment',
                                        //115=>'Packing',
										//118=>'Order',
										//119=>'Receive Order',
										//120=>'Expedisi',
										//Pembelian 131-150
										//143=>'PO Cabang',
										//141=>'PO Pusat',
										131=>'Purchase Order',
										132=>'Arsip Pembelian',
										133=>'Arsip Retur Pembelian',
										134=>'Pembelian By Barang',
										135=>'Pembelian By Supplier',
										136=>'Pembelian By Kel. Barang',
										142=>'Pembelian By Operator',
										//137=>'Budget Supplier',
                                        138=>'Laporan Bayar Hutang',
                                        //139=>'Laporan Kontra Bon',
                                        //140=>'Laporan Bayar Kontra Bon',
										//Penjualan 151-170
										151=>'Arsip Penjualan',
										165=>'Arsip Penjualan Online',
										//152=>'Penjualan By '.$menu_group['as_group1'],
										153=>'Arsip Return Penjualan',
										154=>'Penjualan By Kel. Barang',
										155=>'Omset Penjualan',
										164=>'Omset Periode',
										156=>'Penjualan By Customer',
										157=>'Penjualan By Kasir',
										158=>'Penjualan By Barang',
										159=>'Penjualan By '.$menu_group['as_group2'],
										160=>'Penjualan By Kassa',
										161=>'Penjualan By Sales',
										162=>'Penjualan By EDC',
										163=>'Laporan Bayar Piutang',
										//Retur Cabang 171-180
										//171=>'Arsip Retur Cabang',
										//log 271-280
										271=>'Log Otorisasi',
										272=>'Log Transaksi',
                                        273=>'Log Aktivitas',
                                        //Akunting 201-240
                                        //201=>'Laba Rugi',
                                        202=>'Kas Masuk',
                                        203=>'Kas Keluar',
                                        204=>'Deposit Member',
                                        205=>'Laporan PPOB',
                                        206=>'Feedback Member',
                                        207=>'Contact Us'
									)/*, 'Utility'=>array( //181-190
										181=>'Cetak Barcode',
										184=>'Cetak Barcode Custom',
										182=>'Cetak Packing Barang',
										183=>'Cetak Price Tag'
									)*/, 'Otorisasi'=>array( //261-270
										263=>'Edit Transaksi',
                                        261=>'Hapus Transaksi',
                                        262=>'Print Transaksi',
                                        264=>'Pengolahan Master Data'
                                    )
								); ?>
								<input type="hidden" id="jumlah" name="jumlah" value="300" />

							    <div class="form-group ">
                                    <label class="control-label col-lg-1">Access</label>
									<div class="col-lg-11">
										<?php foreach($access_menu as $row => $value){ ?>
											<div class="col-lg-2">
												<?php $field = $row; ?>
												<div class="checkbox checkbox-primary">
													<input class="form-control" type="checkbox" id="<?=str_replace(' ', '_', $field)?>" name="<?=$field?>" value="1" />	
													<label for="<?=$field?>" style="color:red"> <?=$row?></label>
												</div>
												<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
											</div>
											<?php if(is_array($value)){ ?> 
												<div class="col-lg-12 form-inline">
													<?php foreach($value as $rows => $values){ ?>
														<?php $field = $rows; ?>
														<div class="col-lg-3 checkbox checkbox-primary">
															<input class="form-control <?=str_replace(' ', '_', $row)?>" type="checkbox" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)==1)?'checked':((isset($master_data['access'])&&substr($master_data['access'],$field,1)==1)?'checked':null)?> />	
															<label for="<?=$field?>"> <?=$values?></label>
														</div><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													<?php } ?>
												</div>
											<?php } ?>
											<script>
											$("#<?=str_replace(' ', '_', $row)?>").click(function () {
												if ($("#<?=str_replace(' ', '_', $row)?>").is(":checked")) {
													$(".<?=str_replace(' ', '_', $row)?>").prop('checked', true);
												} else {
													$(".<?=str_replace(' ', '_', $row)?>").prop('checked', false);
												}
											});
											</script>
										<?php } ?>
									</div>
								</div>
								
								<!--<div class="form-group ">
									<div>
										<label class="control-label col-lg-2">Access</label>
										<div class="col-lg-10">
										
											<div class="panel-group panel-group-joined" id="accordion-test" style="margin-bottom:5px;"> 
												<div class="panel panel-default"> 
													<div class="panel-heading" onclick="cek()"> 
														<h4 class="panel-title"> 
															<a data-toggle="collapse" data-parent="#accordion-test" href="#collapseOne">
																<?php $field = 'ac'; ?>
																<div class="checkbox checkbox-primary">
																	<input class="form-control cek_lokasi" type="checkbox" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)==1)?'checked':((isset($master_data[$field])&&substr($master_data[$field],1,1)==1)?'checked':null)?> />	
																	<label for="<?=$field?>" style="color:red">Master Data 1</label>
																</div>
															</a> 
														</h4> 
													</div> 
													<div id="collapseOne" class="panel-collapse collapse in"> 
														<div class="panel-body">
															Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
														</div> 
													</div> 
												</div> 
											</div> 
											<div class="panel-group panel-group-joined" id="accordion-test"> 
												<div class="panel panel-default"> 
													<div class="panel-heading"> 
														<h4 class="panel-title"> 
															<a data-toggle="collapse" data-parent="#accordion-test" href="#collapseTwo">
																Collapsible Group Item #2
															</a> 
														</h4> 
													</div> 
													<div id="collapseTwo" class="panel-collapse collapse in"> 
														<div class="panel-body">
															Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
														</div> 
													</div> 
												</div> 
											</div> 
												
										</div>
									</div>
								</div>-->

								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
									</div>
								</div>
								
							<?=form_close()?>
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

