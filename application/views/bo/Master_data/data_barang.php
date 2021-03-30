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
                            <?= form_open($content); ?>
                            <div class="row"><!--
														<div class="col-sm-3">
															<div class="form-group">
																<label>Search</label>
																<div class="input-group">
																	<div class="input-group-btn">
																		<?php /*$field = 'column';
																		$option = null;
																		$option['kd_brg'] = 'Kode Barang';
																		$option['barcode'] = 'Barcode';
																		$option['nm_brg'] = 'Nama Barang';
																		$option['Deskripsi'] = $menu_group['as_deskripsi'];
																		$option['g1.Nama'] = 'Supplier';
																		$option['g2.Nama'] = 'Sub Dept';
																		//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
																		//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
																		echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
																		*/?>
																	</div>
																	<?php /*$field = 'any'; */?>
																	<input class="form-control" style="height: 40px" type="text" id="<?/*=$field*/?>" name="<?/*=$field*/?>" value="<?/*=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)*/?>" autofocus />
																</div>
																<?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
															</div>
														</div>-->
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Sort</label>
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <?php $field = 'order_by';
                                                $option = null;
                                                $option['kd_brg'] = 'Kode Barang';
                                                $option['barcode'] = 'Barcode';
                                                $option['nm_brg'] = 'Nama Barang';
                                                $option['Deskripsi'] = $menu_group['as_deskripsi'];
                                                $option['kb.nm_kel_brg'] = 'Kelompok';
                                                $option['g1.Nama'] = 'Supplier';
                                                $option['g2.Nama'] = 'Sub Dept';
                                                //$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
                                                //foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                                echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                                ?>
                                            </div>
                                            <?php $field = 'order_sort';
                                            $option = null;
                                            $option['asc'] = 'Ascending';
                                            $option['desc'] = 'Descending';
                                            //$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
                                            //foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                            echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required'));
                                            ?>
                                        </div>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <label>Periode Input</label>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <?php $field = 'periode'; ?>
                                            <div class="checkbox checkbox-primary">
                                                <input id="<?=$field?>" name="<?=$field?>" value="1" type="checkbox" <?=(isset($this->session->search[$field]) && $this->session->search[$field]=='1')?'checked':''?>>
                                                <label for="<?=$field?>" title="Semua Periode" style="font-size: 7.9pt">
                                                    Semua Periode
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php $field = 'field-date';?>
                                            <div id="daterange-right" style="cursor: pointer;">
                                                <input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label>Jenis Barang</label>
                                    <?php $field = 'jns_brg';
                                    $option = null;
                                    $option[''] = 'Semua Barang';
                                    $option['1'] = 'Barang Dijual';
                                    $option['0'] = 'Barang Tidak Dijual';
                                    echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'form-control', 'id'=>$field));
                                    ?>
                                </div>
                                <div class="col-sm-1" style="margin-top:25px;">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
                                </div>
                                <!--<div class="col-sm-1">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
                                </div>-->
                                <div class="col-sm-1" style="margin-top:25px;">
                                    <?=anchor($content.'/add', '<i class="fa fa-plus"></i> Add', array('class'=>'pull-right btn btn-primary'))?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <?php $caption = $this->m_crud->get_data('Setting', 'as_group1, as_group2', "Kode = '1111'"); ?>
                                        <table id="" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>No</th><th>Action</th><th>Kode Barang</th><th>Barcode</th><th>Nama Barang</th><th><?=$menu_group['as_deskripsi']?></th><th>Kelompok</th>
                                                <th><?=$caption['as_group1'];?></th><th><?=$caption['as_group2'];?></th>
                                                <th>Satuan</th><th>Harga Beli</th><th>Stock Min</th><th>Berat</th><th>Kategori</th><th>Jenis Barang</th><th>Barang Online</th><th>Favorit</th><th>Poin</th>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>
                                                    <?php $field = 'kd_brg'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td>
                                                    <?php $field = 'barcode'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td>
                                                    <?php $field = 'nm_brg'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td>
                                                    <?php $field = 'Deskripsi'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td>
                                                    <?php $field = 'kb_nm_kel_brg'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td>
                                                    <?php $field = 'g1_Nama'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td>
                                                    <?php $field = 'g2_Nama'; ?>
                                                    <input class="form-control" style="width: 160px" onclick="$(this).select()" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>">
                                                </td>
                                                <td colspan="8"></td>
                                            </tr>
                                            </thead>
                                            <?= form_close(); ?>
                                            <tbody>
                                            <?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0); foreach($master_data as $row){ $no++; ?>
                                                <tr>
                                                    <td><?=$no?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                                                            <ul class="dropdown-menu" role="menu">

                                                                <?php if($row['kategori']==='Paket'): ?>
                                                                    <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="editBahan('<?=$row["kd_brg"]?>','<?=$row["nm_brg"]?>')"><i class="md md-menu"></i> Edit Bahan</button></div></li>
                                                                <?php endif; ?>

                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</button></div></li>
                                                                <?php
                                                                if ($row['Jenis'] == 'Barang Dijual') {
                                                                    $jenis = 'Barang Tidak Dijual';
                                                                } else {
                                                                    $jenis = 'Barang Dijual';
                                                                }

                                                                if ($row['fav'] == '' || $row['fav'] == '0') {
                                                                    $fav = 'Set Favorit';
                                                                    $fav_val = '1';
                                                                } else {
                                                                    $fav = 'Hapus Favorit';
                                                                    $fav_val = '0';
                                                                }

                                                                if ($row['poin'] == '' || $row['poin'] == '0') {
                                                                    $pts = 'Set Dihitung Poin';
                                                                    $pts_val = '1';
                                                                } else {
                                                                    $pts = 'Set Tidak Dihitung Poin';
                                                                    $pts_val = '0';
                                                                }
                                                                ?>
                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="update('<?=base64_encode(json_encode(array('table'=>$table, 'update'=>array(array('col'=>'Jenis', 'data'=>$jenis)), 'id'=>$row['kd_brg'])))?>')"><i class="fa fa-circle-o"></i> Set <?=$jenis?></button></div></li>
                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="update('<?=base64_encode(json_encode(array('table'=>$table, 'update'=>array(array('col'=>'fav', 'data'=>$fav_val)), 'id'=>$row['kd_brg'])))?>')"><i class="fa fa-circle-o"></i> <?=$fav?></button></div></li>
                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hargaCustomer('<?=$row['kd_brg']?>')"><i class="md md-attach-money"></i> Harga Customer</button></div></li>

                                                                <li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="update('<?=base64_encode(json_encode(array('table'=>$table, 'update'=>array(array('col'=>'poin', 'data'=>$pts_val)), 'id'=>$row['kd_brg'])))?>')"><i class="fa fa-circle-o"></i> <?=$pts?></button></div></li>
                                                                <?php
                                                                if (substr($access->access,264,1)==1) {
                                                                    echo '
																							<li><div class="col-sm-12">'.anchor($content.'/edit/?trx='.base64_encode($row['kd_brg']), '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12')).'</div></li>
																							<li><div class="col-sm-12"><button class="btn btn-default col-sm-12" onclick="hapus(\''.$table.'\', \'kd_brg\', \''.base64_encode($row['kd_brg']).'\')"><i class="fa fa-trash"></i> Delete</button></div></li>
																						';
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td><?=$row['kd_brg']?></td>
                                                    <td><?=$row['barcode']?></td>
                                                    <td><?=$row['nm_brg']?></td>
                                                    <td><?=(strlen($row['Deskripsi'])>30?substr($row['Deskripsi'], 0, 30).'...':$row['Deskripsi'])?></td>
                                                    <td><?=$row['nm_kel_brg']?></td>
                                                    <td><?=$row['nm_Group1']?></td>
                                                    <td><?=$row['nm_Group2']?></td>
                                                    <td><?=$row['satuan']?></td>
                                                    <td style="text-align:right;"><?=number_format($row['hrg_beli'])?></td>
                                                    <td><?=$row['stock_min']+0?></td>
                                                    <td><?=$row['berat']+0?> gr</td>
                                                    <td><?=$row['kategori']?></td>
                                                    <td><?=$row['Jenis']?></td>
                                                    <td><?=$row['online']=='1'?'Ya':'Tidak'?></td>
                                                    <td><?=$row['fav']=='1'?'Ya':'Tidak'?></td>
                                                    <td><?=$row['poin']=='1'?'Ya':'Tidak'?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="pull-right">
                                        <?= $this->pagination->create_links() ?>
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

<?php $caption_harga = $this->m_crud->get_data('harga', 'hrg1, hrg2, hrg3, hrg4', "Kode = '1111'"); ?>
<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 10):0); foreach($master_data as $row){ $i++; ?>
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
                            <div class="col-sm-5"><b>Kode Barang</b></div><div class="col-sm-7"><b> : </b><?=$row['kd_brg']?></div>
                            <div class="col-sm-5"><b>Barcode</b></div><div class="col-sm-7"><b> : </b><?=$row['barcode']?></div>
                            <div class="col-sm-5"><b>Nama Barang</b></div><div class="col-sm-7"><b> : </b><?=$row['nm_brg']?></div>
                            <div class="col-sm-5"><b>Kelompok Barang</b></div><div class="col-sm-7"><b> : </b><?=$row['nm_kel_brg']?></div>
                            <div class="col-sm-5"><b><?=$caption['as_group1']?></b></div><div class="col-sm-7"><b> : </b><?=$row['nm_Group1']?></div>
                            <div class="col-sm-5"><b><?=$caption['as_group2']?></b></div><div class="col-sm-7"><b> : </b><?=$row['nm_Group2']?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="col-sm-5"><b>Satuan</b></div><div class="col-sm-7"><b> : </b><?=$row['satuan']?></div>
                            <div class="col-sm-5"><b>Harga Beli</b></div><div class="col-sm-7"><b> : </b><?=number_format($row['hrg_beli'])?></div>
                            <div class="col-sm-5"><b>Kategori</b></div><div class="col-sm-7"><b> : </b><?=$row['kategori']?></div>
                            <div class="col-sm-5"><b>Jenis</b></div><div class="col-sm-7"><b> : </b><?=$row['Jenis']?></div>
                            <div class="col-sm-5"><b>Deskripsi</b></div><div class="col-sm-7"><b> : </b><?=$row['Deskripsi']?></div>
                            <div class="col-sm-5"><b>Gambar</b></div><div class="col-sm-7">
                                <?php
                                if($row['gambar']!=null && $row['gambar']!='-') {
                                    $str = $row['gambar'];
                                    $explode = explode('/', $str);
                                    if ($explode[0].'/' == $this->config->item('site')) {
                                        $gambar = $this->config->item('url').$row['gambar'];
                                    } else {
                                        $gambar = base_url().$row['gambar'];
                                    }
                                    echo '<img width="200" src="'.$gambar.'" />';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <table id="" class="table table-striped table-bordered">
                                <!--<table id="datatable<?=$i?>" class="table table-striped table-bordered">-->
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lokasi</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Margin</th>
                                    <th>Service</th>
                                    <th>PPN</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $get_data_master = $this->m_crud->get_data("barang", "hrg_beli, hrg_jual_1, service, PPN", "kd_brg='".$row['kd_brg']."'");
                                ?>
                                <tr>
                                    <td>1</td>
                                    <td>Master</td>
                                    <td style="text-align:right;"><?=number_format($get_data_master['hrg_beli'])?></td>
                                    <td style="text-align:right;"><?=number_format($get_data_master['hrg_jual_1'])?></td>
                                    <td><?=($get_data_master['hrg_beli']>0 && $get_data_master['hrg_beli']<$get_data_master['hrg_jual_1'])?round((1-($get_data_master['hrg_beli']/$get_data_master['hrg_jual_1']))*100, 2):'0'?> %</td>
                                    <td><?=$get_data_master['service']+0?> %</td>
                                    <td><?=$get_data_master['PPN']+0?> %</td>
                                </tr>
                                <?php $no = 1;
                                $detail = $this->m_crud->read_data('barang_hrg bh, barang br', 'bh.lokasi, bh.hrg_jual_1, bh.hrg_jual_2, bh.hrg_jual_3, bh.hrg_jual_4, bh.disc1, bh.ppn, br.hrg_beli', "bh.barang=br.kd_brg AND bh.barang = '".$row['kd_brg']."'", "bh.lokasi asc");
                                foreach($detail as $rows){ $no++; ?>
                                    <tr>
                                        <td><?=$no?></td>
                                        <td><?=$rows['lokasi']?></td>
                                        <td style="text-align:right;"><?=number_format($rows['hrg_beli'])?></td>
                                        <td style="text-align:right;"><?=number_format($rows['hrg_jual_1'])?></td>
                                        <td><?=($rows['hrg_beli']>0 && $rows['hrg_beli']<$rows['hrg_jual_1'])?round((1-($rows['hrg_beli']/$rows['hrg_jual_1']))*100, 2):'0'?> %</td>
                                        <td><?=$rows['service']+0?> %</td>
                                        <td><?=$rows['PPN']+0?> %</td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div id="modalCustomer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="titleModalCustomer"></h4>
                    <input type="hidden" id="kd_brg_temp">
                    <div class="row" style="margin-top: 10px">
                        <div class="col-md-6">
                            <input type="text" id="cariCustomer" class="form-control" placeholder="Cari Customer">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary waves-effect" onclick="simpanHargaCustomer()">Simpan</button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px">
                        <div class="col-md-6">
                            <label class="checkbox-inline"><input type="checkbox" name="tampilkanHarga" value="true" id="tampilkanHarga">Tampilkan yang memiliki harga jual</label>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table id="tableHargaCustomer" class="table table-striped" style="width: 100%">
                        <thead>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Harga Jual</th>
                        </thead>
                        <tbody id="listHargaCustomer"></tbody>
                    </table>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        $(function () {
            for(var i=1;i<=<?=$i?>;i++){
                $("#datatable"+i).DataTable();
            }
        });


    </script>
<?php } ?>


<div id="modalBahan" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleModalBahan"></h4><br>
                <input type="hidden" id="kd_brg_b">
                <input type="hidden" id="nm_brg_b">
                <div class="form-group">
                    <label for="">Tambah Bahan</label>
                    <div class="input-group">
                        <?php
                        $data_option = $this->m_crud->read_data('barang', 'kd_brg,nm_brg', "kategori='Bahan Baku'", 'nm_brg asc');
                        $option='';
                        ?>

                        <select name="kd_bahan" id="kd_bahan" class="form-control">
                            <option value="">==== PILIH ====</option>
                            <?php
                            foreach ($data_option as $row){
                                echo '<option value="'.$row['kd_brg'].'">'.$row['nm_brg'].'</option>';
                            }
                            ?>
                        </select>
                        <span onclick="addBahan()"  class="input-group-addon" id="isLoadBtn"><i class="fa fa-send"></i></span>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <table class="table table-striped" style="width: 100%">
                    <thead>
                    <th>No</th>
                    <th>Nama</th>
                    <th>#</th>
                    </thead>
                    <tbody id="result_bahan"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    function addBahan(){
        var kode_bahan=$("#kd_bahan").val();
        var kode_paket=$("#kd_brg_b").val();
        var nm_brg=$("#nm_brg_b").val();
        $.ajax({
            url: "<?=base_url().'api/getBahan/add'?>",
            type: "post",
            data:{kode_paket:kode_paket,kode_bahan:kode_bahan},
            dataType: "JSON",
            beforeSend: function () {
                $("#isLoadBtn").html('<i class="fa fa-circle-o-notch fa-spin"></i>');
            },
            complete: function () {
//                $("#loading").hide();
                $("#isLoadBtn").html("<i class='fa fa-send'></i>");
            },
            success: function (res) {
                console.log(res);
                if(res.status){
                    editBahan(kode_paket,nm_brg);
                }else{
                    alert("data sudah ada");
                }
            }
        });

    }
    function deleteBahan(kd_bahan,kd_paket,nama){
        if(confirm('Delete Data?')){
            $.ajax({
                url: "<?=base_url().'api/getBahan/delete'?>",
                type: "post",
                data:{kd_bahan:kd_bahan},
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $("#loading").hide();
                },
                success: function (res) {
                    if(res.status){
                        editBahan(kd_paket,nama);
                    }
                }
            });
        }

    }
    function editBahan(id,nama){
        $.ajax({
            url: "<?=base_url().'api/getBahan/edit'?>",
            type: "post",
            data:{kd_brg:id,nm_brg:nama},
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $("#loading").hide();
            },
            success: function (res) {
                $("#kd_brg_b").val(id);
                $("#nm_brg_b").val(nama);
                $("#modalBahan").modal("show");
                $("#result_bahan").html(res.result);
                $("#titleModalBahan").text("daftar bahan "+nama);
            }
        });
    }
    function isShowInput(param,id,nama){
        $("#nm_bahan").focus();
        if(param==='add'){
            $("#kode_bahan").val(id);
            $("#nm_bahan").val(nama);
        }
        else{
            $("#kode_bahan").val(id);
            $("#nm_bahan").val(nama);
        }
    }
    $(document).ready(function () {
        if ('<?=$this->uri->segment(3)?>' == btoa('<?=date('Y-m-d')?>')) {
            send_message('new_price=1');
        }
        var isHargaTampil = $('input[name="tampilkanHarga"]:checked').length > 0;
        $('#tampilkanHarga').click(function() {
            // alert("Checkbox state (method 1) = " + $('#tampilkanHarga').prop('checked'));
            var kd=$("#kd_brg_temp").val()
            // alert("Checkbox state (method 2) = " +);
            if($('#tampilkanHarga').is(':checked')){
                $("#listHargaCustomer").html("");
                hargaCustomer(kd,true);
            }else{
                hargaCustomer(kd,false);
            }

        });
    });

    var listHargaCustomer = [];

    function hargaCustomer(kd_brg,isHarga=false) {
        $.ajax({
            url: "<?=base_url().'master_data/hargaCustomer'?>",
            type: "POST",
            data: {kd_brg: kd_brg},
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
                console.log('before')

            },
            complete: function () {
                console.log('complete')
                $("#loading").hide();
            },
            success: function (res) {
                console.log('sts',res.status)

                if (res.status) {

                    listHargaCustomer = res.data;
                    tampilHargaCustomer(listHargaCustomer,isHarga);
                    $("#modalCustomer").modal('show');
                }
            }
        });
    }


    $("#cariCustomer").keyup(function () {
        var cari = $(this).val();
        var filter = listHargaCustomer;

        if (cari != '') {
            filter = listHargaCustomer.filter(function (el) {
                return (el.nama.search(cari) !== -1);
            });
        }

        tampilHargaCustomer(filter);
    });
    function updateHargaCustomer(kd_cust) {
        var val = hapustitik($("#hrg_jual_"+kd_cust).val());

        var index = listHargaCustomer.findIndex((el) => el.kd_cust === kd_cust);

        listHargaCustomer[index].hrg_jual = val;
    }

    function simpanHargaCustomer() {
        $.ajax({
            url: "<?=base_url().'master_data/simpanHargaCustomer'?>",
            type: "POST",
            data: {list: JSON.stringify(listHargaCustomer)},
            dataType: "JSON",
            beforeSend: function () {
                $("#modalCustomer").modal('hide');
                $('#loading').show();
            },
            complete: function () {
                $("#loading").hide();
            },
            success: function (res) {
                if (res.status) {
                    alert('Data berhasil disimpan');
                } else {
                    alert('Data gagal disimpan');
                    $("#modalCustomer").modal('show');
                }
            }
        });
    }
    function hapustitik(str) {
        str = str.toString();
        while (str.search(/\./) >= 0) {
            str = (str + "").replace('.', '');
        }
        return str;
    }

    function tampilHargaCustomer(data,isHarga=false) {
        var list = '';
        var title = '';

        if (data.length > 0) {
            jQuery.each(data, function (i, item) {
                if (i === 0) {
                    title = item.kd_brg + ' - ' + item.nm_brg + ' (Rp ' + to_rp(item.hrg_jual_1) + ')';
                    $("#kd_brg_temp").val(item.kd_brg)
                }
                if(!isHarga){
                    list += '<tr>' +
                        '<td>'+(i+1)+'</td>' +
                        '<td>'+item.nama+'</td>' +
                        '<td><input style="width: 100%" type="text" id="hrg_jual_'+item.kd_cust+'" onkeyup="updateHargaCustomer(\''+item.kd_cust+'\')" class="form-control currency" value="'+item.hrg_jual+'"></td>' +
                        '</tr>';
                }else{
                    if(item.hrg_jual!==".0000"){
                        list += '<tr>' +
                            '<td>'+(i+1)+'</td>' +
                            '<td>'+item.nama+'</td>' +
                            '<td><input style="width: 100%" type="text" id="hrg_jual_'+item.kd_cust+'" onkeyup="updateHargaCustomer(\''+item.kd_cust+'\')" class="form-control currency" value="'+item.hrg_jual+'"></td>' +
                            '</tr>';
                    }
                }
            });

            $("#listHargaCustomer").html(list);
            $("#titleModalCustomer").text(title);
            // $('.currency').('init', {aSep: '.', aDec: ',', mDec: '0', lZero: 'deny', vMin: 0});
        }
    }

    function after_change(val) {
        $.ajax({
            url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
            type: "GET"
        });
    }

    function hapus(table, column, id){
        if(confirm('Delete Data?')){
            $.ajax({
                //type:'POST',
                url:'<?=site_url().$this->control?>/delete_barang/' + table + '/' + column + '/' + id,
                //url:"<?=site_url()?>site/delete_ajax2/" + table + "/" + btoa(column + " = '" +atob(id)+ "'") + "/" + btoa("select kd_brg from kartu_stock where kd_brg = '"+atob(id)+"' and keterangan <> 'Input Barang'"),
                //data: {delete_id : id},
                success: function (data) {
                    if(data==1){
                        window.location='<?=site_url().$this->control?>/<?=$page?>';
                    } else {
                        alert('Delete Failed. Data sudah digunakan transaksi');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown){ alert('Delete Failed'); }
            });
        }
    }
</script>
