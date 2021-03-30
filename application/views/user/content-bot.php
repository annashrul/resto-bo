<footer class="main-footer no-print">
	<!-- To the right -->
	<div class="pull-right hidden-xs"></div>
	<!-- Default to the left -->
	<strong>Copyright &copy; <?php echo date('Y'); echo ' '.$site->title; ?></strong> | All rights reserved.
</footer>
	  
</body>

<script src="<?=base_url()?>assets/DataTables/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>assets/DataTables/dataTables.bootstrap.min.js"></script>
<script src="<?=base_url()?>assets/adminLTE/plugins/select2/select2.full.min.js"></script>
<script>
$(function () {
//Initialize Select2 Elements
	$(".select2").select2();
});

function to_rp(angka){
	if(angka != '' || angka != 0){
		var rev     = parseInt(angka, 10).toString().split('').reverse().join('');
		var rev2    = '';
		for(var i = 0; i < rev.length; i++){
			rev2  += rev[i];
			if((i + 1) % 3 === 0 && i !== (rev.length - 1)){
				rev2 += ',';
			}
		}
		
		var dec		= parseFloat(angka, 10).toString().split('.');
		if(dec[1] > 0){ dec = dec[1]; } else { dec = '00'; }
		
		//return 'IDR : ' + rev2.split('').reverse().join('') + ',-';
		return rev2.split('').reverse().join('') + '.' + dec;
	} else {
		//return 'IDR : ';
		return '0';
	}
}

$(function () {
	var mundur = 1 + <?=$this->m_website->selisih_hari($this->m_crud->max_data('acc_periode', 'tanggal_akhir', "status = 4 and lokasi = '".$this->m_website->get_lokasi()."'"), date('Y-m-d'))?>;
	$('.datepicker_date').datepicker({
		format: 'yyyy-mm-dd',
	});
	$('.datepicker_back').datepicker({
		format: 'yyyy-mm-dd',
		startDate: mundur+'d',
		endDate: '+0d'
	});
	$('.datepicker_front').datepicker({
		format: 'yyyy-mm-dd',
		startDate: '-0d'
	});
	$('.datepicker_front_back').datepicker({
		format: 'yyyy-mm-dd',
		startDate: mundur+'d'
	});
});

$(function () {
	$("#example").DataTable();
	$("#example1").DataTable();
    $('#example2').DataTable({
		"paging": false,
		"lengthChange": true,
		"searching": true,
		"ordering": true,
		"info": false,
		"autoWidth": true
    });
});
</script>

</html>