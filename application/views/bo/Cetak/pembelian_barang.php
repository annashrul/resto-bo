<div id="print_3ply" class="hidden">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_faktur_beli']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center">Laporan Arsip Pembelian (<?=$row['no_faktur_beli']?>)</td>
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
            <td style="font-size: 10pt !important">Tanggal</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=substr($row['tgl_beli'],0,10)?></td>

            <td></td>
            <td style="font-size: 10pt !important">Operator</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['operator']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Pelunasan</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['Pelunasan']?></td>

            <td></td>
            <td style="font-size: 10pt !important">Penerima</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['nama_penerima']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Lokasi</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['lokasi']?></td>

            <td></td>
            <td style="font-size: 10pt !important"></td>
            <td style="font-size: 10pt !important"></td>
            <td style="font-size: 10pt !important"></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">No</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Kode Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Nama Barang</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Beli</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Harga Jual</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Margin</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Diskon 1 %</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Diskon 2 %</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty Beli</td>
            <!--<td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty Retur</td>-->
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Qty Bonus</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Ppn</td>
            <td style="border-bottom: solid; border-width: thin; padding-left: 5pt; font-size: 10pt !important">Sub Total</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $no = 0;
        $sub_total = 0;
        $detail = $this->db->query("SELECT isnull(br.hrg_jual_1 ,0) harga_jual, db.kode_barang, br.nm_brg, db.jumlah_beli, isnull(db.jumlah_bonus, 0) jumlah_bonus, db.harga_beli, db.ppn ppn_item, db.diskon disc1, db.disc2, db.disc3, db.disc4, br.satuan, isnull(dr.jml,0) jumlah_retur
										FROM master_beli mb
										LEFT JOIN det_beli db ON db.no_faktur_beli=mb.no_faktur_beli 
										LEFT JOIN barang br ON db.kode_barang = br.kd_brg
										LEFT JOIN Master_Retur_Beli mr ON db.no_faktur_beli=mr.no_beli
										LEFT JOIN Det_Retur_Beli dr ON dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang
										WHERE mb.no_faktur_beli = '".$row['no_faktur_beli']."'")->result_array();
        $qt = 0;
        $qr = 0;
        $qb = 0;
        $st = 0;
        foreach($detail as $rows){
            $no++;
            $hitung_netto = ((float)$rows['jumlah_beli']) * (float)$rows['harga_beli'];
            $diskon = $this->m_website->double_diskon($hitung_netto, array($rows['disc1'], $rows['disc2']));
            $hitung_sub_total = $this->m_website->grand_total_ppn($diskon, 0, $rows['ppn_item']);
            $sub_total = $sub_total + $hitung_sub_total;
            $d1 = $rows['harga_beli']*(1-($rows['disc1']/100));
            $hrg_beli = $d1*(1-($rows['disc2']/100));
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kode_barang']?></td>
                <td><?=$rows['nm_brg']?></td>
                <td class="text-right"><?=number_format($rows['harga_beli'], 2)?></td>
                <td class="text-right"><?=number_format($rows['harga_jual'], 2)?></td>
                <td class="text-center"><?=(($rows['harga_jual']!=0)?round((1 - ($hrg_beli/$rows['harga_jual']))*100, 2):'0')?> %</td>
                <td style="text-align: center"><?=($rows['disc1']+0)?></td>
                <td style="text-align: center"><?=($rows['disc2']+0)?></td>
                <td><?=(float)$rows['jumlah_beli'].' '.$rows['satuan']?></td>
                <!--<td><?/*=(int)$rows['jumlah_retur'].' '.$rows['satuan']*/?></td>-->
                <td><?=(int)$rows['jumlah_bonus'].' '.$rows['satuan']?></td>
                <td><?=($rows['ppn_item']+0)?></td>
                <td class="text-right"><?=number_format($hitung_sub_total, 2)?></td>
            </tr>
            <?php
            $qt = $qt + (float)$rows['jumlah_beli'];
            /*$qr = $qr + (float)$rows['jumlah_retur'];*/
            $qb = $qb + (int)$rows['jumlah_bonus'];
            $st = $st + $hitung_sub_total;
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="border-top: solid; border-width: thin" colspan="8">TOTAL</td>
            <td style="border-top: solid; border-width: thin"><?=$qt?></td>
            <!--<td style="border-top: solid; border-width: thin"><?/*=$qr*/?></td>-->
            <td style="border-top: solid; border-width: thin"><?=$qb?></td>
            <td style="border-top: solid; border-width: thin"></td>
            <td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($st, 2)?></td>
        </tr>
        <tr>
            <td style="border-top: solid; border-width: thin" colspan="11">DISKON</td>
            <td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($row['disc'], 2)?></td>
        </tr>
        <tr>
            <td style="border-top: solid; border-width: thin" colspan="11">PPN</td>
            <td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($row['ppn'], 2)?></td>
        </tr>
        <tr>
            <td style="border-top: solid; border-width: thin" colspan="11">GRAND TOTAL</td>
            <td style="border-top: solid; border-width: thin" class="text-right"><?=number_format($st-$row['disc']+$row['ppn'], 2)?></td>
        </tr>
        </tfoot>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
            <td style="border-top: solid; border-width: thin" width="33%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:center;">
            </td>
            <td style="text-align:center;">
            </td>
            <td style="text-align:center;">
                <b><br/><br/><br/><br/>_____________</b>
            </td>
        </tr>
        </tbody>
    </table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_faktur_beli'])?></span>
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
        //add_activity('<?=$row['no_faktur_beli']?>');
        printDiv('print_3ply');
    });
</script>