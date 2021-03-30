<style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    .alert {
        margin-bottom: 10px;
        border: none;
    }
    .alert small {
        color: black;
        font-weight: bold;
    }
    .alert-success {
        background-color: #cef1de!important;
        color: #34b471!important;
    }
    .alert-success .icon2 path {
        fill:#34b471
    }
    .alert-success .icon2 polygon {
        fill:#34b471
    }
    .alert-danger {
        background-color: #f9e2e2!important;
        color: #d9534f!important;
    }
    .alert-danger .icon2 path {
        fill:#d9534f
    }
    .alert-danger .icon2 polygon {
        fill:#d9534f
    }
    .lnr {
        font-family: "li Linearicons-Free";
        speak: none;
        font-style: normal;
        font-weight: 400;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .order-items {
        border-bottom: 1px dashed #ddd;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    .payment-stts {
        margin-top: 20px;
        border-top: 1px dashed #2ea065;
        padding-top: 8px;
        font-size: 12px;
    }
    .med {
         font-size: 24px;
     }
    .mbtm-10 {
        margin-bottom: 10px;
    }
    .mtop-20 {
        margin-top: 20px;
    }
    .icon {
        max-width:24px;
        max-height:24px;
    }
    .icon2 {
        max-width:26px;
        max-height:26px;
        margin-right: 10px;
    }
    .tr-status ul {
        list-style:none;
        padding:0;
        margin-bottom:20px
    }
    .tr-status ul li {
        width:36px;
        height:36px;
        display:inline-block;
        border-radius:50%;
        margin-right:6px;
        margin-bottom:6px;
        text-align:center;
        padding-top:4px;
        border:2px solid #ddd;
        background-color:#fff
    }
    .tr-status ul li.done {
         border:2px solid #34b471
     }
    .tr-status ul li.done .icon path {
        fill:#34b471
    }
    .tr-status ul li.undone .icon path {
        fill:#ddd
    }
    .input-group-btn.input-space {
        padding-left: 15px;
    }
</style>
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title"><?=$main?></h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="<?=base_url()?>"><?=$site->title?></a></li>
                        <li class="active"><?=$title?></li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <h4 class="box-title"><?=$title?></h4>
                                </div>

                                <div class="col-md-2">
                                    <div class="box-tools">
                                        <select id="bank" class="form-control">
                                            <option value="">Filter Bank</option>
                                            <?php
                                            $read_bank = $this->m_crud->read_data("bank", "nama id_bank, nama");
                                            foreach ($read_bank as $row) {
                                                echo '<option '.($this->session->search['bank']==$row['id_bank']?'selected':'').' value="'.$row['id_bank'].'">'.$row['nama'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="box-tools">
                                        <div class="input-group">
                                            <span class="input-group-addon" data-toggle="tooltip" data-placement="top" title="Semua Periode">
                                                <input id="periode" name="periode" type="checkbox" value="1" <?=$this->session->search['periode']=='1'?'checked':''?>>
                                            </span>
                                            <input type="text" id="table_date" class="form-control daterange2" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="box-tools">
                                        <div class="input-group" style="width: 100%;">
                                            <input type="text" name="table_search" class="form-control pull-right" id="search" value="<?=isset($this->session->search['any'])?$this->session->search['any']:''?>" placeholder="Cari by Kode Order/Pemesan/Penerima">
                                            <div class="input-group-btn input-space">
                                                <button type="button" class="btn btn-primary bg-blue" onclick="load_data()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari"><i class="fa fa-search"></i></button>
                                            </div>
                                            <div class="input-group-btn input-space">
                                                <button type="button" class="btn btn-primary bg-blue" onclick="clear_session()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reset Filter"><i class="fa fa-close"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <ul class="tab-buttons">
                                            <li class="selected"><a href="javascript:" onclick="load_data('belum_bayar')"><i class="fa fa-dollar"></i><span>Belum Bayar</span><span id="belum_bayar" class="font-status">0</span></a></li>
                                            <li class="selected2"><a href="javascript:" onclick="load_data('belum_proses')"><i class="fa fa-dropbox"></i><span>Belum Diproses</span><span id="belum_proses" class="font-status">0</span></a></li>
                                            <li class="selected3"><a href="javascript:" onclick="load_data('belum_resi')"><i class="fa fa-file-text"></i><span>Belum Ada Resi</span><span id="belum_resi" class="font-status">0</span></a></li>
                                            <li class="selected4"><a href="javascript:" onclick="load_data('belum_lacak')"><i class="fa fa-search"></i><span>Belum Dilacak</span><span id="belum_lacak" class="font-status">0</span></a></li>
                                            <li class="selected5"><a href="javascript:" onclick="load_data('dalam_proses')"><i class="fa fa-truck"></i><span>Sedang Dikirim</span><span id="dalam_proses" class="font-status">0</span></a></li>
                                            <li class="selected6"><a href="javascript:" onclick="load_data('berhasil')"><i class="fa fa-check-circle-o"></i><span>Selesai</span><span id="berhasil" class="font-status">0</span></a></li>
                                        </ul><!--- END TAB MENU -->
                                    </div><!--- END COL -->
                                </div><!--- END ROW -->
                            </div>
                        </div>
                    </div>
                    <div id="result_order">
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <input id="print_selected" type="checkbox"><button class="btn btn-sm btn-info" onclick="print_label('selected')" style="margin-left: 10px"><span class="fa fa-print"></span> Print label pengiriman</button>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div align="center" id="pagination_link">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
isset($this->session->search['date'])?$date = $this->session->search['date']:$date='null';
?>
<script>
    $(document).ready(function(){
        load_data('<?=$this->session->search['filter']?>', 'load_first');
        if ('<?=$date?>' != 'null') {
            set_date('<?=$date?>', 'daterange2');
        }
    }).on("click", ".pagination li a", function(event){
        event.preventDefault();
        var page = $(this).data("ci-pagination-page");
        load_data('pagging', null, page);
    });

    function load_header() {
        $.ajax({
            url:"<?=base_url().$content.'/load_header';?>",
            method:"GET",
            dataType:"JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success:function(res)
            {
                $("#belum_bayar").text(res.belum_bayar);
                $("#belum_proses").text(res.belum_proses);
                $("#belum_resi").text(res.belum_resi);
                $("#belum_lacak").text(res.belum_lacak);
                $("#dalam_proses").text(res.dalam_proses);
                $("#berhasil").text(res.berhasil);
            }
        });
    }

    function load_data(filter_=null, param=null, page=1) {
        var date_ = '<?=$date?>';

        if ('<?=$date?>' == 'null') {
            date_ = '<?=date('Y-m-d').' - '.date('Y-m-d')?>';
        }

        if (param == null) {
            date_ = $("#table_date").val();
        }

        var periode_ = null;
        var checkBox = document.getElementById("periode");
        if (checkBox.checked == true){
            periode_ = checkBox.value;
        }

        $.ajax({
            url:"<?=base_url().$content.'/get_data/';?>" + page,
            type:"POST",
            data:{search: true, any: $("#search").val(), filter:filter_, date: date_, bank: $("#bank").val(), periode: periode_},
            dataType:"JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success:function(data)
            {
                load_header();
                $('#result_order').html(data.result_order);
                $('#pagination_link').html(data.pagination_link);
                to_svg();
            }
        });
    }

    function cari(e, val) {
        if (e.keyCode == 13) {
            load_data({search:true, any:val});
        }
    }

    function input_resi(id) {
        var resi = $("#data_resi"+replace_slash(id)).val();
        $.ajax({
            url: "<?=base_url().$content.'/input_resi';?>",
            type: "POST",
            data: {id_order: btoa(id), no_resi: resi},
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                if (res.status) {
                    swal(
                        'Berhasil!',
                        'Nomor resi berhasil disimpan',
                        'success'
                    ).then(function () {
                        load_status(JSON.stringify(res.res_orders));
                    })
                }
            }
        });
    }

    function lacak(id) {
        $.ajax({
            url: "<?=base_url().$content.'/lacak_resi';?>",
            type: "POST",
            data: {id_pengiriman: id},
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                if (res.status) {
                    swal(
                        'Berhasil!',
                        res.message,
                        'success'
                    ).then(function () {
                        load_status(JSON.stringify(res.res_orders));
                    })
                } else {
                    swal({
                        type: 'info',
                        title: 'Oops...',
                        text: res.message
                    })
                }
            }
        });
    }

    function bukti_tf(id) {
        $.ajax({
            url: "<?=base_url().'api/get_transfer'?>",
            type: "POST",
            data: {id_pembayaran: id},
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                if (res.status) {
                    sweetImage(res.res_transfer['gambar'], res.res_transfer['atas_nama']+' ('+res.res_transfer['bank']+'-'+res.res_transfer['no_rek']+') ~ Rp '+to_rp(res.res_transfer['total'], '-'))
                }
            }
        });
    }

    function verifikasi(id) {
        swal({
            title: 'Verifikasi Pembayaran?',
            text: "Anda akan memverifikasi pembayaran dengan nomor "+id,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "<?=base_url().$content.'/verifikasi';?>",
                    type: "POST",
                    data: {id_pembayaran: btoa(id)},
                    dataType: "JSON",
                    beforeSend: function() {
                        $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
                    },
                    complete: function() {
                        $('.first-loader').remove();
                    },
                    success: function (res) {
                        if (res.status) {
                            swal(
                                'Berhasil!',
                                'Pembayaran berhasil diverifikasi',
                                'success'
                            ).then(function () {
                                load_status(JSON.stringify(res.res_orders));
                            })
                        }
                    }
                });
            }
        })
    }

    function load_status(id) {
        $.ajax({
            url: "<?=base_url().$content.'/load_status';?>",
            type: "POST",
            data: {orders: id},
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                if (res.status) {
                    var data_order = res.res_order;
                    for (var i = 0; i < data_order.length; i++) {
                        $("#status_transaksi"+data_order[i].id).html(data_order[i].status);
                        $("#input_resi"+data_order[i].id).html(data_order[i].input_resi);
                        $("#no_resi"+data_order[i].id).html(data_order[i].no_resi);
                        $("#action"+data_order[i].id).html(data_order[i].aksi);
                    }
                    load_header();
                    to_svg();
                } else {
                    alert("GAGAL");
                }
            }
        });
    }

    function batalkan(id) {
        swal({
            title: 'Batalkan Pesanan?',
            text: "Anda akan membatalkan pesanan dengan nomor "+id,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "<?=base_url().$content.'/batalkan';?>",
                    type: "POST",
                    data: {id_order: btoa(id)},
                    dataType: "JSON",
                    beforeSend: function() {
                        $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
                    },
                    complete: function() {
                        $('.first-loader').remove();
                    },
                    success: function (res) {
                        if (res.status) {
                            swal(
                                'Berhasil!',
                                'Pesanan telah dibatalkan',
                                'success'
                            ).then(function () {
                                load_data();
                                //load_status(JSON.stringify(res.res_orders));
                            })
                        }
                    }
                });
            }
        })
    }

    function clear_session() {
        $.ajax({
            url: "<?=base_url().'site/unset_session/search';?>",
            success: function (res) {
                if (res) {
                    $("#search").val('');
                    $("#bank").val('').change();
                    set_date('<?=date('Y-m-d').' - '.date('Y-m-d')?>', 'daterange2');
                    load_data();
                }
            }
        });
    }

    $("#print_selected").click(function () {
        if ($("#print_selected").is(":checked")) {
            $(".ck_print").prop('checked', true);
        } else {
            $(".ck_print").prop('checked', false);
        }
    });

    var print_nota = [];
    function print_label(type, id=null) {
        if (type == 'single') {
            print_nota = ["'"+id+"'"];
            window.location = "<?=base_url().'penjualan/print_label/'?>"+btoa(JSON.stringify(print_nota));
        } else {
            print_nota = [];
            var print = document.getElementsByClassName('ck_print');
            for (var x=0; x<print.length; x++) {
                if (print[x].checked) {
                    print_nota.push("'"+print[x].value+"'");
                }
            }

            if (print_nota.length > 0) {
                window.location = "<?=base_url().'penjualan/print_label/'?>"+btoa(JSON.stringify(print_nota));
            } else {
                swal({
                    title: "Nota belum dipilih!",
                    text: "Harap ceklis kode order yang akan dicetak!",
                    type: "warning"
                });
            }
        }
    }
</script>