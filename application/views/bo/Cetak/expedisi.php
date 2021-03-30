<div id="print_3ply" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['kd_expedisi']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center;">Expedisi Barang</td>
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
            <td><?=substr($row['tgl_expedisi'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$row['nama_operator']?></td>
        </tr>
        <tr>
            <th></th>
            <td>Kode Expedisi</td>
            <td>:</td>
            <td><?=$row['kd_expedisi']?></td>

            <td></td>
            <td>Pengirim</td>
            <td>:</td>
            <td><?=$row['pengirim']?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Asal</td>
            <td>:</td>
            <td><?=$row['nama_lokasi_asal']?></td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Tujuan</td>
            <td>:</td>
            <td><?=$row['nama_lokasi_tujuan']?></td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">No</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Kode Packing</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Kode Mutasi</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Nama Supp/Jns Brg</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Jumlah Koli</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" colspan="3">Menerima</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Keterangan</td>
        </tr>
        <tr>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">BRG</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">SJ</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">TTD</td>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0;
        $detail = $this->m_crud->join_data('det_expedisi de', 'de.*, mp.no_faktur_mutasi', array('master_packing mp'), array('mp.kd_packing=de.kd_packing'), "de.kd_expedisi = '".$row['kd_expedisi']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_packing']?></td>
                <td><?=$rows['no_faktur_mutasi']?></td>
                <td><?=$rows['ket']?></td>
                <td><?=$rows['jml_koli']?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php
        } ?>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:center;">
                <br/>Operator<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Supir<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Checker<br/><br/><br/>_____________
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
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['kd_expedisi'])?></span>
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
        //add_activity('<?=$row['kd_expedisi']?>');
        printDiv('print_3ply');
    });
</script>