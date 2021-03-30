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
            <div class="row" style="margin-bottom: 10px">
                <div class="col-md-2">
                    <div class="box-tools">
                        <select id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option <?=($this->session->search['status']=='\'1\' and pb.status>=\'2\''?'selected':'')?> value="'1' and pb.status>='2'">Sudah Dibayar</option>
                            <option <?=($this->session->search['status']=='\'1\' and pb.status<\'2\''?'selected':'')?> value="'1' and pb.status<'2'">Menunggu Pembayaran</option>
                            <option <?=($this->session->search['status']=='\'2\' and p.no_resi is not null'?'selected':'')?> value="'2' and p.no_resi is not null">Sudah Dikirim</option>
                            <option <?=($this->session->search['status']=='\'2\' and p.no_resi is null'?'selected':'')?> value="'2' and p.no_resi is null">Belum Dikirim</option>
                            <option <?=($this->session->search['status']=='\'3\''?'selected':'')?> value="'3'">Dalam Pengiriman Kurir</option>
                            <option <?=($this->session->search['status']=='\'4\''?'selected':'')?> value="'4'">Sukses</option>
                            <option <?=($this->session->search['status']=='\'5\''?'selected':'')?> value="'5'">Batal</option>
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
                        <div class="input-group input-group-sm" style="width: 100%;">
                            <input type="text" name="table_search" class="form-control pull-right" id="search" value="<?=isset($this->session->search['any'])?$this->session->search['any']:''?>" placeholder="Cari by Kode Order/Nama Member">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary bg-blue" onclick="cari()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cari"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- /.box-header -->
                    <div class="table-responsive no-padding" id="result_table"></div>
                    <div align="center" id="pagination_link"></div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" id="modal_detail" style="display: none">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal_title_detail"></h4>
            </div>
            <form class="form-horizontal" id="form_detail">
                <div class="modal-body">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Produk</h3>
                                </div>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th>Ukuran</th>
                                        <th>Warna</th>
                                        <th>Qty Jual</th>
                                        <th>Harga Jual</th>
                                        <th>Diskon</th>
                                    </tr>
                                    </thead>
                                    <tbody id="det_produk">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php
isset($this->session->search['date'])?$date = $this->session->search['date']:$date='null';
?>
<script>
    $(document).ready(function(){
        load_data(1);
        if ('<?=$date?>' != 'null') {
            set_date('<?=$date?>', 'daterange2');
        }
    }).on("click", ".pagination li a", function(event){
        event.preventDefault();
        var page = $(this).data("ci-pagination-page");
        load_data(page);
    });

    function load_data(page,data={})
    {
        $.ajax({
            url:"<?=base_url().$content.'/get_data/';?>"+page,
            type:"POST",
            data:data,
            dataType:"JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success:function(data)
            {
                $('#result_table').html(data.result_table);
                $('#pagination_link').html(data.pagination_link);
                $("#user_level").html(data.user_level);
            }
        });
    }

    function cari() {
        var date_ = $("#table_date").val();
        var periode_ = null;
        var checkBox = document.getElementById("periode");
        if (checkBox.checked == true){
            periode_ = checkBox.value;
        }

        load_data(1, {search: true, any: $("#search").val(), date: date_, status: $("#status").val(), periode: periode_});
    }

    function detail(id_) {
        $.ajax({
            url: "<?=base_url().$content.'/detail'?>",
            type: "POST",
            data: {id: id_},
            dataType: "JSON",
            beforeSend: function() {
                $('body').append('<div class="first-loader"><img src="<?=base_url().'/assets/images/spin.svg'?>"></div>');
            },
            complete: function() {
                $('.first-loader').remove();
            },
            success: function (res) {
                $("#modal_title_detail").text("Detail Orders");
                $("#det_produk").html(res.res_produk);
                $("#modal_detail").modal("show");
            }
        });
    }
</script>