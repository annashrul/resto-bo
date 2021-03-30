<div style="margin-top:0px;"></div>

<!--MASTER DATA-->
<?php if ((substr($access->access,1,2) != 0)){ ?>
<div class="row">
	<h3 style="margin-left:30px; color:#707070 ;"><i class="fa fa-briefcase"></i> Master Data</h3><br>

	<?php if (substr($access->access,1,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>user/user-level">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/master-data/user-level.png" width="50px" height="50px"/>
			<h4>User Level</h4>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,2,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>user/user-list">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/master-data/user-list.png" width="50px" height="50px"/>
			<h4>User List</h4>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<div class="col-lg-12">
		<hr style=" height: 12px; border: 0; box-shadow: inset 0 12px 12px -12px rgba(0, 0, 0, 0.5);">
	</div>
	
</div>
<?php } ?>


<!--ACCOUNTING-->
<?php if (substr($access->access,3,31) != 0){ ?>
<div class="row">
<h3 style="margin-left:30px; color:#707070 ;"><i class="fa fa-money"></i> Accounting</h3><br>

	<?php if (substr($access->access,3,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/account-category">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/account-category.png" width="50px" height="50px"/>
			<h5>Account Category</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,4,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/account-group">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/account-group.png" width="50px" height="50px"/>
			<h5>Account Group</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,5,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/code-of-account">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/code-of-account.png" width="50px" height="50px"/>
			<h5>Code of Account</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,6,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/set_periode">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/SetPeriode.png" width="50px" height="50px"/>
			<h5>Set Periode</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,7,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/exchange-money">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/exchange-money.png" width="50px" height="50px"/>
			<h5>Exchange Money</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,8,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/beginning-balance">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/beginning-balance.png" width="50px" height="50px"/>
			<h5>Beginning Balance</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,9,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/general-journal">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/general-journal.png" width="50px" height="50px"/>
			<h5>General Journal</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,10,1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/ledger">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/ledger.png" width="50px" height="50px"/>
			<h5>Ledger</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,11, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/cash_mutation">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/cash-mutation.png" width="50px" height="50px"/>
			<h5>Cash Mutation</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,12, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/cash-mutation-report">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/cash-mutation-report.png" width="50px" height="50px"/>
			<h5>Cash Mutation Report</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,13, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/bank_voucher">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/bank-voucher.png" width="50px" height="50px"/>
			<h5>Bank Voucher</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,14, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/bank_voucher_report">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/bank-voucher-report.png" width="50px" height="50px"/>
			<h5>Bank Voucher Report</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,15, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/cash_voucher">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/cash-voucher.png" width="50px" height="50px"/>
			<h5>Cash Voucher</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,16, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/cash-voucher-report">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/cash-voucher-report.png" width="50px" height="50px"/>
			<h5>Cash Voucher Report</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,17, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/tico_voucher">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/tico-voucher.png" width="50px" height="50px"/>
			<h5>Tico Voucher</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,18, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/tico-voucher-report">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/tico-voucher-report.png" width="50px" height="50px"/>
			<h5>Tico Voucher Report</h5>
		</center>
		</a>
	</div>
	<?php } ?>
		
	<?php if (substr($access->access,19, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/fixed_asset">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/fixed-asset.png" width="50px" height="50px"/>
			<h5>Fixed Asset</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,20, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/journal-entry">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/journal-entry.png" width="50px" height="50px"/>
			<h5>Journal Entry</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,21, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/journal-entry-report">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/journal-entry-report.png" width="50px" height="50px"/>
			<h5>Journal Entry Report</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,22, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/adjustment-journal">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/adjustment-journal.png" width="50px" height="50px"/>
			<h5>Adjustment Journal</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,23, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/adjustment-journal-report">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/adjustment-journal-report.png" width="50px" height="50px"/>
			<h5>Adjustment Jour. Report</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,24, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/work-sheet">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/work-sheet.png" width="50px" height="50px"/>
			<h5>Work Sheet</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,25, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/trial-balance">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/trial-balance.png" width="50px" height="50px"/>
			<h5>Trial Balance</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,26, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/profit-loss">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/profit-lost.png" width="50px" height="50px"/>
			<h5>Profit Loss</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,27, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/balance-sheet">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/balance-sheet.png" width="50px" height="50px"/>
			<h5>Balance Sheet</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,28, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/cash-flow">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/cash-flow.png" width="50px" height="50px"/>
			<h5>Cash Flow</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,29, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/capital-change">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/capital-change.png" width="50px" height="50px"/>
			<h5>Capital Change</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,30 , 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/currency_balance">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/currency-balance.png" width="50px" height="50px"/>
			<h5>Currency Balance</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<?php if (substr($access->access,31, 1) == 1){ ?>
	<div class="col-lg-2">
		<a href="<?php echo base_url();?>accounting/closing-entries">
		<center>
			<img src="<?php echo base_url();?>assets/images/menu/accounting/closing-entry.png" width="50px" height="50px"/>
			<h5>Closing Entries</h5>
		</center>
		</a>
	</div>
	<?php } ?>
	
	<div class="col-lg-12">
		<hr style=" height: 12px; border: 0; box-shadow: inset 0 12px 12px -12px rgba(0, 0, 0, 0.5);">
	</div>
	
</div>
<?php } ?>


<script>
setCarouselHeight('#carousel-example');

    function setCarouselHeight(id)
    {
        var slideHeight = [];
        $(id+' .item').each(function()
        {
            // add all slide heights to an array
            slideHeight.push($(this).height());
        });

        // find the tallest item
        max = Math.max.apply(null, slideHeight);

        // set the slide's height
        $(id+' .carousel-content').each(function()
        {
            $(this).css('height',max+'px');
        });
    }
	
      $(function () {
        /* ChartJS
         * -------
         * Here we will create a few charts using ChartJS
         */

        //--------------
        //- AREA CHART -
        //--------------

        // Get context with jQuery - using jQuery's .get() method.
        var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var areaChart = new Chart(areaChartCanvas);

        var areaChartData = {
          labels: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli","Agustus","September","Oktober","November","Desember"],
          datasets: [
            {
              label: "Electronics",
              fillColor: "rgba(210, 214, 222, 1)",
              strokeColor: "rgba(210, 214, 222, 1)",
              pointColor: "rgba(210, 214, 222, 1)",
              pointStrokeColor: "#c1c7d1",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(220,220,220,1)",
              data: []
            },
            {
              label: "Digital Goods",
              fillColor: "rgba(60,141,188,0.9)",
              strokeColor: "rgba(60,141,188,0.8)",
              pointColor: "#3b8bba",
              pointStrokeColor: "rgba(60,141,188,1)",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(60,141,188,1)",
              data: [<?php echo $perkembangan_deposit; ?>]
            }
          ]
        };

        var areaChartOptions = {
          //Boolean - If we should show the scale at all
          showScale: true,
          //Boolean - Whether grid lines are shown across the chart
          scaleShowGridLines: false,
          //String - Colour of the grid lines
          scaleGridLineColor: "rgba(0,0,0,.05)",
          //Number - Width of the grid lines
          scaleGridLineWidth: 1,
          //Boolean - Whether to show horizontal lines (except X axis)
          scaleShowHorizontalLines: true,
          //Boolean - Whether to show vertical lines (except Y axis)
          scaleShowVerticalLines: true,
          //Boolean - Whether the line is curved between points
          bezierCurve: true,
          //Number - Tension of the bezier curve between points
          bezierCurveTension: 0.3,
          //Boolean - Whether to show a dot for each point
          pointDot: false,
          //Number - Radius of each point dot in pixels
          pointDotRadius: 4,
          //Number - Pixel width of point dot stroke
          pointDotStrokeWidth: 1,
          //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
          pointHitDetectionRadius: 20,
          //Boolean - Whether to show a stroke for datasets
          datasetStroke: true,
          //Number - Pixel width of dataset stroke
          datasetStrokeWidth: 2,
          //Boolean - Whether to fill the dataset with a color
          datasetFill: true,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
          //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: true,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true
        };

        //Create the line chart
        areaChart.Line(areaChartData, areaChartOptions);

        //-------------
        //- LINE CHART -
        //--------------
        var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
        var lineChart = new Chart(lineChartCanvas);
        var lineChartOptions = areaChartOptions;
        lineChartOptions.datasetFill = false;
        lineChart.Line(areaChartData, lineChartOptions);

        //-------------
        //- PIE CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
        var pieChart = new Chart(pieChartCanvas);
        var PieData = [
          {
            value: 700,
            color: "#f56954",
            highlight: "#f56954",
            label: "Chrome"
          },
          {
            value: 500,
            color: "#00a65a",
            highlight: "#00a65a",
            label: "IE"
          },
          {
            value: 400,
            color: "#f39c12",
            highlight: "#f39c12",
            label: "FireFox"
          },
          {
            value: 600,
            color: "#00c0ef",
            highlight: "#00c0ef",
            label: "Safari"
          },
          {
            value: 300,
            color: "#3c8dbc",
            highlight: "#3c8dbc",
            label: "Opera"
          },
          {
            value: 100,
            color: "#d2d6de",
            highlight: "#d2d6de",
            label: "Navigator"
          }
        ];
        var pieOptions = {
          //Boolean - Whether we should show a stroke on each segment
          segmentShowStroke: true,
          //String - The colour of each segment stroke
          segmentStrokeColor: "#fff",
          //Number - The width of each segment stroke
          segmentStrokeWidth: 2,
          //Number - The percentage of the chart that we cut out of the middle
          percentageInnerCutout: 50, // This is 0 for Pie charts
          //Number - Amount of animation steps
          animationSteps: 100,
          //String - Animation easing effect
          animationEasing: "easeOutBounce",
          //Boolean - Whether we animate the rotation of the Doughnut
          animateRotate: true,
          //Boolean - Whether we animate scaling the Doughnut from the centre
          animateScale: false,
          //Boolean - whether to make the chart responsive to window resizing
          responsive: true,
          // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
          maintainAspectRatio: true,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
        };
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        pieChart.Doughnut(PieData, pieOptions);

        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $("#barChart").get(0).getContext("2d");
        var barChart = new Chart(barChartCanvas);
        var barChartData = areaChartData;
        barChartData.datasets[1].fillColor = "#00a65a";
        barChartData.datasets[1].strokeColor = "#00a65a";
        barChartData.datasets[1].pointColor = "#00a65a";
        var barChartOptions = {
          //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
          scaleBeginAtZero: true,
          //Boolean - Whether grid lines are shown across the chart
          scaleShowGridLines: true,
          //String - Colour of the grid lines
          scaleGridLineColor: "rgba(0,0,0,.05)",
          //Number - Width of the grid lines
          scaleGridLineWidth: 1,
          //Boolean - Whether to show horizontal lines (except X axis)
          scaleShowHorizontalLines: true,
          //Boolean - Whether to show vertical lines (except Y axis)
          scaleShowVerticalLines: true,
          //Boolean - If there is a stroke on each bar
          barShowStroke: true,
          //Number - Pixel width of the bar stroke
          barStrokeWidth: 2,
          //Number - Spacing between each of the X value sets
          barValueSpacing: 5,
          //Number - Spacing between data sets within X values
          barDatasetSpacing: 1,
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
          //Boolean - whether to make the chart responsive
          responsive: true,
          maintainAspectRatio: true
        };

        barChartOptions.datasetFill = false;
        barChart.Bar(barChartData, barChartOptions);
      });
    </script>