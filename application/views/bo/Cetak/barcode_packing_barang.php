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
<?php $barang = $data_barcode;?>
<!--start area print-->
<?php $div='printbarcode'; ?>
<div id="<?=$div?>">
    <!--start canvas/ukuran kertas-->
    <?php $barcode = array(
        'canvas'=>array('width'=>104,'margin_top'=>0,'margin_right'=>0,'margin_bottom'=>0,'margin_left'=>10,'border'=>0),
        'label'=>array('width'=>102,'height'=>50,'margin_top'=>0,'margin_right'=>0,'margin_bottom'=>0,'margin_left'=>0,'border'=>0),
        'barcode'=>array('btype'=>'code128','width'=>1,'height'=>15,'fontSize'=>8,'renderer'=>'css','bcolor'=>'transparant','fcolor'=>'#000000','showHRI'=>'true','marginHRI'=>5,'addQuietZone'=>0,'quietZone'=>'false','rectangular'=>'false','posx'=>10,'posy'=>20)
    ); ?>
    <div style="width:<?=$barcode['canvas']['width']?>mm; margin:<?=$barcode['canvas']['margin_top']?>mm <?=$barcode['canvas']['margin_right']?>mm <?=$barcode['canvas']['margin_bottom']?>mm <?=$barcode['canvas']['margin_left']?>mm; border: <?=$barcode['canvas']['border']?>px solid black;">
        <?php foreach($barang as $row => $value){
            $barcode['barcode']['value'] = $value['barcode'];
            ?>
            <!--start label-->
            <div style="position:relative; float:left; width:<?=$barcode['label']['width']?>mm; height:<?=$barcode['label']['height']?>mm; margin:<?=$barcode['label']['margin_top']?>mm <?=$barcode['label']['margin_right']?>mm <?=$barcode['label']['margin_bottom']?>mm <?=$barcode['label']['margin_left']?>mm; border:<?=$barcode['label']['border']?>px solid black;">
                &nbsp;
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:4mm; color:black;">
                    <?php $divbarcode=$div.'barcode'.$row; $canvasbarcode=$div.'canvas'.$row; ?>
                    <svg style="margin-left: 1mm" id="brcd<?=$row?>"></svg>
                    <script>
                        JsBarcode("#brcd<?=$row?>", "<?=substr($value['packing'], 0, 20)?>", {
                            width: 1.5,
                            margin: 0,
                            background: "transparent",
                            textAlign: "center",
                            textMargin: 0,
                            height: 50,
                            fontSize: 12,
                            fontOptions: "bold",
                            font: "arial"
                        });
                    </script>
                    <div id="<?=$divbarcode?>" class="<?=$divbarcode?>"></div><canvas id="<?=$canvasbarcode?>" width="170" height="100"></canvas>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:0mm; font-size:14px; font-weight: bold; color:black;">
                    <b><?=$value['nm_brg']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:20mm; font-size:14px; font-weight: bold; color:black;">
                    <b>SKU: <?=$value['kd_brg']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:25mm; font-size:14px; font-weight: bold; color:black;">
                    <b>BC: <?=$value['barcode']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:30mm; font-size:14px; font-weight: bold; color:black;">
                    <b>ART: <?=$value['art']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:55mm; margin-top:10mm; font-size:14px; font-weight: bold; color:black;">
                    <b>SUPP: <?=$value['gr1']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:55mm; margin-top:15mm; font-size:14px; font-weight: bold; color:black;">
                    <b>LOKASI:</b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:55mm; margin-top:20mm; font-size:14px; font-weight: bold; color:black;">
                    <b>PAK: <?=(int)$value['qty_packing']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:50mm; margin-top:35mm; font-size:10px; font-weight: bold; color:black;">
                    <b>Printed on <?=date('Y-m-d H:i:s')?></b>
                </div>
            </div>
            <!--end label-->
            <?php if((($row+1) % 1) == 0){ ?><div style="clear:both;"></div><?php } ?>
        <?php } ?>
    </div>
    <!--end canvas/ukuran kertas-->
</div>
<!--end area print-->

<script>
    $(document).ready(function () {
        printDiv('printbarcode');
    });
</script>

<?php $this->load->view('bo/footer'); ?>

</body>
</html>