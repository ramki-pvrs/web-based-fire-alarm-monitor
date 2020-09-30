    //START OF LINE CHART BLOCK - will not work because alarmhistory table model has changed
    //changed the id so that bar chart can be plugged in with new alarm history table model
    $(document).on('click', '#lineChartSize', function() {
        var dataW = {};
        var linechart;
        var xaxisDate = [];
        var yaxisRaised = [];
        var yaxisReset = [];
        dataW.actionfunction = "getLineChartData";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.barChartSize = $("#numOfAlarms").val();
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                if (response != 'error') {
                    $.each(response, function(key, value) {
                        //js_alarmsData.alarms returns array of js objects;
                        //alert(JSON.stringify(js_alarmsData.alarms));
                        if (key == 'history') {
                            $.each(value, function(x, y) {
                                xaxisDate.push(y["setreset"]);
                                yaxisRaised.push(y["raised"]);
                                yaxisReset.push(y["reset"]);
                            });
                        }
                    }); // end of .each
                } //End of if response!=error
            } //END of success
        }) //END of CRUD ajax call

        $('#alarmChart').remove(); // this is my <canvas> element
        $('#chartContainer').append('<canvas id="alarmChart" width="1500" height="400" style="background: linear-gradient(darkgray, lightblue);"></canvas>');
        
        var alarmCanvas = document.getElementById("alarmChart");

        Chart.defaults.global.defaultFontFamily = "Lato";
        Chart.defaults.global.defaultFontSize = 18;
        var dataRaised = null;
        dataRaised = {
            label: "Alarm Raised",
            //data: [0, 59, 75, 20, 20, 55, 40],
            data: yaxisRaised,
            lineTension: 0.2,
            fill: false,
            borderColor: 'red',
            borderWidth: 1.5,
            backgroundColor: 'transparent',
            pointBorderColor: 'red',
            pointBackgroundColor: 'lightgreen',
            pointRadius: 5,
            pointHoverRadius: 15,
            pointHitRadius: 30,
            pointBorderWidth: 2,
            pointStyle: 'rect'
        };
        var dataCleared = null;
        dataCleared = {
            label: "Alarm Cleared",
            //data: [20, 15, 60, 60, 65, 30, 70],
            data: yaxisReset,
            lineTension: 0.2,
            fill: false,
            borderColor: 'green',
            borderWidth: 1.5,
            backgroundColor: 'transparent',
            pointBorderColor: 'green',
            pointBackgroundColor: 'lightgreen',
            pointRadius: 5,
            pointHoverRadius: 15,
            pointHitRadius: 30,
            pointBorderWidth: 2
        };
        var alarmData = null;
        alarmData = {
            //labels: ["0s", "10s", "20s", "30s", "40s", "50s", "60s"],
            labels: xaxisDate,
            datasets: [dataRaised, dataCleared]
        };
        var chartOptions = null;
        chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 80,
                    fontColor: 'black'
                }
            },
            scaleShowValues: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 2
                    }
                }],
                xAxes: [{
                    ticks: {
                        autoSkip: false,
                        fontSize: 14
                    }
                }]
            }
        };

        linechart = new Chart(alarmCanvas, {
            type: 'line',
            data: alarmData,
            options: chartOptions
        });
    }); //END of onclick

    //$("#lineChartSize").click();