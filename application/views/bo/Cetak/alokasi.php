<div id="print_3ply" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_faktur_mutasi']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center">Alokasi Barang (<?=$row['no_faktur_mutasi']?>)</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="2%"></td>
            <td width="20%"></td>
            <td width="2%"></td>
            <td width="28%"></td>

            <td width="6%"></td>
            <td width="12%"></td>
            <td width="2%"></td>
            <td width="29%"></td>
        </tr>
        <tr>
            <td></td>
            <td>Tanggal</td>
            <td>:</td>
            <td><?=substr($row['tgl_mutasi'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$this->m_website->get_nama_user($row['kd_kasir'])?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Asal</td>
            <td>:</td>
            <td><?=$row['kd_lokasi_1']?></td>

            <td></td>
            <td>Jenis Transaksi</td>
            <td>:</td>
            <td><?=(substr($row['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch')?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Tujuan</td>
            <td>:</td>
            <td><?=$row['kd_lokasi_2']?></td>

            <td></td>
            <td>Delivery Note</td>
            <td>:</td>
            <td><?=$row['no_faktur_beli']?></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-right">Harga</td>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0; $total = 0;
        $detail = $this->m_crud->join_data('Det_Mutasi as dm', 'br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, dm.qty, dm.hrg_jual', 'barang as br', 'br.kd_brg = dm.kd_brg', "dm.no_faktur_mutasi = '".$row['no_faktur_mutasi']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=($rows['barcode']==$rows['kd_brg']?$rows['Deskripsi']:$rows['barcode'])?></td>
                <td><?=$rows['nm_brg']?></td>
                <td class="text-center"><?=(int)$rows['qty']?></td>
                <td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
            </tr>
            <?php
            $total = $total + (int)$rows['qty'];
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" style="border-top: solid; border-width: thin">TOTAL</td>
            <td class="text-center" style="border-top: solid; border-width: thin"><?=$total?></td>
            <td style="border-top: solid; border-width: thin"></td>
        </tr>
        </tfoot>
    </table>
    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin;" width="33%"></td>
            <td style="border-top: solid; border-width: thin;" width="33%"></td>
            <td style="border-top: solid; border-width: thin;" width="33%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:center;">
                <br/>Pengirim<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Mengetahui<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Penerima<br/><br/><br/>_____________
            </td>
        </tr>
        </tbody>
    </table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_faktur_mutasi'])?></span>
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
        //add_activity('<?=$row['no_faktur_mutasi']?>');
        printDiv('print_3ply');
    });
</script>