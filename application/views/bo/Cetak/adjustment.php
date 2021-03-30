<div id="print_3ply" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['kd_trx']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center">Adjustment Stock (<?=$row['kd_trx']?>)</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="2%"></td>
            <td width="20%"></td>
            <td width="2%"></td>
            <td width="28%"></td>

            <td width="10%"></td>
            <td width="12%"></td>
            <td width="2%"></td>
            <td width="25%"></td>
        </tr>
        <tr>
            <td></td>
            <td>Tanggal</td>
            <td>:</td>
            <td><?=substr($row['tgl'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$row['nama']?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi</td>
            <td>:</td>
            <td><?=$row['lokasi']?></td>

            <td></td>
            <td>Keterangan</td>
            <td>:</td>
            <td><?=$row['keterangan']?></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode</td>
            <td style="width: 30%; border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Stock Terakhir</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt">Jenis</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty Adjust</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Saldo Stock</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;
        $detail = $this->m_crud->join_data('det_adjust da', 'br.kd_brg, br.barcode, br.nm_brg, da.status, isnull(da.stock_terakhir, 0) stock_terakhir, da.qty_adjust, da.saldo_stock', array(array('table'=>'barang br', 'type'=>'LEFT')), array('da.kd_brg = br.kd_brg'), "da.kd_trx = '".$row['kd_trx']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=$rows['barcode']?></td>
                <td><?=$rows['nm_brg']?></td>
                <td class="text-center"><?=(int)$rows['stock_terakhir']?></td>
                <td><?=$rows['status']?></td>
                <td class="text-center"><?=(int)$rows['qty_adjust']?></td>
                <td class="text-center"><?=(int)$rows['saldo_stock']?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <th style="border-top: solid; border-width: thin;" width="50%"></th>
            <th style="border-top: solid; border-width: thin;" width="50%"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:center;">
                <br/>Operator<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Mengetahui<br/><br/><br/>_____________
            </td>
        </tr>
        </tbody>
    </table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['kd_trx'])?></span>
</div>

<script src="<?=base_url().'assets/'?>js/jquery.min.js"></script>
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
        //add_activity('<?=$row['kd_trx']?>');
        printDiv('print_3ply');
    });
</script>