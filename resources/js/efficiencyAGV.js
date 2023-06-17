import Chart from 'chart.js';
$(function() {
    'use strict'

    var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    }

    var mode = 'index';
    var intersect = true;
    const DATA_COUNT = 7;
    const NUMBER_CFG = { count: DATA_COUNT, min: 0, max: 100 };

    let data1 = [10, 50, 50, 15, 20, 10, 30];
    let data2 = [20, 20, 10, 15, 20, 10, 20];
    let data3 = [50, 10, 15, 30, 20, 30, 10];
    let data4 = [10, 10, 15, 20, 20, 20, 20];
    let data5 = [10, 10, 10, 20, 20, 30, 20];
    let sumData1 = 1;
    let sumData2 = 1;
    let sumData3 = 1;
    let sumData4 = 1;
    let sumData5 = 1;

    var $efficiencyChart = $('#efficiency-chart')
    var efficiencyChart = new Chart($efficiencyChart, {
        type: 'bar',
        data: {
            labels: ['AGV1', 'AGV2', 'AGV3', 'AGV4', 'AGV5', 'AGV6', 'AGV7'],
            datasets: [{
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    data: data1,
                    label          : __agv.run,
                    // label: 'Chạy',
                },
                {
                    backgroundColor: '#FBD502',
                    borderColor: '#FBD502',
                    data: data2,
                    // label: 'Dừng Sạc',
                    label          : __agv.stop,
                },
                {
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    data: data3,
                    label          : __agv.error,
                    // label: 'Lỗi',
                },
                {
                    backgroundColor: "#007bff",
                    borderColor: "#007bff",
                    data: data4,
                    label          : __agv.lowBattery,
                    // label: 'Pin Yếu',
                },
                {
                    backgroundColor: "#d9d9d9",
                    borderColor: "#d9d9d9",
                    data: data5,
                    label          : __agv.powerLoss,
                    // label: 'Tắt Nguồn',
                }
            ]
        },
        plugins: [{
            beforeInit: function(chart, options) {
                chart.legend.afterFit = function() {
                    this.height = this.height + 50;
                };
            }
        }],
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                mode: 'point',
                intersect: intersect,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label) {
                            label += ' : ';
                        }

                        label += Math.round(tooltipItem.yLabel * 100) / 100;
                        // console.log(tooltipItem.yLabel, tooltipItem.xLabel)

                        return label;
                    }
                }
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                position: 'top',
                usePointStyle: true
            },
            plugins: {
                datalabels: {
                    display: true,
                    align: 'center',
                    backgroundColor: '#ccc',
                    borderRadius: 3,
                    font: {
                        size: 18,
                    },
                    formatter: function(value) {
                        return value + '%';
                    },
                },
            },
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    min: 0,
                    max: 10,
                }
            }
        }
    });

    let colors = [
        "#3e95cd", "#8e5ea2", "#3cba9f",
        "#e8c3b9", "#c45850", '#90AFC5',
        '#336B87', '#2A3132', '#763626',
        '#46211A', '#46211A', '#46211A',
        '#A43820', '#505160', '#68829E',
        '#AEBD38', '#AEBD38', '#003B46'
    ];

    let errorChart = new Chart(document.getElementById("efficiency-chart-error"), {
        type: 'pie',
        data: {
            labels: ["Lỗi 1", "Lỗi 2", "Lỗi 3", "Lỗi 4", "Lỗi 5"],
            datasets: [{
                label: "Population (millions)",
                backgroundColor: colors,
                data: [2478, 5267, 734, 784, 433]
            }]
        },
        options: {
            animation: false,
            legend: {
                position: 'right',
                usePointStyle: true
            },
            title: {
                display: false,
                // text: 'Predicted world population (millions) in 2050'
            },
            plugins: {
                datalabels: {
                    display: true,
                    align: 'bottom',
                    // backgroundColor: '#ccc',
                    borderRadius: 3,
                    font: {
                        size: 12,
                    },
                    formatter: function(value) {
                        return value + '%';
                    },
                },
            }
        }
    });

    let totalChart = new Chart(document.getElementById("efficiency-chart-total"), {
        type: 'pie',
        data: {
            labels: [__agv.run, __agv.stop, __agv.error, __agv.lowBattery, __agv.powerLoss],
            // labels: ['Chạy', 'Dừng Sạc', 'Lỗi', 'Pin Yếu', 'Tắt Nguồn'],
            datasets: [{
                label: "Population (millions)",
                backgroundColor: ["#28a745", "#FBD502", "#dc3545", "#007bff", "#d9d9d9"],
                data: [2478, 5267, 734, 500, 300]
            }]
        },
        options: {
            animation: false,
            legend: {
                position: 'right',
                usePointStyle: true
            },
            title: {
                display: false,
                // text: 'Predicted world population (millions) in 2050'
            },
            plugins: {
                datalabels: {
                    display: true,
                    align: 'bottom',
                    // backgroundColor: '#ccc',
                    borderRadius: 3,
                    font: {
                        size: 12,
                    },
                    formatter: function(value) {
                        return value + '%';
                    },
                },
            }
        }
    });

    calWin();

    function calWin() {
        // $('#efficiency-chart').height(
        //   $('#efficiency-chart-total').height() + $('#efficiency-chart-error').height() + 135
        // );
    }



    function efficiencyAgv(dataGet) {
        return $.ajax({
            method: 'get',
            url: window.location.origin + '/setting/setting-agv/efficiency',
            data: dataGet,
            dataType: 'json'
        })
    }

    function efficiencyErr(dataGet) {
        return $.ajax({
            method: 'get',
            url: window.location.origin + '/control-agv/efficiency-agv/efficiency-error',
            data: dataGet,
            dataType: 'json'
        })
    }

    let time;

    function runTime() {
        clearTimeout(time);
        time = setTimeout(function() {
            drawEff();
        }, 30000);
    }

    drawEff();

    function drawEff() {
        let agvId = [];

        $(".check-box:checked").each(function() {
            agvId.push($(this).val());
        });

        let dataGet = {
            agv: agvId,
            from: $('#from').val(),
            to: $('#to').val()
        }

        efficiencyAgv(dataGet).done(function(data) {
            // console.log(data);
            let data0 = [];
            let dataTotal = [0, 0, 0, 0, 0];
            let dataD0 = [];
            let dataD1 = [];
            let dataD2 = [];
            let dataD3 = [];
            let dataD4 = [];
            let labels = [];
            let sumTotal = 0;

            efficiencyChart.data.datasets.forEach((dataset) => {
                dataset.data = [];
            });

            for (let i = 0; i < data.data.length; i++) {
                let dat = data.data[i];
                // console.log(dat);
                let run = parseInt(dat.RUNTIME ? dat.RUNTIME : 0);
                let ide = parseInt(dat.IDE_TIME ? dat.IDE_TIME : 0);
                let err = parseInt(dat.ERRORTIME ? dat.ERRORTIME : 0);
                let lowB = parseInt(dat.LOWBATTERY ? dat.LOWBATTERY : 0);
                let powL = parseInt(dat.POWERLOSS ? dat.POWERLOSS : 0);


                let pRun = ((run / (run + ide + err + lowB + powL)) * 100).toFixed(2);
                let pIde = ((ide / (run + ide + err + lowB + powL)) * 100).toFixed(2);
                let pLowB = ((lowB / (run + ide + err + lowB + powL)) * 100).toFixed(2);
                let pPowL = ((powL / (run + ide + err + lowB + powL)) * 100).toFixed(2);
                let pErr = (100 - pRun - pIde - pLowB - pPowL).toFixed(2);

                labels.push(dat.Name);

                data0[dat.Name] = [];
                data0[dat.Name].push(run);
                data0[dat.Name].push(ide);
                data0[dat.Name].push(err);
                data0[dat.Name].push(lowB);
                data0[dat.Name].push(powL);

                dataTotal[0] += run;
                dataTotal[1] += ide;
                dataTotal[2] += err;
                dataTotal[3] += lowB;
                dataTotal[4] += powL;

                sumTotal += run + ide + err + lowB + powL;

                dataD0.push(pRun);
                dataD1.push(pIde);
                dataD2.push(pErr);
                dataD3.push(pLowB);
                dataD4.push(pPowL);
            }

            sumTotal == '0' ? sumTotal = 1 : '';

            // Run
            efficiencyChart.data.datasets[0].data = dataD0;
            // Ide
            efficiencyChart.data.datasets[1].data = dataD1;
            // Error
            efficiencyChart.data.datasets[2].data = dataD2;
            // Lower Battery
            efficiencyChart.data.datasets[3].data = dataD3;
            // Power Loss
            efficiencyChart.data.datasets[4].data = dataD4;
            // Labels
            efficiencyChart.data.labels = labels;

            // console.log(dataD0, dataD1, dataD2, labels);

            let opt = {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    mode: 'point',
                    intersect: intersect,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            let label = data.datasets[tooltipItem.datasetIndex].label || '';

                            if (label) {
                                label += ' : ';
                            }

                            let time = data0[tooltipItem.xLabel][tooltipItem.datasetIndex];

                            if (time > 60 && time < 3600 && 0) {
                                label += time + 'm';
                            } else if (time >= 3600 && 0) {
                                let h = parseInt(time / 3600);
                                let m = parseInt((time - h * 3600) / 60);
                                let s = (time - h * 3600) % 60

                                label += h + 'h' + m + 'm' + s + 's';
                            } else {
                                label += time + 's';
                            }

                            return label;
                        }
                    }
                },
                hover: {
                    mode: mode,
                    intersect: intersect
                },
                legend: {
                    position: 'top',
                    usePointStyle: true
                },
                plugins: {
                    datalabels: {
                        display: true,
                        align: 'center',
                        // backgroundColor: '#ccc',
                        borderRadius: 3,
                        font: {
                            size: 12,
                        },
                        formatter: function(value) {
                            return value + '%';
                        },
                    },
                },
                scales: {
                    yAxes: [{
                        stacked: true,
                        // display: false,
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return value + '%';
                            }
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        barPercentage: labels.length >= 7 ? 1 : 0.3,
                        stacked: true,
                        display: true,
                        gridLines: {
                            display: false
                        },
                        ticks: $.extend({
                            beginAtZero: true,
                            // callback: function (value, index, values) {
                            //   return value;
                            // }
                        }, ticksStyle)
                    }]
                }
            };

            efficiencyChart.options = opt;

            efficiencyChart.update();

            totalChart.data.datasets.forEach((dataset) => {
                dataset.data = [];
            });

            let dataTotalNew = [];
            let tot = 0;

            for (let i = 0; i < dataTotal.length; i++) {
                let da = dataTotal[i];

                da = da / sumTotal;

                if (i == dataTotal.length - 1 && tot != '0') {
                    da = 1 - tot;
                }

                tot += da;

                dataTotalNew.push((da * 100).toFixed(1));
            }

            totalChart.options.tooltips = {
                intersect: intersect,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.labels[tooltipItem.index] || '';

                        if (label) {
                            label += ' : ';
                        }

                        let time = dataTotal[tooltipItem.index];

                        if (time > 60 && time < 3600 && 0) {
                            label += time + 'm';
                        } else if (time >= 3600 && 0) {
                            let h = parseInt(time / 3600);
                            let m = parseInt((time - h * 3600) / 60);
                            let s = (time - h * 3600) % 60

                            label += h + 'h' + m + 'm' + s + 's';
                        } else {
                            label += time + 's';
                        }

                        return label;
                    }
                }
            };

            totalChart.data.datasets[0].data = dataTotalNew;

            // console.log(dataTotal);

            totalChart.update();
        }).fail(function(err) {
            console.log(err);
            clearTimeout(time);
        });

        efficiencyErr(dataGet).done(function(data) {
            // console.log(data);
            let labels = [];
            let dataError = [];
            let sumError = 0;

            for (let dat of data.data) {
                let sum = dat.log_sum ? dat.log_sum : 0;

                labels.push(dat.ERROR);

                dataError.push(parseInt(sum));

                sumError += parseInt(sum);
            }

            sumError == '0' ? sumError = 1 : '';

            // console.log(labels, dataError);

            errorChart.data.datasets.forEach((dataset) => {
                dataset.data = [];
            });

            let dataErrorNew = [];
            let tot = 0;

            for (let i = 0; i < dataError.length; i++) {
                let da = dataError[i];

                da = (da / sumError);

                if (i == dataError.length - 1) {
                    da = 1 - tot;
                }

                tot += da;

                // da < 0.00001 ? da = 0 : '';

                dataErrorNew.push((da * 100).toFixed(1));
            }

            errorChart.data.datasets[0].data = dataErrorNew;

            errorChart.data.labels = labels;

            errorChart.options.tooltips = {
                intersect: intersect,
                callbacks: {
                    label: function(tooltipItem, data) {
                        let label = data.labels[tooltipItem.index] || '';

                        if (label) {
                            label += ' : ';
                        }

                        let time = dataError[tooltipItem.index];

                        if (time > 60 && time < 3600 && 0) {
                            label += time + 'm';
                        } else if (time >= 3600 && 0) {
                            let h = parseInt(time / 3600);
                            let m = parseInt((time - h * 3600) / 60);
                            let s = (time - h * 3600) % 60

                            label += h + 'h' + m + 'm' + s + 's';
                        } else {
                            label += time + 's';
                        }

                        return label;
                    }
                }
            };

            errorChart.update();

            runTime();
        }).fail(function(err) {
            console.log(err);
            clearTimeout(time);
        });
    }

    $('#to').on('change', function() {
        drawEff();
    });

    $('.btn-agv').on('click', function() {
        drawEff();
        $('#modalAGV').modal('hide');
    });

})