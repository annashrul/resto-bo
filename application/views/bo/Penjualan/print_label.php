
<link rel="stylesheet" href="<?=base_url().'assets/'?>css/style-print.css">
<style>
    .column{
        -webkit-column-count: 2;
        -moz-column-count: 2;
        column-count: 2;
    }

    table{
        page-break-after: auto;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .py{
        padding: 1rem 0;
    }
    .bb-1{
        border-bottom: 1px solid #222;
    }
    .bl-1{
        border-left: 1px solid #222;
    }
    .logo{
        border-left: 1px solid #222;
        width: 80px;
        padding: .5rem;
    }
    .exp{
        border-left: 1px solid #222;
        text-align: center;
        width: 80px;
    }
    .lead{
        font-size: 1.25rem;
        font-weight: 700;
    }
    .p-0{
        padding: 0;
    }
    .ls-1{
        letter-spacing: 1px;
    }
    .pb-025{
        padding-bottom: .25rem;
    }
    .ttu{
        text-transform: uppercase;
    }
    td.to{
        width: 20px;
        vertical-align: top;
        padding-right: 0 !important;
    }
</style>

<div id="non-printable">
    <div class="block-left">
        <button class="btn btn-blank" onClick="history.back()">&larr; Back</button>
    </div>
    <div class="block-print">
        <div class="radio-outer">
            <p style="padding: 0 0 5px; margin: 0; font-weight: bold; letter-spacing: 2px;">CETAK:</p>
            <input type="radio" name="print" id="print-label" value="print-label" checked="checked">
            <label for="print-label">Shipping Label</label> <br>
            <input type="radio" name="print" id="print-label-v2" value="print-label-v2">
            <label for="print-label-v2">Shipping Label (v2)</label> <br>
            <input type="radio" name="print" id="print-invoice" value="print-invoice">
            <label for="print-invoice">Invoice</label>
        </div>
    </div>
    <div class="block-options">
        <div class="checkbox-outer-label">
            <p style="padding: 0 0 5px; margin: 0; font-weight: bold; letter-spacing: 2px;">PENGATURAN:</p>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_order" checked> Detail Order
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_fragile"> Fragile Sign
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_logo" checked> Shop Logo
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_shop_info" checked> Shop Info
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_expedisi" checked> Expedisi
                </label>
            </div>
            <div class="checkbox" style="display:none">
                <label>
                    <input type="checkbox" id="toggle_qrcode"> QR Code
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_po"> No. Order
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_tgl_order"> Tanggal Order
                </label>
            </div>

        </div>

        <div class="checkbox-outer-label-v2">
            <p style="padding: 0 0 5px; margin: 0; font-weight: bold; letter-spacing: 2px;">PENGATURAN:</p>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_order_v2" checked> Detail Order
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_po_v2"> No. Order
                </label>
            </div>

            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_tgl_order_v2"> Tanggal Order
                </label>
            </div>

        </div>


        <div class="checkbox-outer-invoice">
            <p style="padding: 0 0 5px; margin: 0; font-weight: bold; letter-spacing: 2px;">PENGATURAN:</p>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="toggle_sku_inv"> SKU
                </label>
            </div>
        </div>
    </div>
    <div class="block-right">
        <button class="btn btn-blue" onClick="printpage()"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fff" viewBox="0 0 485.212 485.212"><path d="M151.636 363.906h151.618v30.33H151.636zm-30.324-333.58h242.595v60.65h30.32v-60.65C394.23 13.596 380.667 0 363.907 0H121.313c-16.748 0-30.327 13.595-30.327 30.327v60.65h30.327v-60.65zm30.324 272.93h181.94v30.328h-181.94z"/><path d="M454.882 121.304H30.334c-16.748 0-30.327 13.59-30.327 30.324v181.956c0 16.76 13.58 30.32 30.327 30.32h60.65v90.98c0 16.765 13.58 30.327 30.328 30.327h242.595c16.76 0 30.32-13.56 30.32-30.323v-90.98h60.654c16.76 0 30.325-13.562 30.325-30.32v-181.96c0-16.732-13.56-30.323-30.32-30.323zm-90.975 333.582H121.312V272.93h242.595v181.956zm60.644-242.604c-16.76 0-30.32-13.564-30.32-30.327 0-16.73 13.56-30.327 30.32-30.327 16.768 0 30.334 13.595 30.334 30.327 0 16.762-13.567 30.327-30.33 30.327z"/></svg> Print</button>
    </div>
</div>
<div id="printable">
    <?php
    foreach ($data as $id => $row) {
        $get_produk = $this->m_crud->join_data("det_orders do", "do.qty, do.berat, do.catatan, (do.hrg_jual+do.hrg_varian-do.diskon) harga, '-' ukuran, '-' warna, bo.nama, bo.id_barang code", "barang_online bo", "do.det_produk=bo.id_barang", "do.orders='".$row['id_orders']."'");
        $list_label = '';
        $list_inv = '';
        $berat = 0;
        $total = 0;
        foreach ($get_produk as $row_produk) {
            $berat = $berat + (float)($row_produk['berat']*$row_produk['qty']);
            $total = $total + (float)($row_produk['harga']*$row_produk['qty']);
            $list_label .= '
                <li>
                    <span class="left">'.$row_produk['nama'].' ('.$row_produk['ukuran'].'~'.$row_produk['warna'].')</span>
                    <span class="right">'.(int)$row_produk['qty'].' Item</span>
                </li>
            ';
            $list_inv .= '
                <tr style="line-height: 1.25em;font-size: 12px; vertical-align: middle;">
                    <td colspan="2">
                        '.$row_produk['nama'].' ('.$row_produk['ukuran'].'~'.$row_produk['warna'].') ('.$row_produk['catatan'].') <span class="sku-inv" style="float:right; display: none;">'.$row_produk['code'].'</span>					
                    </td>
                    <td>'.(int)$row_produk['qty'].'</td>
                    <td>'.(($row_produk['berat']*$row_produk['qty'])/1000).' Kg</td>
                    <td>Rp '.number_format($row_produk['harga']).'</td>
                    <td>Rp '.number_format($row_produk['harga']*$row_produk['qty']).'</td>
                </tr>
            ';
        }
        echo '
        <table width="100%" border="0" cellspacing="0" class="print-data printLabel" id="'.$id.'">
            <tr>
                <td width="20%" rowspan="3" style="" class="text-center shop-logo">
                    <img class="img-logo" src="'.$situs['logo'].'" width="120">
                    <div class="shop-info">
                        <h4 style="margin:10px 0 5px;">'.$situs['nama'].'</h4>
                        <p>'.$situs['web'].'</p>
                    </div>    
                </td>
                <td class="plabel">Kepada:</td>
                <td class="plabel orderdetail">Order <span class="po-number" style="display:none;">'.$row['id_orders'].'</span> <span class="tgl_order" style="display:none;">('.date('d M Y', strtotime($row['tgl_orders'])).')</td>
                <td class="fragile" width="30%" rowspan="4">
                    <img class="img-fragile" src="'.base_url().'assets/images/site/'.'fragile.png " alt="">
                    <h2>FRAGILE</h2>
                    <p>JANGAN DIBANTING!!!</p>
                </td>
            </tr>
            <tr>
                <td width="40%" valign="top">
                    <p class="receiver-name ls-1"> '.$row['penerima'].'</p>
                    <p class="address">'.$row['alamat'].'<br>
                        Kec. '.$row['kecamatan'].',  Kota '.$row['kota'].', '.$row['kode_pos'].' <br>
                        Provinsi '.$row['provinsi'].' <br>
                        Telp: '.$row['telepon'].'
                    </p>
                </td>
                <td style="font-size: 12px;" width="30%" valign="top" class="orderdetail">
                    <ul class="product-list">
                        '.$list_label.'
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="plabel">Pengirim: </p>
                    <p class="sender" >'.$situs['nama'].'<br>'.$situs['tlp'].'</p>
                </td>
                <td>
                    <span class="expedisi">'.$row['kurir'].'-'.$row['service'].' ('.($berat/1000).' Kg)</span>
                </td>
            </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" class="print-data printInvoice" font-size="12px;" style="display: none;" id="'.$id.'">
            <tr style="margin: 0;padding: 20px;">
                <td style="margin: 0;" width="10%">
                    <img class="img-logo" src="'.$situs['logo'].'" style="width: 64px;">
                </td>
                <td colspan="3" style="margin: 0;vertical-align: top;">
                    <h3 style="padding:0; margin: 0.5em 0 0;">'.$situs['nama'].'</h3>
                    <p>'.$situs['web'].'</p>    
                </td>
                <td colspan="2" style="margin: 0;vertical-align: top;">
                    <h5 style="padding:0; margin:0.5em 0;">
                        <strong>Tanggal:</strong>
                        <span style="clear:both;display:block;font-weight: normal;">'.date('d M Y', strtotime($row['tgl_orders'])).'</span>
                    </h5>
                    <h5 style="padding:0; margin:0.5em 0;">
                        <strong>Nomor Invoice:</strong>
                        <span style="clear:both;display:block;font-weight: normal;">'.$row['id_orders'].'</span>
                    </h5>    
                </td>
            </tr>    
            <tr style="margin: 0; padding: 20px;">
                <td colspan="4">
                    <p style="line-height: 1em;margin: 0;padding: 20px 0 0;"><strong>Kepada <span style="text-transform: capitalize;">'.$row['penerima'].'</span></strong></p>
                    <p style="font-size: 12px;line-height: 2em;">Terima kasih telah berbelanja di '.$situs['nama'].'. Berikut adalah rincian orderan Anda:</p>
                </td>
                <td colspan="2" style="font-size: 0.85rem;">
                    <!--<strong><span style="color: #E45864;">UNPAID</span></strong>-->
                </td>
            </tr>    
            <tr style="margin: 0; background: #555;line-height: 1em;font-size: 12px;color:#fff;">
                <td colspan="2" style="padding: 10px 20px; width: 45%;">Nama Produk</td>
                <td style="padding: 10px 20px; width: 10%;">Jumlah</td>
                <td style="padding: 10px 20px; width: 15%;">Berat</td>
                <td style="padding: 10px 20px; width: 15%;">Harga</td>
                <td style="padding: 10px 20px; width: 15%;">Subtotal</td>
            </tr>
            '.$list_inv.'
            <tr style="line-height: 1.25em;font-size: 12px;">
                <td colspan="2">
                    <strong>'.$row['kurir'].'-'.$row['service'].'</strong>
                </td>
                <td></td>
                <td>'.($berat/1000).' Kg</td>
                <td>Rp '.number_format($row['biaya']).'</td>
                <td>Rp '.number_format($row['biaya']).'</td>
            </tr>
            <tr style="line-height: 1.25em;font-size: 12px;">
                <td colspan="5">Kode unik</td>
                <td>Rp '.number_format($row['kode_unik']).'</td>
            </tr>    
            <tr style="line-height: 2em;font-size: 12px;">
                <td colspan="5"><span style="font-weight: 700; font-size: 1rem;">TOTAL</span></td>
                <td><span style="font-weight: 700; font-size: 1rem;">Rp '.number_format($total+$row['kode_unik']+$row['biaya']).'</span></td>
            </tr>
            <tr>
                <td colspan="6">
                    <hr style="border-color: #ddd; border-style: dotted;">
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;margin: 0;padding: 10px 0;">
                    <p style="line-height: 1em;margin: 0;padding: 0 0 0 20px;font-size:12px;">Alamat Pengiriman:</p>
                </td>
                <td colspan="5">
                    <p style="font-size: 12px;line-height: 1.25em;margin:0;padding: 10px 0;">
                        <span style="font-weight: bold; font-size:16px; text-transform: capitalize;">'.$row['penerima'].'</span><br>
                        '.$row['alamat'].'
                        Kec. '.$row['kecamatan'].',  Kota '.$row['kota'].', '.$row['kode_pos'].'
                        Prov. '.$row['provinsi'].'<br>
                        Telp: '.$row['telepon'].'
                    </p>    
                </td>
            </tr>
        </table>
        <table width="100%" border="0" cellspacing="0" class="print-data printLabelV2" style="display: none; font-size: .775rem;" id="'.$id.'">
            <tr>
                <td class="py bb-1 to">
                    <strong>FROM:</strong>
                </td>
                <td class="py bb-1">
                    '.$situs['nama'].' ('.$situs['tlp'].')
                </td>
                <td class="bb-1 exp">
                    <strong>'.$row['kurir'].'-'.$row['service'].'</strong><br>
                    ('.($berat/1000).' kg)
                </td>
            </tr>
            <tr>
                <td class="py bb-1 to">
                    <p class="lead pb-025">TO:</p>
                </td>
                <td class="py bb-1" colspan="2">
                    <span class="ttu ls-1"><strong>'.$row['penerima'].'</strong></span> <br>
                    '.$row['alamat'].'<br>
                    Kec. '.$row['kecamatan'].',  Kota '.$row['kota'].', '.$row['kode_pos'].'<br>
                    Provinsi '.$row['provinsi'].'<br>
                    Telp: '.$row['telepon'].'</td>
            </tr>
            <tr class="orderdetail_v2">
                <td class="py" colspan="3">
                    <p class="pb-025"><strong>ORDER <span class="po-number-v2" style="display:none;">'.$row['id_orders'].'</span> <span class="tgl_order_v2" style="display:none;">(Rabu, 4 Apr 2018)</strong></p>
                    <ul class="product-list" style="margin-bottom: 26px;">
                        '.$list_label.'
                    </ul>
                </td>
            </tr>
        </table>
        ';
    }
    ?>
</div>

<script type="text/javascript" src="<?=base_url().'assets/'?>js/jquery-1.11.3.min.js"></script>
<script>
    $(function () {
        $('.printLabel').each(function() {
            var pembatas = $(this).attr('id');
            if(pembatas % 4 === 0){
                //	$(this).after("<div class='page-break'></div>");
            }
        });

        $('.checkbox-outer-label-v2').hide();
        $('.checkbox-outer-invoice').hide();

        $('input:radio[name=print]').change(function() {

            $('.page-break').removeClass();

            if (this.value == 'print-label') {
                $('.printLabel').show();
                $('.printLabelV2').hide();
                $('.printInvoice').hide();
                $('.checkbox-outer-label').show();
                $('.checkbox-outer-label-v2').hide();
                $('.checkbox-outer-invoice').hide();
                $('#printable').removeClass('column');

                $('.printLabel').each(function() {
                    var pembatas = $(this).attr('id');
                    if(pembatas % 4 === 0){
                        //		$(this).after("<div class='page-break'></div>");
                    }
                });

            }
            else if(this.value == 'print-label-v2'){
                $('.printLabelV2').show();
                $('.printLabel').hide();
                $('.printInvoice').hide();
                $('.checkbox-outer-label').hide();
                $('.checkbox-outer-label-v2').show();
                $('.checkbox-outer-invoice').hide();

                $('#printable').addClass('column');

            }
            else if (this.value == 'print-invoice') {
                $('.printInvoice').show();
                $('.printLabel').hide();
                $('.printLabelV2').hide();
                $('.checkbox-outer-label').hide();
                $('.checkbox-outer-label-v2').hide();
                $('.checkbox-outer-invoice').show();
                $('#printable').removeClass('column');

                $('.printInvoice').each(function() {
                    var pembatas = $(this).attr('id');
                    if(pembatas % 2 === 0){
                        $(this).after("<div class='page-break'></div>");
                    }
                });

            }
        });

        $('input#toggle_fragile').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".fragile").show();
                } else {
                    $(".fragile").hide();
                }
            });

        $('input#toggle_order').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".orderdetail").show();
                } else {
                    $(".orderdetail").hide();
                }
            });

        $('input#toggle_logo').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".shop-logo").show();
                } else {
                    $(".shop-logo").hide();
                }
            });

        $('input#toggle_expedisi').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".expedisi").show();
                } else {
                    $(".expedisi").hide();
                }
            });

        $('input#toggle_qrcode').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".qrcode").show();
                } else {
                    $(".qrcode").hide();
                }
            });

        $('input#toggle_shop_info').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".shop-info").show();
                } else {
                    $(".shop-info").hide();
                }
            });

        $('input#toggle_po').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".po-number").show();
                } else {
                    $(".po-number").hide();
                }
            });


        $('input#toggle_tgl_order').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".tgl_order").show();
                } else {
                    $(".tgl_order").hide();
                }
            });

        $('input#toggle_note_inv').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".note-inv").show();
                } else {
                    $(".note-inv").hide();
                }
            });

        $('input#toggle_sku_inv').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".sku-inv").show();
                } else {
                    $(".sku-inv").hide();
                }
            });

        $('input#toggle_po_v2').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".po-number-v2").show();
                } else {
                    $(".po-number-v2").hide();
                }
            });

        $('input#toggle_tgl_order_v2').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".tgl_order_v2").show();
                } else {
                    $(".tgl_order_v2").hide();
                }
            });

        $('input#toggle_order_v2').change(
            function() {
                if ($(this).is(':checked')) {
                    $(".orderdetail_v2").show();
                } else {
                    $(".orderdetail_v2").hide();
                }
            });
    });

    function printpage()
    {
        window.print();
    }
</script>