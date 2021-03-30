<div id="print_3ply" class="hidden">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_nota']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" class="text-center">Nota Bayar Hutang (<?=$row['no_faktur_beli']?>)</td>
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
            <td style="font-size: 10pt !important"><?=substr($row['tgl_byr'],0,10)?></td>

            <td></td>
            <td style="font-size: 10pt !important">Operator</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['kasir']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">No. Transaksi</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['no_nota']?></td>

            <td></td>
            <td style="font-size: 10pt !important">Jenis Pembayaran</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['cara_byr']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Nota Pembelian</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['fak_beli']?></td>

            <td></td>
            <td style="font-size: 10pt !important">Pembayaran</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=number_format($row['jumlah'],2)?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Tanggal Jatuh Tempo</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=substr($row['tgl_jatuh_tempo'],0,10)?></td>

            <td></td>
            <td style="font-size: 10pt !important">Pembulatan</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=number_format($row['bulat'],2)?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Supplier</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['Nama']?></td>

            <td></td>
            <td style="font-size: 10pt !important">Cek/Giro</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['nogiro']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Bank</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['nm_bank']?></td>

            <td></td>
            <td style="font-size: 10pt !important">Keterangan</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=$row['ket']?></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size: 10pt !important">Tanggal Cair Giro</td>
            <td style="font-size: 10pt !important">:</td>
            <td style="font-size: 10pt !important"><?=substr($row['tgl_cair_giro'],0,10)?></td>

            <td></td>
            <td style="font-size: 10pt !important"></td>
            <td style="font-size: 10pt !important"></td>
            <td style="font-size: 10pt !important"></td>
        </tr>
        </tbody>
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
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_nota'])?></span>
</div>
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
        //add_activity('<?=$row['no_nota']?>');
        printDiv('print_3ply');
    });
</script>