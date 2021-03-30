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
							<?=form_open_multipart($this->control.'/'.$page.$update, array('id'=>'form_barang', 'class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
							
							<?php $caption_setting = $this->m_crud->get_data('Setting', 'as_group1, as_group2', "Kode = '1111'"); ?>
							<?php $caption_harga = $this->m_crud->get_data('harga', 'hrg1, hrg2, hrg3, hrg4', "Kode = '1111'"); ?>
							<div class="col-lg-4">
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Kode Barang</label>
									<div class="col-lg-8">
										<?php $field = 'kd_brg'; ?>
										<input class="form-control" type="text" maxlength="21" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Kode barang sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" <?= isset($_GET['trx'])?'readonly':null; ?> />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        <b class="error" id="ntf_<?=$field?>"></b>
                                        <input type="hidden" id="param_<?=$field?>" value="0">
                                        <input type="hidden" id="temp_<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Barcode</label>
									<div class="col-lg-8">
										<?php $field = 'barcode'; ?>
										<input class="form-control" type="text" maxlength="21" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Barcode sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        <b class="error" id="ntf_<?=$field?>"></b>
                                        <input type="hidden" id="param_<?=$field?>" value="0">
                                        <input type="hidden" id="temp_<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Nama Barang</label>
									<div class="col-lg-8">
										<?php $field = 'nm_brg'; ?>
										<input class="form-control" type="text" maxlength="20" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4"><?=$menu_group['as_deskripsi']?></label>
									<div class="col-lg-8">
										<?php $field = 'Deskripsi'; ?>
										<input class="form-control" type="text" maxlength="30" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', '<?=$menu_group['as_deskripsi']?> sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        <b class="error" id="ntf_<?=$field?>"></b>
                                        <input type="hidden" id="param_<?=$field?>" value="0">
                                        <input type="hidden" id="temp_<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-4">Kelompok</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'kel_brg';
                                        $option = null; $option[''] = '-- Kelompok --';
                                        //$option['all'] = 'All';
                                        $data_option = $this->m_crud->read_data('kel_brg', 'kel_brg, nm_kel_brg', null, 'nm_kel_brg asc');
                                        foreach($data_option as $row){ $option[$row['kel_brg']] = $row['kel_brg']." | ".$row['nm_kel_brg']; }
                                        echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'onchange'=>'cek_group2()', 'id'=>$field, 'required'=>'required'));
                                        ?><?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4"><?=$caption_setting['as_group1']?></label>
									<div class="col-lg-8">
										<?php $field = 'Group1';
										$option = null; $option[''] = '-- '.$caption_setting['as_group1'].' --';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Group1', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Kode']." | ".$row['Nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
										?>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4"><?=$caption_setting['as_group2']?></label>
									<div class="col-lg-8">
										<?php $field = 'Group2';
										$option = null; $option[''] = '-- '.$caption_setting['as_group2'].' --';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Kode']." | ".$row['Nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
										?>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Satuan</label>
									<div class="col-lg-8">
										<?php $field = 'satuan'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Harga Beli</label>
									<div class="col-lg-8">
										<?php $field = 'hrg_beli'; $field_beli = 'hrg_beli'?>
										<input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Stock Min</label>
									<div class="col-lg-8">
										<?php $field = 'stock_min'; ?>
										<input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" required aria-required="true" />
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-4">Jenis Barang</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'Jenis';
										$option = null;
										$option['Barang Dijual'] = 'Barang Dijual';
										$option['Barang Tidak Dijual'] = 'Barang Tidak Dijual';
										//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
										//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
										?>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px;">
                                    <label class="col-sm-4 control-label text-left">Gambar</label>
                                    <div class="col-sm-8">
                                        <?php if(isset($master_data['gambar']) && $master_data['gambar']!=null && $master_data['gambar']!='-'){ ?>
                                            <input type="hidden" name="logo_gambar" value="<?=$master_data['gambar']?>">
                                            <img width="200" src="<?=$this->config->item('url').$master_data['gambar']?>" />
                                        <?php } ?>
                                        <input type="file" name="gambar" id="gambar" />
                                        <font color='red'><?php if(isset($error_logo)){ echo $error_logo; } ?></font>
                                    </div>
                                </div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-4">Kode Packing</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'kd_packing'; ?>
                                        <input class="form-control" type="text" maxlength="15" onchange="cek_data('<?=$table?>','<?=$field?>', 'warning', 'Kode sudah digunakan!')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>"/>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                        <b class="error" id="ntf_<?=$field?>"></b>
                                        <?php
                                        if (isset($master_data[$field]) && $master_data[$field] == '' || $master_data[$field] == null) {
                                            ?>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="generate_kode()">Buat Kode
                                            </button>
                                            <?php
                                        }
                                        ?>
                                        <input type="hidden" id="param_<?=$field?>" value="0">
                                    </div>
                                </div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-4">Qty Packing</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'qty_packing'; ?>
                                        <input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>"/>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
								<!--<input type="hidden" name="kategori" id="kategori" value="Non Paket">-->
								<!--<input type="hidden" name="Jenis" id="Jenis" value="Barang Dijual">-->
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-4">Kategori</label>
									<div class="col-lg-8">
										<?php $field = 'kategori';
										$option = null;
										/*$option[''] = '-- Kategori --';*/
                                        $option['Non Paket'] = 'Non Paket';
                                        $option['Paket'] = 'Paket';
                                        //$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
										//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
										?>
										<p style="font-size:10px; color:red;">Kategori Paket tidak akan mengurangi stock, sedangkan Non Paket akan mengurangi stock</p>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-4">Barang Online</label>
                                    <div class="col-lg-8">
                                        <?php $field = 'barang_online'; ?>
										<div class="checkbox checkbox-primary">
											<input class="form-control" type="checkbox" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)=='1')?'checked':((isset($master_data[$field])&&$master_data[$field]=='1')?'checked':null)?> />	
											<label for="<?=$field?>"></label>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
							</div>
							<div class="col-lg-8">
                                <ul class="nav nav-tabs tabs">
                                    <li class="active tab">
                                        <a href="#lokasi-harga" data-toggle="tab" aria-expanded="false"> 
                                            <span class="visible-xs">Harga Lokasi</span>
                                            <span class="hidden-xs">Harga Lokasi</span>
                                        </a> 
                                    </li>
                                    <li class="tab">
                                        <a href="#harga-bertingkat" data-toggle="tab" aria-expanded="false">
                                            <span class="visible-xs">Harga Bertingkat</span>
                                            <span class="hidden-xs">Harga Bertingkat</span>
                                        </a>
                                    </li>
                                </ul> 
                                <div class="tab-content" style="max-height: 480px; overflow: auto; overflow-x: hidden;"> 
									<div class="tab-pane active" id="lokasi-harga"> 
										<div class="form-group " style="margin-bottom:5px;">
											<label class="control-label col-lg-3"><p style="text-align:center;">Lokasi</p></label>
											<div class="col-lg-9">
												<div class="col-lg-4">
													<label class="control-label"><p style="text-align:center;">Harga Jual</p></label>
												</div>
                                                <div class="col-lg-8">
                                                    <div class="col-md-4">
                                                        <label class="control-label"><p style="text-align:center;">Margin %</p></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="control-label"><p style="text-align:center;">Diskon</p></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="control-label"><p style="text-align:center;">PPN</p></label>
                                                    </div>
                                                </div>
											</div>
										</div>
										<div class="form-group " style="margin-bottom:5px;">
											<label class="control-label col-lg-2"><p style="color:red;">Atur Semua</p></label>
											<div class="col-lg-1">
												<?php $field = 'cek_lokasi'; ?>
												<div class="checkbox checkbox-primary">
													<input class="form-control" type="checkbox" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>" />
													<label for="<?=$field?>"></label>
												</div>
												<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
											</div>
											<div class="col-lg-9">
												<div class="col-lg-4">
													<?php $field = 'hrg_jual_1'; ?>
													<input class="form-control" type="number" step="any" onclick="$(this).select()" onkeyup="hitung_margin('<?=$field?>', 'margin', 'hrg_jual')" onchange="hitung_margin('<?=$field?>', 'margin', 'hrg_jual')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>" />
													<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
												</div>
                                                <div class="col-lg-8">
                                                    <div class="col-md-4">
                                                        <?php $field = 'margin'; ?>
                                                        <input class="form-control" type="number" step="any" onclick="$(this).select()" onkeyup="hitung_margin('<?=$field?>', 'hrg_jual_1', 'margin')" onchange="hitung_margin('<?=$field?>', 'hrg_jual_1', 'margin')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>" />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $field = 'disc1'; ?>
                                                        <input class="form-control" type="number" step="any" onclick="$(this).select()" onkeyup="atur_sama('<?=$field?>')" onchange="atur_sama('<?=$field?>')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>" />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $field = 'ppn'; ?>
                                                        <input class="form-control" type="number" step="any" onclick="$(this).select()" onkeyup="atur_sama('<?=$field?>')" onchange="atur_sama('<?=$field?>')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>" />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                </div>
											</div>
										</div>
                                        <div class="form-group " style="margin-bottom:5px;">
                                            <label class="control-label col-lg-2"><p>Master</p></label>
                                            <div class="col-lg-1">
                                                <?php $field = 'master_harga'; ?>
                                                <div class="checkbox checkbox-primary">
                                                    <input class="form-control" type="checkbox" checked disabled id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):null?>" />
                                                    <label for="<?=$field?>"></label>
                                                </div>
                                                <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                            </div>
                                            <div class="col-lg-9">
                                                <div class="col-lg-4">
                                                    <?php $field = 'master_hrg_jual_1'; ?>
                                                    <input class="form-control" type="number" step="any" onclick="$(this).select()" onkeyup="hitung_margin('<?=$field?>', 'master_margin', 'hrg_jual')" onchange="atur_sama('<?=$field?>'); hitung_margin('<?=$field?>', 'master_margin', 'hrg_jual')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data['hrg_jual_1'])?($master_data['hrg_jual_1']+0):null)?>" />
                                                    <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="col-md-4">
                                                        <?php $field = 'master_margin'; ?>
                                                        <input class="form-control" type="number" step="any" onclick="$(this).select()" onkeyup="hitung_margin('<?=$field?>', 'master_hrg_jual_1', 'margin')" onchange="hitung_margin('<?=$field?>', 'master_hrg_jual_1', 'margin')" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):((isset($master_data['hrg_jual_1']) && $master_data['hrg_jual_1'] != 0 && isset($master_data['hrg_beli']) && $master_data['hrg_beli'] != 0)?round((1-($master_data['hrg_beli']/$master_data['hrg_jual_1']))*100, 2):0)?>" />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $field = 'master_disc1'; ?>
                                                        <input class="form-control" type="number" step="any" onclick="$(this).select()" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data['diskon'])?($master_data['diskon']+0):null)?>" />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $field = 'master_ppn'; ?>
                                                        <input class="form-control" type="number" step="any" onclick="$(this).select()" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data['PPN'])?($master_data['PPN']+0):null)?>" />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
										<?php $lokasi = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc'); ?>
										<?php $no = 0; foreach($lokasi as $rows){ $no++; ?>
											<?php if(isset($_GET['trx'])){ 
												foreach($master_data_detail as $row){
													if($row['lokasi']==$rows['Kode']){
														$master_data['id_barang_hrg'.$no] = $row['id_barang_hrg'];
														$master_data['cek_lokasi'.$no] = $row['lokasi'];
														$master_data['margin'.$no] = $row['margin'];
														$master_data['hrg_jual_1'.$no] = $row['hrg_jual_1'];
														$master_data['disc1'.$no] = $row['disc1'];
														$master_data['ppn'.$no] = $row['ppn'];
													}
												} 
											} ?>
										<div class="form-group " style="margin-bottom:5px;">
											<label class="control-label col-lg-2"><?=$rows['Nama']?></label>
											<div class="col-lg-1">
												<?php $field = 'cek_lokasi'.$no; ?>
												<div class="checkbox checkbox-primary">
													<input class="form-control cek_lokasi" type="checkbox" onclick="checked_lokasi(<?=$no?>)" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)==$rows['Kode'])?'checked':((isset($master_data[$field])&&$master_data[$field]==$rows['Kode'])?'checked':null)?> />	
													<label for="<?=$field?>"></label>
												</div>
												<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
											</div>
											<div class="col-lg-9">
												<div class="col-lg-4">
													<?php $field = 'hrg_jual_1'.$no; $field_jual = 'hrg_jual_1'.$no; ?>
													<input class="form-control" type="number" onclick="$(this).select()" onkeyup="hitung_margin('<?=$field?>', 'margin<?=$no?>', 'hrg_jual', 1)" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" <?=(set_value('cek_lokasi'.$no)==$rows['Kode'])?null:((isset($master_data['cek_lokasi'.$no])&&$master_data['cek_lokasi'.$no]==$rows['Kode'])?null:'readonly')?> />
													<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
												</div>
                                                <div class="col-lg-8">
                                                    <div class="col-md-4">
                                                        <?php $field = 'margin'.$no; ?>
                                                        <input class="form-control" type="number" onclick="$(this).select()" onkeyup="hitung_margin('<?=$field?>', 'hrg_jual_1<?=$no?>', 'margin', 1)" step="any" min="0" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field_jual)?set_value($field_jual):((isset($master_data[$field_jual]) && $master_data[$field_jual] != 0 && isset($master_data[$field_beli]) && $master_data[$field_beli] != 0)?round((1-($master_data['hrg_beli']/$master_data[$field_jual]))*100, 2):0)?>" <?=(set_value('cek_lokasi'.$no)==$rows['Kode'])?null:((isset($master_data['cek_lokasi'.$no])&&$master_data['cek_lokasi'.$no]==$rows['Kode'])?null:'readonly')?>/>
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $field = 'disc1'.$no; ?>
                                                        <input class="form-control" type="number" onclick="$(this).select()" step="any" min="0" max="100" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" <?=(set_value('cek_lokasi'.$no)==$rows['Kode'])?null:((isset($master_data['cek_lokasi'.$no])&&$master_data['cek_lokasi'.$no]==$rows['Kode'])?null:'readonly')?> />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <?php $field = 'ppn'.$no; ?>
                                                        <input class="form-control" type="number" onclick="$(this).select()" step="any" min="0" max="100" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?($master_data[$field]+0):null)?>" <?=(set_value('cek_lokasi'.$no)==$rows['Kode'])?null:((isset($master_data['cek_lokasi'.$no])&&$master_data['cek_lokasi'.$no]==$rows['Kode'])?null:'readonly')?> />
                                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                                    </div>
                                                </div>
											</div>
										</div>
										<?php $field = 'lokasi'.$no; ?>
										<input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$rows['Kode']?>" />
										<?php $field = 'id_barang_hrg'.$no; ?>
										<input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=(isset($master_data[$field])?$master_data[$field]:null)?>" />
										<?php } ?>
										<?php $field = 'jumlah_lokasi'; ?>
										<input type="hidden" id="<?=$field?>" name="<?=$field?>" value="<?=$no?>" />
									</div>
                                    <div class="tab-pane" id="harga-bertingkat">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Aksi</th>
                                                <th>Qty 1</th>
                                                <th>Qty 2</th>
                                                <th>Diskon %</th>
                                                <th>Harga</th>
                                            </tr>
                                            </thead>
                                            <tbody id="list_harga">
                                            </tbody>
                                            <input type="hidden" id="max_data" name="max_data" value="0">
                                        </table>
                                    </div>
                                </div> 
                            </div> 
							<div class="col-lg-12">
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
									</div>
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

<script>
    function generate_kode() {
        var kode1 = ('<?=date('y')?>');
        var kode2 = '<?=date('md')?>';
        var random = Math.floor(100000 + Math.random() * 900000);

        var kode = '2'+kode1+kode2+random;
        document.getElementById("kd_packing").value = kode;
    }

    var array_harga = [];
    function add_list(id) {
        var new_list = '';
        var qty_before = '';
        var max_data = parseInt(document.getElementById("max_data").value);

        if (id != '-' && id != 'x') {
            var q1 = $("#q1_"+id).val();
            var q2 = $("#q2_"+id).val();
            var disc = $("#disc_"+id).val();
            var hrg = $("#hrg_"+id).val();

            if (q1 != '' && q2 != '' && hrg != '' && disc != '') {
                var data = {qty_1: q1, qty_2: q2, harga: hrg, diskon: disc};
                array_harga.push(data);

                for (var x = 0; x < array_harga.length; x++) {
                    new_list += '<tr>' +
                        '<td><button type="button" id="add_harga_' + x + '" onclick="add_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_harga_' + x + '" onclick="remove_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                        '<td><input type="number" value="' + array_harga[x].qty_1 + '" class="form-control" id="q1_' + x + '" name="q1_' + x + '"></td>' +
                        '<td><input type="number" value="' + array_harga[x].qty_2 + '" class="form-control" id="q2_' + x + '" name="q2_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_harga[x].diskon + '" class="form-control" id="disc_' + x + '" name="disc_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_harga[x].harga + '" class="form-control" id="hrg_' + x + '" name="hrg_' + x + '"></td>' +
                        '</tr>';

                    max_data = x + 1;
                }

                qty_before = parseInt(array_harga[x - 1].qty_2) + 1;
            } else {
                return false;
            }
        } else {
            if ('<?=$this->uri->segment(3)?>' == 'edit' && id != 'x') {
                for (var x = 0; x < array_harga.length; x++) {
                    new_list += '<tr>' +
                        '<td><button type="button" id="add_harga_' + x + '" onclick="add_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_harga_' + x + '" onclick="remove_list(' + x + ')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                        '<td><input type="number" value="' + array_harga[x].qty_1 + '" class="form-control" id="q1_' + x + '" name="q1_' + x + '"></td>' +
                        '<td><input type="number" value="' + array_harga[x].qty_2 + '" class="form-control" id="q2_' + x + '" name="q2_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_harga[x].diskon + '" class="form-control" id="disc_' + x + '" name="disc_' + x + '"></td>' +
                        '<td><input type="text" value="' + array_harga[x].harga + '" class="form-control" id="hrg_' + x + '" name="hrg_' + x + '"></td>' +
                        '</tr>';

                    max_data = x + 1;
                }

                qty_before = parseInt(array_harga[x - 1].qty_2) + 1;
            }
        }

        new_list += '<tr>' +
            '<td><button type="button" id="add_harga_'+(max_data)+'" onclick="add_list('+(max_data)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_harga_'+(max_data)+'" onclick="remove_list('+(max_data)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
            '<td><input type="number" value="'+qty_before+'" onkeyup="valid_qty('+(max_data)+')" class="form-control" id="q1_'+(max_data)+'" name="q1_'+(max_data)+'"></td>' +
            '<td><input type="number" class="form-control" onkeyup="valid_qty('+(max_data)+')" id="q2_'+(max_data)+'" name="q2_'+(max_data)+'"><b class="error" id="ntf_sampai"></b></td>' +
            '<td><input type="text" onkeydown="return isNumber(event)" onkeyup="valid_diskon(\''+(max_data)+'\')" class="form-control" id="disc_'+(max_data)+'" name="disc_'+(max_data)+'"></td>' +
            '<td><input type="text" onkeydown="return isNumber(event)" onkeyup="isMoney(\'hrg_'+(max_data)+'\', \'+\'); valid_harga(\''+(max_data)+'\')" class="form-control" id="hrg_'+(max_data)+'" name="hrg_'+(max_data)+'"></td>' +
            '</tr>';

        document.getElementById("max_data").value = max_data;
        document.getElementById("list_harga").innerHTML = new_list;

        disable_form(max_data);
    }

    function remove_list(id) {
        var new_list = '';
        var max_data = 0;

        array_harga.splice(id, 1);

        for (var x = 0; x < array_harga.length; x++) {
            new_list += '<tr>' +
                '<td><button type="button" id="add_harga_'+x+'" onclick="add_list('+x+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_harga_'+x+'" onclick="remove_list('+x+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
                '<td><input type="number" value="'+array_harga[x].qty_1+'" class="form-control" id="q1_'+x+'" name="q1_'+x+'"></td>' +
                '<td><input type="number" value="'+array_harga[x].qty_2+'" class="form-control" id="q2_'+x+'" name="q2_'+x+'"></td>' +
                '<td><input type="text" value="' + array_harga[x].diskon + '" class="form-control" id="disc_' + x + '" name="disc_' + x + '"></td>' +
                '<td><input type="text" value="'+array_harga[x].harga+'" class="form-control" id="hrg_'+x+'" name="hrg_'+x+'"></td>' +
                '</tr>';

            max_data = x+1;
        }

        if (x > 0) {
            qty_before = parseInt(array_harga[x - 1].qty_2) + 1;
        } else {
            qty_before = '';
        }

        new_list += '<tr>' +
            '<td><button type="button" id="add_harga_'+(max_data)+'" onclick="add_list('+(max_data)+')" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-plus"></i></button><button type="button" id="remove_harga_'+(max_data)+'" onclick="remove_list()" class="btn btn-primary waves-effect waves-light"> <i class="fa fa-close"></i></button></td>' +
            '<td><input type="number" value="'+qty_before+'" class="form-control" onkeyup="valid_qty('+(max_data)+')" id="q1_'+(max_data)+'" name="q1_'+(max_data)+'"></td>' +
            '<td><input type="number" class="form-control" onkeyup="valid_qty('+(max_data)+')" id="q2_'+(max_data)+'" name="q2_'+(max_data)+'"><b class="error" id="ntf_sampai"></b></td>' +
            '<td><input type="text" onkeydown="return isNumber(event)" onkeyup="valid_diskon(\''+(max_data)+'\')" class="form-control" id="disc_'+(max_data)+'" name="disc_'+(max_data)+'"></td>' +
            '<td><input type="text" onkeydown="return isNumber(event)" onkeyup="isMoney(\'hrg_'+(max_data)+'\', \'+\'); valid_harga(\''+(max_data)+'\')" class="form-control" id="hrg_'+(max_data)+'" name="hrg_'+(max_data)+'"></td>' +
            '</tr>';

        document.getElementById("max_data").value = max_data;
        document.getElementById("list_harga").innerHTML = new_list;

        disable_form(max_data);
    }

    function valid_harga(id) {
        var hrg_diskon = parseFloat(hapuskoma($("#hrg_"+id).val()));
        var hrg_jual = parseFloat($("#master_hrg_jual_1").val());

        if (isNaN(hrg_diskon) || hrg_diskon < 0 || isNaN(hrg_jual) || hrg_jual <= 0) {
            $("#disc_"+id).val(100);
            $("#hrg_"+id).val(0);
        } else {
            if (hrg_diskon > hrg_jual) {
                $("#disc_"+id).val(0);
                $("#hrg_"+id).val(to_rp(hrg_jual, '-'));
            } else {
                $("#disc_" + id).val(parseFloat(100 - diskon_persen(hrg_jual, hrg_diskon)));
                $("#hrg_" + id).val(to_rp(hrg_diskon, '-'));
            }
        }
    }

    function valid_diskon(id) {
        var diskon = parseFloat($("#disc_"+id).val());
        var hrg_jual = parseFloat($("#master_hrg_jual_1").val());

        if (isNaN(diskon) || diskon < 0 || isNaN(hrg_jual) || hrg_jual <= 0) {
            $("#disc_"+id).val(0);
            $("#hrg_"+id).val(hrg_jual);
        } else {
            if (diskon > 100) {
                $("#disc_"+id).val(100);
                $("#hrg_"+id).val(0);
            } else {
                $("#disc_"+id).val(parseFloat(diskon));
                $("#hrg_"+id).val(to_rp(hitung_diskon(hrg_jual, diskon), '-'));
            }
        }
    }

    function valid_qty(id) {
        var dari = parseInt($("#q1_"+id).val());
        var sampai = parseInt($("#q2_"+id).val());

        if (sampai < dari) {
            $("#ntf_sampai").text("Qty 2 harus lebih besar dari Qty 1!");
            $("#save").prop('disabled', true);
            $("#add_harga_"+id).prop('disabled', true);
        } else if (isNaN(sampai)) {
            $("#ntf_sampai").text("Qty 2 harus lebih dari 0!");
            $("#save").prop('disabled', true);
            $("#add_harga_"+id).prop('disabled', true);
        } else {
            hide_notif('ntf_sampai');
            $("#save").prop('disabled', false);
            $("#add_harga_"+id).prop('disabled', false);
        }
    }

    function disable_form(id) {
        var x = 0;
        var y = 0;
        for (x; x<id; x++) {
            $("#remove_harga_"+x).hide();
            $("#add_harga_"+x).hide();
            $("#q1_"+x).prop('readonly', true);
            $("#q2_"+x).prop('readonly', true);
            $("#disc_"+x).prop('readonly', true);
            $("#hrg_"+x).prop('readonly', true);
            y = x;
        }
        if (id != 0) {
            $("#q1_"+id).prop('readonly', true);
        } else {
            $("#q1_"+id).prop('readonly', false);
        }

        $("#remove_harga_"+y).show();
        $("#remove_harga_"+id).hide();
    }

    $(document).ready(function () {
        if ('<?=$this->uri->segment(3)?>' == 'edit') {
            var hrg_jual = parseFloat($("#master_hrg_jual_1").val());
            $.ajax({
                url: "<?=base_url().'master_data/get_harga_bertingkat/'.$_GET['trx']?>",
                type: "GET",
                dataType: "JSON",
                success: function (res) {
                    if (res.status>0) {
                        for (var i = 0; i<res.list.length; i++) {
                            var data = {qty_1: parseInt(res.list[i]['dari']), qty_2: parseInt(res.list[i]['sampai']), diskon: 100-diskon_persen(hrg_jual, res.list[i]['harga']), harga: to_rp(res.list[i]['harga'], '-')};
                            array_harga.push(data);
                        }
                        add_list('-');
                    } else {
                        add_list('x');
                    }
                }
            });
        } else {
            add_list('-');
        }
    }).on("keypress", ":input:not(textarea)", function(event) {
        return event.keyCode != 13;
    });

    function cek_simpan() {
        var kd_brg = parseInt($("#param_kd_brg").val());
        var barcode = parseInt($("#param_barcode").val());
        var deskripsi = parseInt($("#param_Deskripsi").val());

        if ((kd_brg + barcode + deskripsi) != 0) {
            $("#save").prop("disabled", true);
        } else {
            $("#save").prop("disabled", false);
        }
    }

function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	var temp = $("#temp_"+column).val();
	if(id != ''){
	    if (id != temp.toLowerCase() && id != temp.toUpperCase()) {
            $.ajax({
                type: 'GET',
                url: '<?=site_url()?>site/cek_data_2/' + btoa(table) + '/' + btoa(column) + '/' + btoa(id),
                //data: {delete_id : id},
                success: function (data) {
                    if (data == 1) {
                        $("#param_" + column).val(1);
                        $("#ntf_" + column).text(pesan);
                        cek_simpan();
                        //if(tipe=='error'){ alert('error'); }
                        //else if(tipe=='warning'){ alert('warning'); }
                    } else {
                        $("#param_" + column).val(0);
                        $("#ntf_" + column).text('');
                        cek_simpan();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Check Data Failed');
                }
            });
        } else {
            $("#param_" + column).val(0);
            $("#ntf_" + column).text('');
            cek_simpan();
        }
	}
}

$("#kel_brg").change(function () {
    var kode = $(this).val();
    if (kode != "") {
        $.ajax({
            url: "<?=base_url()?>site/max_kode_barang/" + btoa(kode),
            type: "GET",
            success: function (res) {
                $("#kd_brg").val(res);
            }
        });
    } else {
        $("#kd_brg").val("");
    }
});

/*$("#Group2").change(function () {
    var kode = $(this).val();
    $.ajax({
        url: "<=base_url()?>site/get_list_dropdown/" + btoa("kel_brg") + "/" + btoa("kel_brg id, nm_kel_brg name") + "/" + btoa("Group2") + "/" + btoa(kode) + "/" + btoa("-- Kelompok --"),
        type: "GET",
        success: function (res) {
            $("#kel_brg").html(res).val("").change();
        }
    });
});*/

function barcode_kd_brg(){
	$('#barcode').val($('#kd_brg').val());
}

function cek_group2(){
	var id = $('#kel_brg').val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/get_data/kel_brg/Group2/kel_brg/' + id,
			//data: {delete_id : id},
			success: function (data) { 
				$("#Group2").select2("val", data);
			}, 
			error: function (jqXHR, textStatus, errorThrown){ alert('Get Data Failed'); }
		});
	}
}

function hitung_margin(field1, field2, tipe, id = 0) {
	var harga_beli = parseFloat($("#hrg_beli").val());
	var hasil = '';

	if (!isNaN(harga_beli)) {
		if (tipe == 'hrg_jual') {
			if (harga_beli <= parseFloat($('#' + field1).val())) {
				hasil = ((1 - (harga_beli/parseFloat($('#' + field1).val()))) * 100).toFixed(2);
			}
		} else {
			if ($('#' + field1).val() > 100) {
				$('#' + field1).val(99)
			}
			if ($('#' + field1).val() < 0) {
				$('#' + field1).val(0)
			}

			hasil = (harga_beli / (1 - (parseFloat($('#' + field1).val()) / 100))).toFixed(2);
		}

		$('#' + field2).val(hasil);

		if (id == 0) {
            atur_sama(field1);
			atur_sama(field2);
		}
	} else {
		if (tipe == 'hrg_jual') {
            $('#' + field1).val(0);
			$('#' + field2).val(0);
            atur_sama(field1);
            atur_sama(field2);
		} else {
            $('#' + field1).val(0);
            $('#' + field2).val(0);
            atur_sama(field1);
            atur_sama(field2);
		}
	}
}

function atur_sama(field){
	for(var i=1; i<=$('#jumlah_lokasi').val(); i++){
		if ($("#cek_lokasi"+i).is(":checked")) {
			$('#'+field+i).val($('#'+field).val());
		} 
	}
    $('#master_'+field).val($('#'+field).val());
}

function checked_lokasi(x=0){
	if(x==0){
		var awal = 1; var akhir = $('#jumlah_lokasi').val();
	} else {
		var awal = x; var akhir = x;
	}
	
	for(var i=awal; i<=akhir; i++){
		if ($("#cek_lokasi"+i).is(":checked")) {
			$('#hrg_jual_1' + i).prop('readonly', false);
			$('#hrg_jual_2' + i).prop('readonly', false);
			$('#hrg_jual_3' + i).prop('readonly', false);
			$('#hrg_jual_4' + i).prop('readonly', false);
			$('#margin' + i).prop('readonly', false);
			$('#disc1' + i).prop('readonly', false);
			$('#ppn' + i).prop('readonly', false);
		} else { 
			$('#hrg_jual_1' + i).prop('readonly', true);
			$('#hrg_jual_2' + i).prop('readonly', true);
			$('#hrg_jual_3' + i).prop('readonly', true);
			$('#hrg_jual_4' + i).prop('readonly', true);
			$('#margin' + i).prop('readonly', true);
			$('#disc1' + i).prop('readonly', true);
			$('#ppn' + i).prop('readonly', true);
			$('#hrg_jual_1' + i).val('');
			$('#hrg_jual_2' + i).val('');
			$('#hrg_jual_3' + i).val('');
			$('#hrg_jual_4' + i).val('');
			$('#margin' + i).val('');
			$('#disc1' + i).val('');
			$('#ppn' + i).val('');
		} 
	}
}

$("#cek_lokasi").click(function () {
	if ($("#cek_lokasi").is(":checked")) {
		$(".cek_lokasi").prop('checked', true);
	} else {
		$(".cek_lokasi").prop('checked', false);
	}
	checked_lokasi();
});
</script>

