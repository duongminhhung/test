
/*
 * Author: Abdullah A Almsaeed
 * Date: 4 Jan 2014
 * Description:
 *      This is a demo file used only for the main dashboard (index.html)
 **/
  'use strict'


  	// console.log("ok");
  	
  	$('.connectedSortable .card-header, .connectedSortable .nav-tabs-custom').css('cursor', 'move')
  	$('#circleChart').height($('#cardPlan').height());
    /*
     * DONUT CHART
     * -----------
     */
    if ($('#donut-chart').length) {
		var optionPieChart = 
		{
			series: [25, 25, 25, 125],
			labels: ['Waiting', 'Running', 'Stop', 'Trouble'],
			chart:{
				type:'pie',
				height:$('#circleChart').height()*0.85
			},
			dataLabels: {
				enabled: true,
			}
		}
		var chartPie = new ApexCharts(document.querySelector("#donut-chart"), optionPieChart);
		chartPie.render();
    }
    /*
     * END DONUT CHART
     */

    function labelFormatter(label, series) {
        return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
          + label
          + '<br>'
          + Math.round(series.percent) + '%</div>'
    }

	// Bar Chart
function drawBarChart(){
		$.ajax({
			method: 'get',
			url: window.location.origin + '/home/get-infor-chart',
			data: {},
			dataType: 'json'
		}).done(function(data)
		{
		  // console.log(data);
		//=====================Bar Chart Material========================
		if ($('#barChartMaterial').length) {
			var areaChartMaterial = {
			  labels  : data.dataMaterial.date,
			  datasets: [
				{
				  	label               : 'Import Material',
				  	backgroundColor     : 'rgba(210, 214, 222, 1)',
				  	borderColor         : 'rgba(210, 214, 222, 1)',
				  	pointRadius         : false,
				  	pointColor          : 'rgba(210, 214, 222, 1)',
				  	pointStrokeColor    : '#c1c7d1',
				  	pointHighlightFill  : '#fff',
				  	pointHighlightStroke: 'rgba(220,220,220,1)',
				  	data                : data.dataMaterial.numberImPro
				},
				{
				  	label               : 'Export Material',
				  	backgroundColor     : 'rgba(60,141,188,0.8)',
				  	borderColor         : 'rgba(60,141,188,0.8)',		
				  	pointRadius         : false,
				  	pointColor          : '#3b8bba',
				  	pointStrokeColor    : 'rgba(60,141,188,0)',
				  	pointHighlightFill  : '#fff',
				  	pointHighlightStroke: 'rgba(60,141,188,1)',
				  	data                : data.dataMaterial.numberExPro
				},
				{
				  	label               : 'Inventory Material',
				  	backgroundColor     : 'rgba(156, 39, 176, 0.57)',
				  	borderColor         : 'rgba(210, 214, 222, 1)',
				  	pointRadius         : false,
				  	pointColor          : 'rgba(210, 214, 222, 1)',
				  	pointStrokeColor    : '#c1c7d1',
				  	pointHighlightFill  : '#fff',
				  	pointHighlightStroke: 'rgba(220,220,220,1)',
				  	data                : data.dataMaterial.numberInvPro
				},
			  ]
			}
			var barChartCanvas = $('#barChartMaterial').get(0).getContext('2d')
			var barChartData = jQuery.extend(true, {}, areaChartMaterial)
		
			var barChartOptions = {
			  responsive              : true,
			  maintainAspectRatio     : false,
			  datasetFill             : false
			}
			var barChart = new Chart(barChartCanvas, {
			  type: 'bar', 
			  data: barChartData,
			  options: barChartOptions
			})
		}
	  	//====================== Bar Chart FG ======================================
			var areaChartFG = {
				labels: data.dataFG.date,
				datasets:[
					{
						label               : 'Import Product FG',
						backgroundColor     : 'rgba(210, 214, 222, 1)',
						borderColor         : 'rgba(210, 214, 222, 1)',
						pointRadius         : false,
						pointColor          : 'rgba(210, 214, 222, 1)',
						pointStrokeColor    : '#c1c7d1',
						pointHighlightFill  : '#fff',
						pointHighlightStroke: 'rgba(220,220,220,1)',
						data                : data.dataFG.numberImPro
				  	},
				  	{
						label               : 'Export Product FG',
						backgroundColor     : 'rgba(60,141,188,0.8)',
						borderColor         : 'rgba(60,141,188,0.8)',		
						pointRadius         : false,
						pointColor          : '#3b8bba',
						pointStrokeColor    : 'rgba(60,141,188,0)',
						pointHighlightFill  : '#fff',
						pointHighlightStroke: 'rgba(60,141,188,1)',
						data                : data.dataFG.numberExPro
				  	},
				  	{
						label               : 'Inventory Product FG',
						backgroundColor     : 'rgba(156, 39, 176, 0.57)',
						borderColor         : 'rgba(210, 214, 222, 1)',
						pointRadius         : false,
						pointColor          : 'rgba(210, 214, 222, 1)',
						pointStrokeColor    : '#c1c7d1',
						pointHighlightFill  : '#fff',
						pointHighlightStroke: 'rgba(220,220,220,1)',
						data                : data.dataFG.numberInvPro
				  	},
				]
			}
			var barChartFGCanvas = $('#barChartFG').get(0).getContext('2d');
			var barChartFGData = jQuery.extend(true, {}, areaChartFG);
	
		var barChartFGOptions = {
		  responsive              : true,
		  maintainAspectRatio     : false,
		  datasetFill             : false
		}
		var barChart = new Chart(barChartFGCanvas, {
		  type: 'bar', 
		  data: barChartFGData,
		  options: barChartFGOptions
		})
	  }).fail(function(e){console.log(e);})
};
drawBarChart();
//================= Plan Chart =====================================
$("#progressBarSelect").on('change',function()
{
	// console.log($(this).val());
	if($(this).val() != 0){

			$.ajax({
				method:'get',
				url: window.location.origin+'/home/get-infor-plan-chart',
				data:{
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'position':$(this).val()
				},
				dataType:'json'
			}).done(function(data)
			{
				// console.log(data);
				let percentPlan = data.data.percentPlan.toFixed(2);
				if(percentPlan-50 <=0 ) $("#progressBarPlan").removeClass('bg-warning bg-danger').addClass('bg-info');
				else if((percentPlan-50)*(percentPlan-80) <=0 ) $("#progressBarPlan").removeClass('bg-info bg-danger').addClass('bg-warning');
				else if((percentPlan-80) >0 ) $("#progressBarPlan").removeClass('bg-warning bg-info').addClass('bg-danger');
				$("#startPlanTime").text(data.data.startPlan);
				$("#endPlanTime").text(data.data.endPlan);
				$("#progressBarPlan").width(percentPlan+'%');
				$("#progressBarPlan").text(percentPlan+'%');
				
				$("#startActualTime").text(moment(data.data.startActual,'YYYY/MM/DD hh:mm').format('YYYY/MM/DD hh:mm'));
				$("#endActualTime").text(data.data.curTime);
				$("#progressBarActual").width(data.data.percentActual.toFixed(2)+'%');
				$("#progressBarActual").text(data.data.quantityActual+'/'+data.data.productPercentPlan);

			}).fail(function(e){console.log(e)});
		}	
	});

	$.ajax({
		method:'get',
		url:window.location.origin+'/home/get-infor-plans-status-chart',
		data:{'_token': $('meta[name="csrf-token"]').attr('content')},
		dataType:'json'
	}).done(function(data)
	{
		// console.log(data);
	}).fail(function(e){console.log(e)});

function removeChart() {
	// alert("Hello");
	$('#visitors').css('display', 'none');
	$('#sales').removeClass('col-lg-7');
	$('#sales').addClass('col-lg-12');
  }
  // var click1 = setInterval(function () {
  //   $('#click2').click();
  //   var click2 = setTimeout(function () {
  //     $('#click3').click();
  //     var click3 = setTimeout(function () {
  //       $('#click1').click();
  //     }, 10000)
  //   }, 10000)
  // }, 20000);
  //===============Stack Bar Chart =========Effeciency of each line========================
//   var d  = moment();
//   var time = d.getHours()+":"+d.getMinutes()+" "+ d.getDate()+"/"+(d.getMonth()+1)+"/"+d.getFullYear();
//   console.log(d);
if ($('#timelinechart').length) {
	var options = {
	series: [{
		name: 'Waiting',
		data: [4, 10, 13, 5]
	  }, 
	  {
		name: 'Running',
		data: [53, 32, 33, 52]
	  }, {
		name: 'Trouble',
		data: [12, 17, 11, 9]
	  }, {
		name: 'Running',
		data: [9, 7, 9, 2]
	  }],
	chart: {
	type: 'bar',
	height: 350,
	stacked: true,
	},
	plotOptions: {
		bar: {
		  horizontal: true,
		},
	  },
	//   labels: ['Apples', 'Oranges', 'Berries', 'Grapes'],
	//   dataLabels: ['Apples', 'Oranges', 'Berries', 'Grapes'],
	  xaxis: {
		categories: ['Line 1', 'Line 2','Line 3', 'Line 4'],
		labels:{
			formatter: function (val) {
			return moment().subtract(val,'seconds').format( "DD/MM/YYYY h:mm:ss a");
			// return val+"s";
			}
		  },
		title: {
			// text: false,
			offsetX: 0,
			offsetY: 0,
			style: {
				color: undefined,
				fontSize: '12px',
				fontFamily: 'Helvetica, Arial, sans-serif',
				fontWeight: 600,
				cssClass: 'apexcharts-xaxis-title',
			},
		},
		reversed: true,
	  },
	  yaxis: {
		title: {
		  text: undefined
		},
		
		reversed: true,
	  },	
	  tooltip: {
		enabled:true,
		// followCursor: true,
		y: {
		  formatter: function (val) {
			return val + "s"
		  }
		},
		fixed: {
			enabled: true,
			position: 'topLeft',
			offsetX: 600,
			offsetY: 0,
		},
	  },
	 
	  fill: {
		opacity: 1
	  },
	  legend: {
		show: false,
		position: 'top',
		horizontalAlign: 'left',
		offsetX: 40,
		onItemClick: {
			toggleDataSeries: true
		},
		onItemHover: {
			highlightDataSeries: true
		},
	  },
	  colors:['#3C74EC', '#1DA427','#DA2E1D','#1DA427']
	  };

	var chart = new ApexCharts(document.querySelector("#timelinechart"), options);
	chart.render();
  };
  $("#stackBarSelect").on("change",function()
  {
	  	// chart.updateSeries([
		// {
		// 	name: 'Running',
		// 	data: [0, 0]
	  	// }, 
	  	// {
		// 	name: 'Trouble',
		// 	data: [53, 32 ]
	  	// }	
		// ]);
		// console.log(chart.toggleDataPointSelection(0, 1));
  });
setInterval(function(){
	drawBarChart();
  	$("#progressBarSelect").change();
},600000);
	
