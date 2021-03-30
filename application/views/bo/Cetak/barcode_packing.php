<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('bo/head'); ?>
    <style>
        footer {
            display: none !important;
        }
    </style>
</head>
<body>
<?php $div='printpacking'; ?>
<div id="<?=$div?>">
    <div style="margin:0mm 0mm 0mm 0mm; width:104mm; border: 0px solid black;">
        <?php $packing = array(
            0 => array('barcode'=>$data['kd_packing'], 'operator'=>$data['operator'], 'jumlah_barang'=> $data['jumlah_barang'], 'total_barang'=>$data['total_barang'], 'pengirim'=>$data['pengirim'], 'tgl'=>substr($data['tgl_packing'],0,19), 'faktur_mutasi'=>$data['faktur_mutasi'], 'box'=>$data['box'], 'lokasi_1'=>$data['lokasi_1'], 'lokasi_2'=>$data['lokasi_2'])
        ); ?>
    <?php foreach($packing as $row => $value){ ?>
        <div style="position:relative; float:left; margin:0mm 0mm 0mm 0mm; border:0px solid black; width:102mm; height:50mm;">
            <center>
                <div draggable="true" id="dragme" style="margin-left:0mm; margin-top:4mm; width:100%; font-size:7px; color:black;">
                    <?php $divbarcode=$div.'barcode'.$row; $canvasbarcode=$div.'1canvas'.$row; ?>
                    <script>
                        $(function(){
                            generateBarcode({'divbarcode':'<?=$divbarcode?>', 'canvasbarcode':'<?=$canvasbarcode?>', 'value':'<?=$value['barcode']?>', 'width':2, 'height':75, 'fontSize':14, 'addQuietZone':0});
                        });
                    </script>
                    <div id="<?=$divbarcode?>" class="<?=$divbarcode?>"></div><canvas id="<?=$canvasbarcode?>" width="170" height="100"></canvas>
                </div>
            </center>
            <div style="width: 100%; font-size: 8pt">
                <div style="width: 57%; float: left">
                    <div style="margin-left: 0.2cm">
                        Faktur : <?=$value['faktur_mutasi'].'<br>'?>
                        Tanggal : <?=substr($value['tgl'],0,19).'<br>'?>
						<div style="font-size:10pt">
                        Dari : <?=$value['lokasi_1']?><br>
			Ke : <?=$value['lokasi_2']?><br>
						</div>
                    </div>
                </div>
                <div style="width: 43%; float: left">
                    <div style="margin-right: 0.2cm">
                        Pengirim : <?=$value['pengirim'].'<br>'?>
                        Operator : <?=$value['operator'].'<br>'?>
                        Barang / Qty : <?=(int)$value['jumlah_barang'].' / '.(int)$value['total_barang'].'<br>'?>
						<div style="font-size:12pt">
                        No Box : <?=(int)$value['box']?>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <?php if((($row+1) % 1) == 0){ ?><div style="clear:both;"></div><?php } ?>
    <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function () {
       printDiv('printpacking');
    });
</script>

<?php $this->load->view('bo/footer'); ?>

</body>
</html>
