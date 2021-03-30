<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqD1Z03FoLnIGJTbpAgRvjcchrR-NiICk&libraries=places" async defer></script>
<style>
    #map {
        height: 400px;
        width: 100%;
    }

    #description {
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
    }

    #infowindow-content .title {
        font-weight: bold;
    }

    #infowindow-content {
        display: none;
    }

    #map #infowindow-content {
        display: inline;
    }

    .pac-card {
        margin: 10px 10px 0 0;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        background-color: #fff;
        font-family: Roboto;
    }

    #pac-container {
        padding-bottom: 12px;
        margin-right: 12px;
    }

    .pac-controls {
        display: inline-block;
        padding: 5px 11px;
    }

    .pac-controls label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }

    #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 100%;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    #title {
        color: #fff;
        background-color: #4d90fe;
        font-size: 25px;
        font-weight: 500;
        padding: 6px 12px;
    }
    #target {
        width: 345px;
    }
    .pac-container {
        background-color: #FFF;
        z-index: 1050;
        position: fixed;
        display: inline-block;
        float: left;
    }
</style>
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
							<?=form_open_multipart($this->control.'/'.$page.$update, array('class'=>"cmxform form-horizontal tasi-form"))?>
							<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama Lokasi</label>
									<div class="col-lg-10">
										<?php $field = 'Nama'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <!--<div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Nama Toko</label>
                                    <div class="col-lg-10">
                                        <?php /*$field = 'nama_toko'; */?>
                                        <input class="form-control" type="text" name="<?/*=$field*/?>" value="<?/*=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)*/?>" required aria-required="true" />
                                        <?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
                                    </div>
                                </div>-->
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kategori</label>
									<div class="col-lg-10">
										<?php $field = 'lokasi_ktg';
										$option = null; $option[''] = '-- Kategori Lokasi --';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('lokasi_ktg', 'id_lokasi_ktg, nama', null, 'nama asc');
										foreach($data_option as $row){ $option[$row['id_lokasi_ktg']] = $row['nama']; }
										echo form_dropdown($field, $option, set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
										?>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Footer 1</label>
									<div class="col-lg-10">
										<?php $field = 'Footer1'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Footer 2</label>
									<div class="col-lg-10">
										<?php $field = 'Footer2'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Footer 3</label>
									<div class="col-lg-10">
										<?php $field = 'Footer3'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Footer 4</label>
									<div class="col-lg-10">
										<?php $field = 'Footer4'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Kota</label>
									<div class="col-lg-10">
										<?php $field = 'kota'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Email</label>
									<div class="col-lg-10">
										<?php $field = 'email'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Web</label>
									<div class="col-lg-10">
										<?php $field = 'web'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Telepon</label>
									<div class="col-lg-10">
										<?php $field = 'phone'; ?>
										<input class="form-control" type="text" name="<?=$field?>" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>" required aria-required="true" />	
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
                                <div class="form-group " style="margin-bottom:5px;">
                                    <label class="control-label col-lg-2">Tampil Di App Member</label>
                                    <div class="col-lg-10 form-inline">
                                        <?php $field = 'status_show'; ?>
                                        <div class="radio radio-primary">
                                            <input class="form-control" type="radio" id="<?=$field?>1" name="<?=$field?>" value="1" <?=((isset($master_data[$field])&&$master_data[$field]==1)?'checked':null)?> required />
                                            <label for="<?=$field?>1"> Tampil </label>
                                        </div>
                                        <div class="radio radio-primary">
                                            <input class="form-control" type="radio" id="<?=$field?>0" name="<?=$field?>" value="0" <?=((isset($master_data[$field])&&$master_data[$field]==0)?'checked':null)?> required />
                                            <label for="<?=$field?>0"> Tidak Tampil </label>
                                        </div>
                                        <?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
                                    </div>
                                </div>
								<!--<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Server</label>
									<div class="col-lg-10">
										<?php /*$field = 'server'; */?>
										<input class="form-control" type="text" name="<?/*=$field*/?>" value="<?/*=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)*/?>" required aria-required="true" />
										<?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
									</div>
								</div>
								<div class="form-group " style="margin-bottom:5px;">
									<label class="control-label col-lg-2">Nama Database</label>
									<div class="col-lg-10">
										<?php /*$field = 'db_name'; */?>
										<input class="form-control" type="text" name="<?/*=$field*/?>" value="<?/*=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)*/?>" required aria-required="true" />
										<?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
									</div>
								</div>-->
                                <div class="row" style="margin-bottom:5px;">
                                    <label class="col-lg-2 control-label">Gambar</label>
                                    <div class="col-lg-10">
                                        <?php if(isset($master_data['gambar']) && $master_data['gambar']!=null && $master_data['gambar']!='-'){ ?>
                                            <input type="hidden" name="logo_gambar" value="<?=$master_data['gambar']?>">
                                            <img width="200" src="<?=base_url().$master_data['gambar']?>" />
                                        <?php } ?>
                                        <input type="file" name="gambar" id="gambar" />
                                        <font color='red'><?php if(isset($error_logo)){ echo $error_logo; } ?></font>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Cari Lokasi</label>

                                    <div class="col-sm-10">
                                        <input id="pac-input" class="controls form-control" type="text" placeholder="Cari Lokasi / Tandai Di Peta">
                                        <div id="map"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php $field = 'Ket'; ?>
                                    <label for="<?=$field?>" class="col-sm-2 control-label">Alamat</label>

                                    <div class="col-sm-10">
                                        <textarea type="text" name="<?=$field?>" class="form-control" id="<?=$field?>" rows="4" autocomplete="off" placeholder="Alamat"><?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?></textarea>
                                    </div>
                                </div>
                                <?php $field = 'lng'; ?>
                                <input type="hidden" name="lng" id="lng" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
                                <?php $field = 'lat'; ?>
                                <input type="hidden" name="lat" id="lat" value="<?=set_value($field)?set_value($field):(isset($master_data[$field])?$master_data[$field]:null)?>">
                                <input type="hidden" name="param" id="param" value="add">
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
$("#pac-input").keypress(function (e) {
    if (e.keyCode == 13) {
        return false;
    }
});

function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}

function cek_data(table, column, tipe, pesan){
    var id = $('#'+column).val();
    if(id!=''){
        $.ajax({
            type:'GET',
            url:'<?=site_url()?>site/cek_data_2/' + btoa(table) + '/' + btoa(column) + '/' + btoa(id),
            //data: {delete_id : id},
            success: function (data) {
                if(data==1){
                    $("#ntf_"+column).text(pesan);
                    //if(tipe=='error'){ alert('error'); }
                    //else if(tipe=='warning'){ alert('warning'); }
                } else {
                    $("#ntf_"+column).text('');
                }
            },
            error: function (jqXHR, textStatus, errorThrown){ alert('Check Data Failed'); }
        });
    }
}

function initMap(zoom_=14, lat_=-6.9228583, lng_=107.6058134, id_='map', param_='edit') {
    var uluru = {lat: lat_, lng: lng_};
    var map = new google.maps.Map(document.getElementById(id_), {
        zoom: zoom_,
        center: uluru
    });

    var geocoder = new google.maps.Geocoder;

    var marker = new google.maps.Marker({
        map: map
    });

    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    //map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
    });

    var markers = [];
    // Listen for the event fired when the user selects a prediction and retrieve
    // more details for that place.

    function clear_map() {
        markers.forEach(function(marker) {
            marker.setMap(null);
        });
        markers = [];

        if (marker && marker.setMap) {
            marker.setMap(null);
        }
    }

    searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // Clear out the old markers.
        clear_map();

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function(place) {
            if (!place.geometry) {
                console.log("Returned place contains no geometry");
                return;
            }

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
                map: map,
                title: place.name,
                position: place.geometry.location
            }));

            if (place.geometry.viewport) {
                // Only geocodes have viewport.
                bounds.union(place.geometry.viewport);
                $("#Ket").val(place.formatted_address);
                $("#lat").val(place.geometry.location.lat());
                $("#lng").val(place.geometry.location.lng());
            } else {
                bounds.extend(place.geometry.location);
            }
        });
        map.fitBounds(bounds);
    });

    if (param_ == 'set' || $("#param").val()=='edit') {
        marker.setPosition(uluru);
    }

    google.maps.event.addListener(map, 'click', function(e) {
        if (param_ == 'edit') {
            var latLng = e.latLng;

            $("#lat").val(latLng.lat());
            $("#lng").val(latLng.lng());

            clear_map();

            marker = new google.maps.Marker({
                position: latLng,
                map: map
            });

            geocoder.geocode({
                'latLng': latLng
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        $("#Ket").val(results[0].formatted_address);
                        $("#pac-input").val('');
                    }
                }
            });
        }
    });
}

$(document).ready(function () {
    if (<?=isset($_GET['trx'])?>) {
        $("#param").val("edit");
        initMap(18, parseFloat(<?=$master_data['lat']?>), parseFloat(<?=$master_data['lng']?>), 'map', 'edit');
    } else {
        initMap();
    }
});
</script>