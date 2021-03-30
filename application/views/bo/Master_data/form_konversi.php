<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->           
<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">

			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="pull-left page-title"><?=$title?></h4>
					<ol class="breadcrumb pull-right">
						<li><a href="<?=base_url()?>"><?=$site->title?></a></li>
						<li class="active"><?=$title?></li>
					</ol>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
						</div>
						<div class="panel-body">
							<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
							<?=form_open($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kode barang 1</label>
									<div class="col-lg-10">
										<?php $field = 'kd_brg1'; ?>
										<input class="form-control autocomplete_data" type="text" onblur="cek_data('<?=$table?>','<?=$field?>', 'error', 'Kode sudah digunakan. Ganti kode lain!')" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kode Barang 2</label>
									<div class="col-lg-10">
										<?php $field = 'kd_brg2'; ?>
										<input class="form-control autocomplete_data" type="text"  id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nilai Konversi</label>
									<div class="col-lg-10">
										<?php $field = 'nilai_konversi'; ?>
										<input class="form-control" type="number" step="any" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]+0:set_value($field)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								
								<div class="form-group">
									<div class="col-lg-offset-2 col-lg-10">
										<button class="btn btn-primary waves-effect waves-light" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
									</div>
								</div>
								
							<?=form_close()?>
						</div>
					</div>
				</div>
				
			</div> <!-- End Row -->
			
		</div> <!-- container -->
				  
	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here --> 
<!-- ============================================================== -->

<script>
var site = "<?=site_url()?>";
$(function(){
	$('.autocomplete_data').autocomplete({
		serviceUrl: site+'site/search_autocomplete/barang/kd_brg-nm_brg/kd_brg-nm_brg',
	});	
});

function cek_data(table, column, tipe, pesan){
	var id = $('#'+column).val();
	if(id!=''){
		$.ajax({
			//type:'POST',
			url:'<?=site_url()?>site/cek_data/' + table + '/' + column + '/' + id,
			//data: {delete_id : id},
			success: function (data) { 
				if(data==1){ 
					alert(pesan);
					//if(tipe=='error'){ alert('error'); }
					//else if(tipe=='warning'){ alert('warning'); }
				}
			},
			error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
		});
	}
}
</script>


