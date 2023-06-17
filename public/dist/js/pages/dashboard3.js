$(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

  var $salesChart = $('#sales-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : [],
      datasets: [
        {
          backgroundColor: '#66d9ff',
          borderColor    : '#66d9ff',
          data           : [1000, 2000, 3000, 2500, 2700, 2500, 3000]
        },
        {
          backgroundColor: '#ff7733',
          borderColor    : '#ff7733',
          data           : [700, 1700, 2700, 2000, 1800, 1500, 2000]
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      scales             : {
        yAxes: [{
          // display: false,
          stacked: true,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero: true,

            callback: function (value, index, values) {

              return value
            }
          }, ticksStyle)
        }],
        xAxes: [{
          stacked: true,
          display  : true,
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  })

  


  function apiDashboard()
  {
    return $.ajax({
      method  : 'get',
      url     : window.location.origin + '/dashboard',
      data    : {},
      dataType: 'json'
    })
  }

  let time;
  let labels = [];

  apiDashboard().done(function(data)
  {
    // console.log(data);
    $('#importNow').text(data.data.import.t6);
    $('#exportNow').text(data.data.export.t6);
    $('#inventoriesNow').text(data.data.inventory.t6);
    // console.log(visitorsChart.data.datasets,  salesChart.data.datasets);

    // for (var i = 0; i <= 6; i++) 
    // {
    //   labels.push(locale(lang).subtract());
    // }

   

    salesChart.data.datasets.forEach((dataset) => 
    {
      dataset.data = [];
    });

    // import
    salesChart.data.labels = [
      'mold1', 'mold2', 'mold3', 'mold4', 'mold5', 'mold6'
    ];
    salesChart.data.datasets[0].data = [
      1000, 2000, 3000, 2500, 2700, 2500, 3000
    ];

    // // export
    salesChart.data.datasets[1].data = [
      700, 1700, 2700, 2000, 1800, 1500, 2000
    ];

    // salesChart.data.labels = labels.reverse();
    
    salesChart.update();
    set_timeout();
  }).fail(function(err){console.log(err)})

  function set_timeout()
  {
    clearTimeout(time);

    time = setTimeout(function()
    {
      apiDashboard().done(function(data)
      {
        // console.log(data);
        $('#importNow').text(data.data.import.t6);
        $('#exportNow').text(data.data.export.t6);
        $('#inventoriesNow').text(data.data.inventory.t6);
        // console.log(visitorsChart.data.datasets,  salesChart.data.datasets);

      

        salesChart.data.datasets.forEach((dataset) => 
        {
          dataset.data = [];
        });
    
        // import
        salesChart.data.labels = [
          'mold1', 'mold2', 'mold3', 'mold4', 'mold5', 'mold6'
        ];
        salesChart.data.datasets[0].data = [
          1000, 2000, 3000, 2500, 2700, 2500, 3000
        ];
    
        // // export
        salesChart.data.datasets[1].data = [
          700, 1700, 2700, 2000, 1800, 1500, 2000
        ];
        
        salesChart.update();


        set_timeout();
      }).fail(function(err){console.log(err)})

    }, 30000)
  }
})


