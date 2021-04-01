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
        'canvas'=>array('width'=>100,'margin_top'=>0,'margin_right'=>0,'margin_bottom'=>0,'margin_left'=>2,'border'=>0),
        'label'=>array('width'=>5,'height'=>3,'margin_top'=>2,'margin_right'=>2,'margin_bottom'=>2,'margin_left'=>2,'border'=>2),
        'barcode'=>array('btype'=>'code128','width'=>1,'height'=>15,'fontSize'=>8,'renderer'=>'css','bcolor'=>'transparant','fcolor'=>'#000000','showHRI'=>'true','marginHRI'=>5,'addQuietZone'=>0,'quietZone'=>'false','rectangular'=>'false','posx'=>10,'posy'=>20)
    ); ?>
    <div style="width:<?=$barcode['canvas']['width']?>%; margin:<?=$barcode['canvas']['margin_top']?>mm <?=$barcode['canvas']['margin_right']?>mm <?=$barcode['canvas']['margin_bottom']?>mm <?=$barcode['canvas']['margin_left']?>mm; border: <?=$barcode['canvas']['border']?>px solid black;">
		<table>
			<tbody>
			<?php foreach($barang as $row => $value){ ?>
				<?php if((($row) % 4)==0 || $row==0){ ?><tr><?php } ?>
					<?php $barcode['barcode']['value'] = $value['barcode']; ?>
					<td>
						<!--start label-->
						<div style="position:relative; float:left; width:<?=$barcode['label']['width']?>cm; height:<?=$barcode['label']['height']?>cm; margin:<?=$barcode['label']['margin_top']?>mm <?=$barcode['label']['margin_right']?>mm <?=$barcode['label']['margin_bottom']?>mm <?=$barcode['label']['margin_left']?>mm; border:<?=$barcode['label']['border']?>px solid; border-color:#0000ff #0000ff #0000ff #0000ff;"> <!--#0000ff #ff9900 #ff9900 #0000ff--> 
							<div class="label_colom" draggable="true" id="dragme" style="margin-left:1mm; margin-top:8mm; color:black;">
								<?php $divbarcode=$div.'barcode'.$row; $canvasbarcode=$div.'canvas'.$row; ?>
								<svg style="margin-left: 1mm" id="brcd<?=$row?>"></svg>
								<script>
									JsBarcode("#brcd<?=$row?>", "<?=substr($value['barcode'], 0, 20)?>", {
										width: 1.1,
										margin: 0,
										background: "transparent",
										textAlign: "left",
										textMargin: 0,
										height: 18,
										fontSize: 11,
										fontOptions: "bold",
										font: "arial"
									});
								</script>
								<div id="<?=$divbarcode?>" class="<?=$divbarcode?>"></div><canvas id="<?=$canvasbarcode?>" width="170" height="100"></canvas>
							</div>
							<div class="label_colom" draggable="true" id="dragme" style="margin-left:2mm; margin-top:2mm; font-size:16px; color:black;">
								<b><?=substr($value['nm_brg'],0,17)?></b>
							</div> -->
							<div class="label_colom" draggable="true" id="dragme" style="margin-left:2mm; margin-top:23mm; font-size:12px; color:black;">
								<b><?=$value['kd_brg']?></b>
							</div>
							<div class="label_colom" draggable="true" id="dragme" style="margin-left:2mm; margin-top:15mm; font-size:23px; color:black;">
								Rp. <b><?=number_format($value['hrg_jual'])?></b>
							</div>
							<div class="label_colom" draggable="true" id="dragme" style="margin-left:29mm; margin-top:23mm; font-size:0px; color:black;">
								<?=$this->m_website->logo('100%', '20px')?>
							</div>
						</div>
						<!--end label-->
					</td>
				<?php if((($row+1) % 4) == 0){ ?></tr><?php } ?>
				<?php /*if((($row+1) % 2) == 0){ ?><div style="clear:both;"></div><?php } ?>
				<?php if((($row+1) % 12) == 0){ ?><br /><br />><?php }*/ ?>
			<?php } ?>
			</tbody>
		</table>
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