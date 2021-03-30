<!--
font sebelumnya
font-family: 'Courier New';
-->

<?php
    $row = $this->m_crud->get_join_data('Master_Retur_Beli rb', "rb.No_Retur, rb.keterangan, rb.Tgl, rb.lokasi_cabang, rb.kd_kasir, rb.Lokasi, sp.Kode, sp.Nama, rb.no_beli, isnull(mb.noNota, 'Tanpa Nota') noNota, rb.Total", array(array("table"=>"Supplier sp", "type"=>"LEFT"), array("table"=>"master_beli mb", "type"=>"LEFT")), array("rb.Supplier=sp.kode", "rb.no_beli=mb.no_faktur_beli"), "rb.No_Retur='".$no_retur."'");
?>
<script src="<?=base_url().'assets/'?>js/jquery.min.js"></script>
<div id="print_retur_pembelian" class="hidden">
    <div class="row"><img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['No_Retur']?>"></div>
    <img style="height: 1cm; position: absolute" src="<?=$this->config->item('url').$this->m_website->site_data()->logo?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'OpenSans-Regular', 'Lucida Sans Typewriter', 'Lucida Typewriter', 'Arial', 'Helvetica', 'sans-serif'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td style="height: 1.5cm; text-align: center" colspan="8">Nota Retur Pembelian</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="2%"></td>
            <td width="23%"></td>
            <td width="2%"></td>
            <td width="25%"></td>

            <td width="3%"></td>
            <td width="19%"></td>
            <td width="2%"></td>
            <td width="25%"></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 8pt !important">Tanggal</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 10pt !important"><?=substr($row['Tgl'],0,10)?></td>

            <td></td>
            <td style="font-size: 8pt !important">Retur Ke</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['Kode']." - ".$row['Nama']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 8pt !important">No. Transaksi</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['No_Retur']?></td>

            <td></td>
            <td style="font-size: 8pt !important">Nota Supplier</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['noNota']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 8pt !important">Operator</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$this->m_website->get_nama_user($row['kd_kasir'])?></td>

            <td></td>
            <td style="font-size: 8pt !important">Lokasi Cabang</td>
            <td style="font-size: 8pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['lokasi_cabang']?></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'OpenSans-Regular', 'Lucida Sans Typewriter', 'Lucida Typewriter', 'Arial', 'Helvetica', 'sans-serif'; font-size:9pt;">
        <thead>
        <tr>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">No</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kode Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Barcode</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Nama Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Satuan</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kondisi</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Beli</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Sub Total</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;
        $sub_total = 0;
        $tqty = 0;
        $total = 0;
        $detail = $this->m_crud->read_data('Det_Retur_Beli drb, barang br, kel_brg kb', 'drb.kd_brg, drb.jml, drb.hrg_beli, drb.kondisi, drb.keterangan, br.barcode, br.nm_brg, kb.nm_kel_brg, br.satuan', "drb.kd_brg = br.kd_brg AND br.kel_brg = kb.kel_brg AND drb.No_Retur = '".$row['No_Retur']."'");
        foreach($detail as $rows){
            $no++;
            $sub_total = $rows['jml'] * $rows['hrg_beli'];
            $total = $total + $sub_total;
            $tqty = $tqty + ($rows['jml']+0);
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=$rows['barcode']?></td>
                <td><?=$rows['nm_brg']?></td>
                <td><?=($rows['jml']+0)?></td>
                <td><?=$rows['satuan']?></td>
                <td><?=$rows['kondisi']?></td>
                <td class="text-right"><?=number_format($rows['hrg_beli'],2)?></td>
                <td class="text-right"><?=number_format($sub_total,2)?></td>
            </tr>
            <?php
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="border-top: solid; border-width: thin" colspan="4">TOTAL</td>
            <td style="border-top: solid; border-width: thin"><?=$tqty?></td>
            <td style="border-top: solid; border-width: thin" colspan="3"></td>
            <td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($total,2)?></td>
        </tr>
        </tfoot>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'OpenSans-Regular', 'Lucida Sans Typewriter', 'Lucida Typewriter', 'Arial', 'Helvetica', 'sans-serif';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:left;" colspan="2">
                <u><?=number_to_words($total)?></u>
            </td>
        </tr>
        <tr>
            <td style="text-align:center;">
                Penerima
            </td>
            <td style="text-align:center;">
                Pengirim
            </td>
            <td style="text-align:center;">
                Admin
            </td>
        </tr>
        <tr>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
        </tr>
        </tbody>
    </table>
    <table width="100%" border="0" style="margin-top: 5mm; letter-spacing: 5px; font-family: 'OpenSans-Regular', 'Lucida Sans Typewriter', 'Lucida Typewriter', 'Arial', 'Helvetica', 'sans-serif';font-size: 9pt">
        <tr>
            <td colspan="3">Ket : <?=$row['keterangan']?></td>
        </tr>
    </table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['No_Retur'])?></span>
</div>

<script>
    function printDiv(divName){
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        //location.reload();
    }

    $(document).ready(function () {
        //add_activity('<?=$row['No_Retur']?>');
        printDiv('print_retur_pembelian')
    });
</script>