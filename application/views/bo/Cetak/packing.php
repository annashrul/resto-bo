<div id="print_3ply" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['kd_packing']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center">Packing Alokasi (<?=$row['kd_packing'].' / '.$row['no_faktur_mutasi']?>)</td>
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
            <td>Tanggal Packing</td>
            <td>:</td>
            <td><?=substr($row['tgl_packing'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$this->m_website->get_nama_user($row['operator'])?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Dari-Ke</td>
            <td>:</td>
            <td><?=$row['kd_lokasi_1']?> - <?=$row['kd_lokasi_2']?></td>

            <td></td>
            <td>Pengirim</td>
            <td>:</td>
            <td><?=$row['pengirim']?></td>
        </tr>
        <tr>
            <th></th>
            <td></td>
            <td></td>
            <td></td>

            <td></td>
            <td>Status</td>
            <td>:</td>
            <td><?php if($row['status']=='0'){if($row['expedisi']=='0'){echo 'Processing';}else{echo 'Sending';}}else if($row['status']=='1'){echo 'Received In Part';}else {echo 'Received';} ?></td>
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
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Status</td>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0; $total = 0;
        $detail = $this->m_crud->read_data("det_packing dp, barang br","dp.qty, br.kd_brg, br.barcode, br.nm_brg, dp.status, (SELECT hrg_jual FROM Det_Mutasi WHERE no_faktur_mutasi='".$row['no_faktur_mutasi']."' AND kd_brg=br.kd_brg GROUP BY hrg_jual) hrg_jual", "dp.kd_brg=br.kd_brg AND dp.kd_packing = '".$row['kd_packing']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=$rows['barcode']?></td>
                <td><?=$rows['nm_brg']?></td>
                <td class="text-center"><?=(int)$rows['qty']?></td>
                <td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
                <td><?=($rows['status']=='0')?($row['expedisi']=='0'?'Processing':'Sending'):'Received'?></td>
            </tr>
            <?php
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" style="border-top: solid; border-width: thin">TOTAL</td>
            <td class="text-center" style="border-top: solid; border-width: thin"><?=$tqty?></td>
            <td style="border-top: solid; border-width: thin"></td>
            <td style="border-top: solid; border-width: thin"></td>
        </tr>
        </tfoot>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin;" width="25%"></td>
            <td style="border-top: solid; border-width: thin;" width="25%"></td>
            <td style="border-top: solid; border-width: thin;" width="25%"></td>
            <td style="border-top: solid; border-width: thin;" width="25%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:center;">
                <br/>Packing<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Pengirim<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Penerima<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Mengetahui<br/><br/><br/>_____________
            </td>
        </tr>
        </tbody>
    </table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['kd_packing'])?></span>
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
        //add_activity('<?=$row['kd_packing']?>');
        printDiv('print_3ply');
    });
</script>