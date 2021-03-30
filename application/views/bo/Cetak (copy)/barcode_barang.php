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
        'canvas'=>array('width'=>110,'margin_top'=>0.5,'margin_right'=>0,'margin_bottom'=>0,'margin_left'=>0,'border'=>0),
        'label'=>array('width'=>32,'height'=>14,'margin_top'=>-1.5,'margin_right'=>2,'margin_bottom'=>0,'margin_left'=>1.5,'border'=>0),
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
                        JsBarcode("#brcd<?=$row?>", "<?=ltrim(rtrim(substr($value['barcode'], 0, 20)))?>", {
                            width: 1.1,
                            margin: 0,
                            background: "transparent",
                            textAlign: "left",
                            textMargin: 0,
                            height: 15,
                            fontSize: 10,
                            fontOptions: "bold",
                            font: "arial"
                        });
                    </script>
                    <div id="<?=$divbarcode?>" class="<?=$divbarcode?>"></div><canvas id="<?=$canvasbarcode?>" width="170" height="100"></canvas>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:1mm; font-size:8px; color:black;">
                    <b><?=$value['nm_brg']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:10.5mm; font-size:11px; color:black;">
                    Rp. <b style="font-family: 'Arial Black', 'Arial Bold', Gadget; font-weight: 900"><?=number_format($value['hrg_jual'])?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:22mm; margin-top:8mm; font-size:6px; color:black;">
                    <b><?=$value['kel_brg']?></b>
                </div>
                <div class="label_colom" draggable="true" id="dragme" style="margin-left:22mm; margin-top:10mm; font-size:6px; color:black;">
                    <b><?=$value['gr1']?></b>
                </div>
            </div>
            <!--end label-->
            <?php if((($row+1) % 3) == 0){ ?><div style="clear:both;"></div><?php } ?>
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