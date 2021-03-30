<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <div class="user-details">
            <div class="pull-left">
                <?php
                $id = $this->session->userdata($this->site. 'user');
                $get_foto = $this->m_crud->get_data("user_detail","foto","user_id='".$id."'");
                ?>
                <img src="<?=($get_foto['foto']==null)?base_url().'assets/images/'.'user-default.png':base_url().'assets/images/foto/'.$get_foto['foto']?>" alt="" class="thumb-md img-circle">
            </div>
            <div class="user-info">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?=$account['nama']?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?=base_url().'setting/edit_profile/?id='.$id?>"><i class="md md-face-unlock"></i> Profile</a></li>
                        <!--<li><a href="javascript:void(0)"><i class="md md-settings"></i> Settings</a></li>
                        <li><a href="javascript:void(0)"><i class="md md-lock"></i> Lock screen</a></li>-->
                        <li><a href="<?=base_url().'site/logout'?>"><i class="md md-settings-power"></i> Logout</a></li>
                    </ul>
                </div>

                <p class="text-muted m-0"><?=$account['lvl']?></p>
            </div>
        </div>
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                <li>
                    <a href="<?=base_url()?>" class="waves-effect <?=($page=='dashboard')?'active':null?>"><i class="md md-home"></i><span>Dashboard</span></a>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,0,10))==0)?'style="display:none;"':null?>>
                    <?php $side_menu=null; $side_menu=array('0','preference','devices','poin','intro','slider','pengirim','deposit'); ?>
                    <a href="#" class="waves-effect <?=array_search($page, $side_menu)?'active':null?>"><i class="md md-settings"></i><span>Pengaturan</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='preference')?'active':null?>" <?=(substr($access->access,0,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/preference'?>">Perusahaan</a></li>
                        <li class="<?=($page=='devices')?'active':null?>" <?=(substr($access->access,1,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/devices'?>">Devices</a></li>
                        <li class="<?=($page=='poin')?'active':null?>" <?=(substr($access->access,2,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/poin'?>">Poin</a></li>
                        <li class="<?=($page=='intro')?'active':null?>" <?=(substr($access->access,3,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/intro'?>">Intro</a></li>
                        <li class="<?=($page=='slider')?'active':null?>" <?=(substr($access->access,4,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/slider'?>">Slider</a></li>
                        <li class="<?=($page=='pengirim')?'active':null?>" <?=(substr($access->access,5,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/pengirim'?>">Lokasi Pengirim</a></li>
                        <li class="<?=($page=='deposit')?'active':null?>" <?=(substr($access->access,6,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'setting/deposit'?>">Deposit</a></li>
                    </ul>
                </li>
                <li class="has_sub" <?=(((int)substr($access->access,11,20))==0 && ((int)substr($access->access,281,20))==0)?'style="display:none;"':null?>>
                    <?php $side_menu=null; $side_menu=array('0','user_level','harga_bertingkat','user_list','group1','group2','kelompok_barang','data_barang','barang_limit_stock','barang_harga','kategori_lokasi','fasilitas','data_lokasi','data_bank','data_assembly','data_kas','data_promo','konversi','tipe_customer','data_customer','data_sales', 'data_supplier', 'data_kitchen_printer', 'data_compliment', 'data_berita', 'data_kurir', 'area', 'meja'); ?>
                    <a href="#" class="waves-effect <?=array_search($page, $side_menu)?'active':null?>"><i class="md md-now-widgets"></i><span>Master Data</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='user_level')?'active':null?>" <?=(substr($access->access,11,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/user_level'?>">User Level</a></li>
                        <li class="<?=($page=='user_list')?'active':null?>" <?=(substr($access->access,12,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/user_list'?>">User List</a></li>
                        <li class="<?=($page=='kategori_lokasi')?'active':null?>" <?=(substr($access->access,13,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/kategori_lokasi'?>">Kategori Lokasi</a></li>
                        <li class="<?=($page=='fasilistas')?'active':null?>" <?=(substr($access->access,287,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/fasilitas'?>">Data Fasilitas</a></li>
                        <li class="<?=($page=='data_lokasi')?'active':null?>" <?=(substr($access->access,14,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_lokasi'?>">Data Lokasi</a></li>
                        <?php $menu_group = $this->m_crud->get_data('Setting', 'as_group1, as_group2', "Kode = '1111'")?>
                        <li class="<?=($page=='group2')?'active':null?>" <?=(substr($access->access,15,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/group2'?>"><?=($menu_group['as_group2']!=null)?$menu_group['as_group2']:'Group 2'?></a></li>
                        <li class="<?=($page=='group1')?'active':null?>" <?=(substr($access->access,16,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/group1'?>"><?=($menu_group['as_group1']!=null)?$menu_group['as_group1']:'Group 1'?></a></li>
                        <li class="<?=($page=='kelompok_barang')?'active':null?>" <?=(substr($access->access,17,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/kelompok_barang'?>">Kelompok Barang</a></li>
                        <li class="<?=($page=='data_barang')?'active':null?>" <?=(substr($access->access,18,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_barang'?>">Data Barang</a></li>
                        <li <?=(((int)substr($access->access,285,2))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='kel_brg_online'||$page=='barang_online')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='kel_brg_online'||$page=='barang_online')?'active':null?>"><span>Barang Online</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='kel_brg_online')?'active':null?>" <?=(substr($access->access,285,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/kel_brg_online'?>">Kelompok Barang</a></li>
                                <li class="<?=($page=='barang_online')?'active':null?>" <?=(substr($access->access,286,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/barang_online'?>">Data Barang</a></li>
                            </ul>
                        </li>
                        <li <?=(((int)substr($access->access,288,2))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='area'||$page=='meja')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='kel_brg_online'||$page=='barang_online')?'active':null?>"><span>Data Meja</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='area')?'active':null?>" <?=(substr($access->access,288,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/area'?>">Area</a></li>
                                <li class="<?=($page=='meja')?'active':null?>" <?=(substr($access->access,289,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/meja'?>">Meja</a></li>
                            </ul>
                        </li>
                        <li class="<?=($page=='barang_harga')?'active':null?>" <?=(substr($access->access,19,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/barang_harga'?>">Barang Harga</a></li>
                        <li class="<?=($page=='barang_limit_stock')?'active':null?>" <?=(substr($access->access,284,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/barang_limit_stock'?>">Barang Limit Stock</a></li>
                        <li class="<?=($page=='harga_bertingkat')?'active':null?>" <?=(substr($access->access,28,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/harga_bertingkat'?>">Harga Bertingkat</a></li>
                        <li class="<?=($page=='data_assembly')?'active':null?>" <?=(substr($access->access,29,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_assembly'?>">Data Paket</a></li>
                        <li class="<?=($page=='data_bank')?'active':null?>" <?=(substr($access->access,20,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_bank'?>">Data Bank</a></li>
                        <li class="<?=($page=='data_kas')?'active':null?>" <?=(substr($access->access,21,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_kas'?>">Data Kas</a></li>
                        <li class="<?=($page=='data_promo')?'active':null?>" <?=(substr($access->access,22,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_promo'?>">Data Promo</a></li>
                        <li class="<?=($page=='konversi')?'active':null?>" <?=(substr($access->access,23,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/konversi'?>">Konversi</a></li>
                        <li <?=(((int)substr($access->access,24,25))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='tipe_customer'||$page=='data_customer')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='tipe_customer'||$page=='data_customer')?'active':null?>"><span>Customer</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='tipe_customer')?'active':null?>" <?=(substr($access->access,24,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/tipe_customer'?>">Tipe Customer</a></li>
                                <li class="<?=($page=='data_customer')?'active':null?>" <?=(substr($access->access,25,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_customer'?>">Data Customer</a></li>
                            </ul>
                        </li>
                        <li class="<?=($page=='data_sales')?'active':null?>" <?=(substr($access->access,26,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_sales'?>">Data Sales/SPG</a></li>
                        <li class="<?=($page=='data_supplier')?'active':null?>" <?=(substr($access->access,27,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_supplier'?>">Data Supplier</a></li>
                        <li class="<?=($page=='data_kitchen_printer')?'active':null?>" <?=(substr($access->access,30,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_kitchen_printer'?>">Data Printer</a></li>
                        <li class="<?=($page=='data_compliment')?'active':null?>" <?=(substr($access->access,281,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_compliment'?>">Data Compliment</a></li>
                        <li class="<?=($page=='data_berita')?'active':null?>" <?=(substr($access->access,282,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_berita'?>">Data Berita</a></li>
                        <li class="<?=($page=='data_kurir')?'active':null?>" <?=(substr($access->access,283,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'master_data/data_kurir'?>">Data Kurir</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,31,20))==0)?'style="display:none;"':null?>>
                    <a href="#" class="waves-effect <?=($page=='delivery_note'||$page=='alokasi'||$page=='expedisi'||$page=='approval_order'||$page=='approval_alokasi'||$page=='approving_alokasi'||$page=='adjusment'||$page=='packing')?'active':null?>"><i class="md md-now-widgets"></i><span>Inventory</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='delivery_note')?'active':null?>" <?=(substr($access->access,37,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/delivery_note'?>">Delivery Note</a></li>
                        <li class="<?=($page=='alokasi')?'active':null?>" <?=(substr($access->access,31,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/alokasi'?>">Alokasi</a></li>
                        <li class="<?=($page=='adjusment')?'active':null?>" <?=(substr($access->access,32,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/adjusment'?>">Adjusment</a></li>
                        <li class="<?=($page=='packing')?'active':null?>" <?=(substr($access->access,33,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/packing'?>">Packing</a></li>
                        <li class="<?=($page=='approval_alokasi'||$page=='approving_alokasi')?'active':null?>" <?=(substr($access->access,34,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/approval_alokasi'?>">Approval Mutasi</a></li>
                        <li class="<?=($page=='approval_order')?'active':null?>" <?=(substr($access->access,35,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/approval_order'?>">Approve Order</a></li>
                        <li class="<?=($page=='expedisi')?'active':null?>" <?=(substr($access->access,36,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/expedisi'?>">Expedisi</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,51,20))==0)?'style="display:none;"':null?>>
                    <a href="#" class="waves-effect <?=($page=='pembelian_barang'||$page=='retur_tanpa_nota'||$page=='purchase_order'||$page=='po_by_cabang'||$page=='po_cabang'||$page=='po_mingguan')?'active':null?>"><i class="md md-now-widgets"></i><span>Pembelian</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='po_by_cabang')?'active':null?>" <?=(substr($access->access,55,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/po_by_cabang'?>">PO Cabang</a></li>
                        <li class="<?=($page=='po_cabang')?'active':null?>" <?=(substr($access->access,54,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/po_cabang'?>">PO Pusat</a></li>
                        <li class="<?=($page=='po_mingguan')?'active':null?>" <?=(substr($access->access,56,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/po_mingguan'?>">PO Mingguan</a></li>
                        <li class="<?=($page=='purchase_order')?'active':null?>" <?=(substr($access->access,51,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/purchase_order'?>">Purchase Order</a></li>
                        <li class="<?=($page=='pembelian_barang')?'active':null?>" <?=(substr($access->access,52,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/pembelian_barang'?>">Pembelian Barang</a></li>
                        <li class="<?=($page=='retur_tanpa_nota')?'active':null?>" <?=(substr($access->access,53,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/retur_tanpa_nota'?>">Retur Tanpa Nota</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,191,10))==0)?'style="display:none;"':null?>>
                    <a href="#" class="waves-effect <?=($page=='penjualan_barang'||$page=='pesanan_online'||$page=='req_deposit')?'active':null?>"><i class="md md-now-widgets"></i><span>Penjualan</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='penjualan_barang')?'active':null?>" <?=(substr($access->access,191,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_barang'?>">Penjualan Barang</a></li>
                        <li class="<?=($page=='pesanan_online')?'active':null?>" <?=(substr($access->access,192,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/pesanan_online'?>">Pesanan Online</a></li>
                        <li class="<?=($page=='req_deposit')?'active':null?>" <?=(substr($access->access,193,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/req_deposit'?>">Request Deposit</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,71,20))==0)?'style="display:none;"':null?>>
                    <a href="#" class="waves-effect <?=($page=='penerimaan_retur_cabang'||$page=='form_retur_cabang'||$page=='approval_retur_cabang')?'active':null?>"><i class="md md-now-widgets"></i><span>Retur Cabang</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='form_retur_cabang')?'active':null?>" <?=(substr($access->access,72,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'retur_cabang/form_retur_cabang'?>">Retur Cabang</a></li>
                        <li class="<?=($page=='penerimaan_retur_cabang'||$page=='approval_retur_cabang')?'active':null?>" <?=(substr($access->access,71,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'retur_cabang/penerimaan_retur_cabang'?>">Penerimaan Retur Cabang</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,241,10))==0)?'style="display:none;"':null?>>
                    <?php $side_menu=null; $side_menu=array('0','bayar_hutang','kontra_bon','bayar_kontra_bon'); ?>
                    <a href="#" class="waves-effect <?=array_search($page, $side_menu)?'active':null?>"><i class="md md-now-widgets"></i><span>Hutang</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='bayar_hutang')?'active':null?>" <?=(substr($access->access,241,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/bayar_hutang'?>">Bayar Hutang</a></li>
                        <li class="<?=($page=='kontra_bon')?'active':null?>" <?=(substr($access->access,242,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/kontra_bon'?>">Kontra Bon</a></li>
                        <li class="<?=($page=='bayar_kontra_bon')?'active':null?>" <?=(substr($access->access,243,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/bayar_kontra_bon'?>">Bayar Kontra Bon</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,251,10))==0)?'style="display:none;"':null?>>
                    <?php $side_menu=null; $side_menu=array('0','bayar_piutang'); ?>
                    <a href="#" class="waves-effect <?=array_search($page, $side_menu)?'active':null?>"><i class="md md-now-widgets"></i><span>Piutang</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='bayar_piutang')?'active':null?>" <?=(substr($access->access,251,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/bayar_piutang'?>">Bayar Piutang</a></li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,91,190))==0 && (int)substr($access->access, 204, 4)==0)?'style="display:none;"':null?>>
                    <a href="#" class="waves-effect <?=($page=='persediaan_konsinyasi'||$page=='expedisi_report'||$page=='po_by_cabang_report'||$page=='po_cabang_report'||$page=='order_report'||$page=='approval_order_report'||$page=='omset_periode'||$page=='laba_rugi'||$page=='adjusment_report'||$page=='kas_masuk'||$page=='kas_keluar'||$page=='budget_supplier'||$page=='penjualan_by_edc'||$page=='bayar_piutang_report'||$page=='bayar_hutang_report'||$page=='alokasi_by_cabang_report'||$page=='packing_report'||$page=='penjualan_by_sales'||$page=='penjualan_by_kassa'||$page=='penjualan_by_group2'||$page=='penjualan_by_barang'||$page=='penjualan_by_kasir'||$page=='pembelian_by_supplier'||$page=='penjualan_by_customer'||$page=='omset_penjualan'||$page=='pembelian_by_kel_barang'||$page=='penjualan_konsinyasi'||$page=='alokasi_report'||$page=='stock_opname_report'||$page=='stock_report'||$page=='alokasi_by_pembelian_report'||$page=='penjualan_by_kel_barang'||$page=='pembelian_barang_report'||$page=='purchase_order_report'||$page=='arsip_return_penjualan'||$page=='arsip_penjualan'||$page=='penjualan_by_group1'||$page=='pembelian_by_barang'||$page=='return_pembelian_report'||$page=='log_transaksi'||$page=='log_aktivitas'||$page=='pembelian_by_operator'||$page=='deposit_member'||$page=='ppob'||$page=='feedback'||$page=='contact'||$page=='arsip_penjualan_online')?'active':null?>"><i class="md md-report"></i><span>Laporan</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='deposit_member')?'active':null?>" <?=(substr($access->access,204,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'accounting/deposit_member'?>">Deposit Member</a></li>
                        <li class="<?=($page=='ppob')?'active':null?>" <?=(substr($access->access,205,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'accounting/ppob'?>">Laporan PPOB</a></li>
                        <li class="<?=($page=='feedback')?'active':null?>" <?=(substr($access->access,206,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/feedback'?>">Feedback Customer</a></li>
                        <li class="<?=($page=='contact')?'active':null?>" <?=(substr($access->access,207,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/contact'?>">Contact Us</a></li>
                        <li <?=(((int)substr($access->access,91,20))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='persediaan_konsinyasi'||$page=='penjualan_konsinyasi')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='persediaan_konsinyasi'||$page=='penjualan_konsinyasi')?'active':null?>"><span>Konsinyasi</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='persediaan_konsinyasi')?'active':null?>" <?=(substr($access->access,91,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'konsinyasi/persediaan_konsinyasi'?>">Persediaan Konsinyasi</a></li>
                                <li class="<?=($page=='penjualan_konsinyasi')?'active':null?>" <?=(substr($access->access,92,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'konsinyasi/penjualan_konsinyasi'?>">Penjualan Konsinyasi</a></li>
                            </ul>
                        </li>
                        <li <?=(((int)substr($access->access,111,20))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='delivery_note_report'||$page=='alokasi_report'||$page=='expedisi_report'||$page=='order_report'||$page=='approval_order_report'||$page=='alokasi_by_cabang_report'||$page=='adjusment_report'||$page=='packing_report'||$page=='stock_opname_report'||$page=='alokasi_by_pembelian_report'||$page=='stock_report')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='delivery_note_report'||$page=='stock_opname_report'||$page=='expedisi_report'||$page=='order_report'||$page=='approval_order_report'||$page=='adjusment_report'||$page=='alokasi_by_cabang_report'||$page=='packing_report'||$page=='alokasi_report'||$page=='alokasi_by_pembelian_report'||$page=='stock_report')?'active':null?>"><span>Inventory</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='delivery_note_report')?'active':null?>" <?=(substr($access->access,121,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/delivery_note_report'?>">Delivery Note</a></li>
                                <li class="<?=($page=='alokasi_report')?'active':null?>" <?=(substr($access->access,111,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/alokasi_report'?>">Alokasi</a></li>
                                <li class="<?=($page=='alokasi_by_pembelian_report')?'active':null?>" <?=(substr($access->access,113,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/alokasi_by_pembelian_report'?>">Alokasi By Pembelian</a></li>
                                <li class="<?=($page=='alokasi_by_cabang_report')?'active':null?>" <?=(substr($access->access,116,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/alokasi_by_cabang_report'?>">Branch Mutasi</a></li>
                                <li class="<?=($page=='packing_report')?'active':null?>" <?=(substr($access->access,115,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/packing_report'?>">Packing</a></li>
                                <li class="<?=($page=='stock_report')?'active':null?>" <?=(substr($access->access,112,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/stock_report'?>">Stock</a></li>
                                <li class="<?=($page=='stock_opname_report')?'active':null?>" <?=(substr($access->access,114,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/stock_opname_report'?>">Stock Opname</a></li>
                                <li class="<?=($page=='adjusment_report')?'active':null?>" <?=(substr($access->access,117,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/adjusment_report'?>">Adjusment</a></li>
                                <li class="<?=($page=='order_report')?'active':null?>" <?=(substr($access->access,118,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/order_report'?>">Order</a></li>
                                <li class="<?=($page=='approval_order_report')?'active':null?>" <?=(substr($access->access,119,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/approval_order_report'?>">Approve Order</a></li>
                                <li class="<?=($page=='expedisi_report')?'active':null?>" <?=(substr($access->access,120,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'inventory/expedisi_report'?>">Expedisi</a></li>
                            </ul>
                        </li>
                        <li <?=(((int)substr($access->access,131,20))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='po_cabang_report'||$page=='pembelian_barang_report'||$page=='bayar_hutang_report'||$page=='bayar_hutang_report'||$page=='pembelian_by_supplier'||$page=='budget_supplier'||$page=='pembelian_by_kel_barang'||$page=='purchase_order_report'||$page=='pembelian_by_barang'||$page=='return_pembelian_report'||$page=='kontra_bon_report'||$page=='bayar_kontra_bon_report'||$page=='pembelian_by_operator')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='po_by_cabang_report'||$page=='po_cabang_report'||$page=='pembelian_barang_report'||$page=='budget_supplier'||$page=='pembelian_by_supplier'||$page=='pembelian_by_kel_barang'||$page=='purchase_order_report'||$page=='pembelian_by_supplier'||$page=='return_pembelian_report'||$page=='kontra_bon_report'||$page=='bayar_kontra_bon_report'||$page=='pembelian_by_operator')?'active':null?>"><span>Pembelian</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='po_by_cabang_report')?'active':null?>" <?=(substr($access->access,143,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/po_by_cabang_report'?>">PO Cabang</a></li>
                                <li class="<?=($page=='po_cabang_report')?'active':null?>" <?=(substr($access->access,141,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/po_cabang_report'?>">PO Pusat</a></li>
                                <li class="<?=($page=='purchase_order_report')?'active':null?>" <?=(substr($access->access,131,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/purchase_order_report'?>">Purchase Order</a></li>
                                <li class="<?=($page=='pembelian_barang_report')?'active':null?>" <?=(substr($access->access,132,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/pembelian_barang_report'?>">Arsip Pembelian</a></li>
                                <li class="<?=($page=='return_pembelian_report')?'active':null?>" <?=(substr($access->access,133,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/return_pembelian_report'?>">Arsip Return Pembelian</a></li>
                                <li class="<?=($page=='pembelian_by_barang')?'active':null?>" <?=(substr($access->access,134,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/pembelian_by_barang'?>">Pembelian By Barang</a></li>
                                <li class="<?=($page=='pembelian_by_supplier')?'active':null?>" <?=(substr($access->access,135,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/pembelian_by_supplier'?>">Pembelian By Supplier</a></li>
                                <li class="<?=($page=='pembelian_by_kel_barang')?'active':null?>" <?=(substr($access->access,136,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/pembelian_by_kel_barang'?>">Pembelian By Kel Barang</a></li>
                                <li class="<?=($page=='pembelian_by_operator')?'active':null?>" <?=(substr($access->access,142,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/pembelian_by_operator'?>">Pembelian By Operator</a></li>
                                <li class="<?=($page=='budget_supplier')?'active':null?>" <?=(substr($access->access,137,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/budget_supplier'?>">Budget Supplier</a></li>
                                <li class="<?=($page=='bayar_hutang_report')?'active':null?>" <?=(substr($access->access,138,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/bayar_hutang_report'?>">Bayar Hutang</a></li>
                                <li class="<?=($page=='kontra_bon_report')?'active':null?>" <?=(substr($access->access,139,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/kontra_bon_report'?>">Kontra Bon</a></li>
                                <li class="<?=($page=='bayar_kontra_bon_report')?'active':null?>" <?=(substr($access->access,140,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'pembelian/bayar_kontra_bon_report'?>">Bayar Kontra Bon</a></li>
                            </ul>
                        </li>
                        <li <?=(((int)substr($access->access,151,20))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='arsip_penjualan'||$page=='omset_periode'||$page=='bayar_piutang_report'||$page=='penjualan_by_edc'||$page=='penjualan_by_sales'||$page=='penjualan_by_kassa'||$page=='penjualan_by_group2'||$page=='penjualan_by_barang'||$page=='penjualan_by_kasir'||$page=='penjualan_by_customer'||$page=='omset_penjualan'||$page=='penjualan_by_kel_barang'||$page=='penjualan_by_group1'||$page=='arsip_return_penjualan'||$page=='arsip_penjualan_online')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='arsip_penjualan'||$page=='omset_periode'||$page=='bayar_piutang_report'||$page=='penjualan_by_edc'||$page=='penjualan_by_sales'||$page=='penjualan_by_kassa'||$page=='penjualan_by_group2'||$page=='penjualan_by_barang'||$page=='penjualan_by_kasir'||$page=='penjualan_by_customer'||$page=='omset_penjualan'||$page=='penjualan_by_group1'||$page=='penjualan_by_kel_barang'||$page=='arsip_return_penjualan'||$page=='arsip_penjualan_online')?'active':null?>"><span>Penjualan</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='arsip_penjualan')?'active':null?>" <?=(substr($access->access,151,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/arsip_penjualan'?>">Arsip Penjualan</a></li>
                                <li class="<?=($page=='arsip_penjualan_online')?'active':null?>" <?=(substr($access->access,165,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/arsip_penjualan_online'?>">Arsip Penjualan Online</a></li>
                                <li class="<?=($page=='penjualan_by_group1')?'active':null?>" <?=(substr($access->access,152,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_group1'?>">Penjualan By <?=($menu_group['as_group1']!=null)?$menu_group['as_group1']:'Group 1'?></a></li>
                                <li class="<?=($page=='arsip_return_penjualan')?'active':null?>" <?=(substr($access->access,153,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/arsip_return_penjualan'?>">Arsip Return Penjualan</a></li>
                                <li class="<?=($page=='penjualan_by_kel_barang')?'active':null?>" <?=(substr($access->access,154,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_kel_barang'?>">Penjualan By Kel. Barang</a></li>
                                <li class="<?=($page=='omset_penjualan')?'active':null?>" <?=(substr($access->access,155,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/omset_penjualan'?>">Omset Penjualan</a></li>
                                <li class="<?=($page=='omset_periode')?'active':null?>" <?=(substr($access->access,164,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/omset_periode'?>">Omset Periode</a></li>
                                <li class="<?=($page=='penjualan_by_customer')?'active':null?>" <?=(substr($access->access,156,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_customer'?>">Penjualan By Customer</a></li>
                                <li class="<?=($page=='penjualan_by_kasir')?'active':null?>" <?=(substr($access->access,157,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_kasir'?>">Penjualan By Kasir</a></li>
                                <li class="<?=($page=='penjualan_by_barang')?'active':null?>" <?=(substr($access->access,158,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_barang'?>">Penjualan By Barang</a></li>
                                <li class="<?=($page=='penjualan_by_group2')?'active':null?>" <?=(substr($access->access,159,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_group2'?>">Penjualan By <?=($menu_group['as_group2']!=null)?$menu_group['as_group2']:'Group 2'?></a></li>
                                <li class="<?=($page=='penjualan_by_kassa')?'active':null?>" <?=(substr($access->access,160,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_kassa'?>">Penjualan By Kassa</a></li>
                                <li class="<?=($page=='penjualan_by_sales')?'active':null?>" <?=(substr($access->access,161,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_sales'?>">Penjualan By Sales</a></li>
                                <li class="<?=($page=='penjualan_by_edc')?'active':null?>" <?=(substr($access->access,162,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/penjualan_by_edc'?>">Penjualan By EDC</a></li>
                                <li class="<?=($page=='bayar_piutang_report')?'active':null?>" <?=(substr($access->access,163,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'penjualan/bayar_piutang_report'?>">Bayar Piutang</a></li>
                            </ul>
                        </li>
                        <li <?=(((int)substr($access->access,202,2))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='kas_masuk'||$page=='kas_keluar')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='kas_masuk'||$page=='kas_keluar')?'active':null?>"><span>Kas</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='kas_masuk')?'active':null?>" <?=(substr($access->access,202,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'accounting/kas_masuk'?>">Kas Masuk</a></li>
                                <li class="<?=($page=='kas_keluar')?'active':null?>" <?=(substr($access->access,203,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'accounting/kas_keluar'?>">Kas Keluar</a></li>
                            </ul>
                        </li>
                        <!--<li class="<?=($page=='laba_rugi')?'active':null?>" <?=(substr($access->access,201,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'accounting/laba_rugi'?>">Laba Rugi</a></li>-->
                        <li <?=(((int)substr($access->access,171,10))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='retur_cabang_report')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='retur_cabang_report')?'active':null?>"><span>Retur Cabang</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='retur_cabang_report')?'active':null?>" <?=(substr($access->access,171,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'retur_cabang/retur_cabang_report'?>">Arsip Retur Cabang</a></li>
                            </ul>
                        </li>
                        <li <?=(((int)substr($access->access,271,10))==0)?'style="display:none;"':null?> class="has_sub <?=($page=='log_otorisasi'||$page=='log_transaksi'||$page=='log_aktivitas')?'active':null?>">
                            <a href="#" class="waves-effect <?=($page=='log_otorisasi')?'active':null?>"><span>Log</span> <span class="pull-right"><i class="md md-add"></i></span></a>
                            <ul class="list-unstyled" style="">
                                <li class="<?=($page=='log_otorisasi')?'active':null?>" <?=(substr($access->access,271,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/log_otorisasi'?>">Log Otorisasi</a></li>
                                <li class="<?=($page=='log_transaksi')?'active':null?>" <?=(substr($access->access,272,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/log_transaksi'?>">Log Transaksi</a></li>
                                <li class="<?=($page=='log_aktivitas')?'active':null?>" <?=(substr($access->access,273,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/log_aktivitas'?>">Log Aktivitas</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="has_sub" <?=(((int)substr($access->access,181,10))==0)?'style="display:none;"':null?>>
                    <?php $side_menu=null; $side_menu=array('0','cetak_barcode','cetak_barcode_custom','cetak_packing_barang','cetak_price_tag'); ?>
                    <a href="#" class="waves-effect <?=array_search($page, $side_menu)?'active':null?>"><i class="md md-settings"></i><span>Utility</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        <li class="<?=($page=='cetak_barcode')?'active':null?>" <?=(substr($access->access,181,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/cetak_barcode'?>">Cetak Barcode</a></li>
                        <li class="<?=($page=='cetak_barcode_custom')?'active':null?>" <?=(substr($access->access,184,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/cetak_barcode_custom'?>">Cetak Barcode Custom</a></li>
                        <li class="<?=($page=='cetak_packing_barang')?'active':null?>" <?=(substr($access->access,182,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/cetak_packing_barang'?>">Cetak Packing Barang</a></li>
                        <li class="<?=($page=='cetak_price_tag')?'active':null?>" <?=(substr($access->access,183,1)!=1)?'style="display:none;"':null?>><a href="<?=base_url().'utility/cetak_price_tag'?>">Cetak Price Tag</a></li>
                    </ul>
                </li>

                <!--
				<li class="has_sub">
					<a href="#" class="waves-effect <?=($page=='persediaan_konsinyasi')?'active':null?>"><i class="md md-report"></i><span>Konsinyasi</span><span class="pull-right"><i class="md md-add"></i></span></a>
					<ul class="list-unstyled">
						<li class="<?=($page=='persediaan_konsinyasi')?'active':null?>"><a href="<?=base_url().'konsinyasi/persediaan_konsinyasi'?>">Persediaan Konsinyasi</a></li>
					</ul>
				</li>
				<li class="has_sub">
					<a href="javascript:void(0);" class="waves-effect"><i class="md md-share"></i><span>Multi Level </span><span class="pull-right"><i class="md md-add"></i></span></a>
					<ul>
						<li class="has_sub">
							<a href="javascript:void(0);" class="waves-effect"><span>Menu Level 1.1</span> <span class="pull-right"><i class="md md-add"></i></span></a>
							<ul style="">
								<li><a href="javascript:void(0);"><span>Menu Level 2.1</span></a></li>
								<li><a href="javascript:void(0);"><span>Menu Level 2.2</span></a></li>
								<li><a href="javascript:void(0);"><span>Menu Level 2.3</span></a></li>
							</ul>
						</li>
						<li>
							<a href="javascript:void(0);"><span>Menu Level 1.2</span></a>
						</li>
					</ul>
				</li>
				-->
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- Left Sidebar End --> 

