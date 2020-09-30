var zoneColIndex = 1;
var locIDColIndex = 2;
var contactColIndex = 3;
var primaryColIndex = 4;
var alternateColIndex = 5;

$(document).ready(function() {
    window.name = 'alarmlist';
    $("#recordAddBtn").hide();
    if ($("#loggedUserRole").val() == "SuperAdmin") {
        $("#adminNavLItag").show();
    } else {
        $("#adminNavLItag").hide();
    }
    var dataW = {};
    dataW.actionfunction = "setAlertMe";
    $.ajax({
        url: "CRUD.php",
        cache: false,
        type: "POST",
        data: { dbData: dataW },
        async: false,
        success: function(response) {
            if (response != 'error') {

            } //End of if response!=error
        } //END of success
    }) //END of CRUD ajax call

    permitNavLinks();

    loadAlarmTable();

    //https://code.tutsplus.com/tutorials/getting-started-with-chartjs-scales--cms-28477
    $(document).on('click', '#barChartSize', function() {
        var dataW = {};
        var linechart;
        var xaxisLocIDs = [];
        var ONdata = [];
        var yaxisOnTime = [];
        var OFFdata = [];
        var yaxisOffTime = [];
        dataW.actionfunction = "getBarChartData";
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
                                xaxisLocIDs.push(y["locationID"]);
                                ONdata.push(y["alarmON"]);
                                yaxisOnTime.push(y["alarmONTime"]);
                                OFFdata.push(y["alarmOFF"]);
                                yaxisOffTime.push(y["alarmOFFTime"]);
                            });
                        }
                    }); // end of .each
                } //End of if response!=error
            } //END of success
        }) //END of CRUD ajax call

        $('#alarmChart').remove(); // this is my <canvas> element
        $('#chartContainer').append('<canvas id="alarmChart" style="height:300px;width:content-box; background: linear-gradient(darkgray, lightblue);"></canvas>');

        //two xaxes one for zone+locationID and the other for ON and OFF 
        //https://stackoverflow.com/questions/42934608/how-to-create-two-x-axes-label-using-chart-js/42934853
        var chartData = {
            //labels for x axis
            labels: xaxisLocIDs,
            //datasets is an array
            datasets: [{
                    label: "ON", //label for legend name
                    backgroundColor: "#FE2E2E",
                    strokeColor: "black",
                    data: ONdata,
                    alarmTime: yaxisOnTime
                },
                {
                    label: "RESET",
                    backgroundColor: "#82FA58",
                    strokeColor: "black",
                    data: OFFdata,
                    alarmTime: yaxisOffTime
                }
            ]
        };

        var opt = {
            responsive: true,
            maintainAspectRatio: false,
            events: false,
            tooltips: {
                enabled: false
            },
            legend: {
                onClick: null,
                position: 'bottom'
            },
            hover: {
                animationDuration: 0
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        min: 0, // it is for ignoring negative step.
                        beginAtZero: true,
                        //callback to remove steps between 0 and 1
                        callback: function(value, index, values) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        }
                    },
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    },
                    barThickness: 40,
                    //categoryPercentage: 0.5,
                    //barPercentage: 1,
                    ticks: {
                        fontFamily: "'Open Sans', sans-serif",
                        fontSize: 12,
                        fontStyle: "bold",
                        fontColor: "blue"
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Location Numbers",
                        fontColor: "red",
                        fontSize: 18,
                    }
                }]
            },
            animation: {
                onComplete: function() {
                    var chartInstance = this.chart;
                    var ctx = chartInstance.ctx;
                    ctx.textAlign = "right";
                    //ctx.textBaseline = 'bottom';
                    //ctx.font = "bold 20px Arial";
                    ctx.font = "14px Arial";
                    ctx.fillStyle = "black";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "bottom";
                    Chart.helpers.each(this.data.datasets.forEach(function(dataset, i) {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        Chart.helpers.each(meta.data.forEach(function(bar, index) {
                            ctx.save();
                            // Translate 0,0 to the point you want the text
                            ctx.translate(bar._model.x, bar._model.y + 100);

                            // Rotate context by -90 degrees
                            ctx.rotate(-Math.PI / 2);

                            // Draw text //fillText is HTML5 canvas function
                            ctx.fillText(dataset.alarmTime[index], 0, 0);
                            ctx.restore();
                        }), this)
                    }), this);
                }
            }

        };
        var ctx = document.getElementById("alarmChart");
        myBarChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: opt
        });
    }); //END of onclick


    $("#barChartSize").click();
    //END OF CHART BLOCK

    function loadAlarmTable() {
        var dataW = {};
        dataW.actionfunction = "alarmList";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUserRole = $('#loggedUserRole').val();
        dataW.loggedUser_id = $('#loggedUser_id').val();
        dataW.loggedUserRole_id = $('#loggedUserRole_id').val();

        var alarmTbl = '';
        var downTbl = '';
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                //alert(JSON.stringify(response));
                if (JSON.stringify(response).indexOf('"alertMe":"alertMe"') == -1) {
                    //alert("NO alertMe");
                } else {
                    //alert("alertMe present");
                    $("#redAlertsContainer").show();
                }
                if (response != 'error') {
                    $.each(response, function(key, value) {
                        if (key == 'alarms') {
                            $.each(value, function(x, y) {
                                alarmTbl += '<tr id="row_' + x + '_l_' + y['lid'] + '"> \
                                                    <td  class="blinking">' + y['zone'] + '</td> \
                                                    <td  class="blinking">' + y['locationID'] + '</td> \
                                                    <td>' + y['contactName'] + '</td> \
                                                     <td>' + y['primaryContact'] + '</td> \
                                                     <td>' + y['alternateContact'] + '</td> \
                                                     <td class="' + y['alertMe'] + '">' + y['lastAlarmTime'] + '</td> \
                                                     <td>' + y['comment'] + '</td> \
                                                  </tr>';
                            });
                            $('#alarmTbl tbody > tr').remove();
                            $('#alarmTbl').append(alarmTbl);
                            //$("#chartSize").click();
                        } else if (key == 'downs') { //END of if key == alarms
                            $.each(value, function(x, y) {
                                downTbl += '<tr> \
                                                    <td>' + y['zone'] + '</td> \
                                                    <td>' + y['locationID'] + '</td> \
                                                    <td>' + y['contactName'] + '</td> \
                                                     <td>' + y['primaryContact'] + '</td> \
                                                     <td>' + y['alternateContact'] + '</td> \
                                                     <td>' + y['lastStatusUpdate'] + '</td> \
                                                     <td>' + y['comment'] + '</td> \
                                                  </tr>';
                            });
                            $('#downTbl tbody > tr').remove();
                            $('#downTbl').append(downTbl);
                            //$("#chartSize").click();
                        } //END of if key == alarms
                    }); // end of .each js_alarmsData
                } //End of if response!=error
                $(".alertDivs").remove();
                //$( "#tableContainer" ).after('"<div id="redAlertsContainer" class="container-fluid p-5"> \
                //                                  <div id="redalerts" class="row"> </div> \
                //                            </div>"');
                //$("#redAlertsContainer").css("display", "block");
                $(".alertMe").each(function() {
                    //$(this).parent().css("display", "block");
                    var thisRowID = $(this).parent().attr("id");
                    //alert(thisRowID);
                    var alertMsg = $(this).parent().find('td').eq(zoneColIndex - 1).html() + "; " +
                        $(this).parent().find('td').eq(locIDColIndex - 1).html() + "; " +
                        $(this).parent().find('td').eq(contactColIndex - 1).html().split(" ")[0] + "; " +
                        $(this).parent().find('td').eq(primaryColIndex - 1).html() + "; " +
                        $(this).parent().find('td').eq(alternateColIndex - 1).html();
                    $('#redalerts').append(
                        '<div class="alertDivs blinking alert mr-1" style="background-color:red;"> \
                           <span id="' + thisRowID + '" class="closebtn">&times;</span> \
                           ' + alertMsg + ' \
                         </div>');
                    //document.getElementById('audiotag1').play();
                });
                //$("#barChartSize").click(); // do not refresh every second, the chart will be re-drawn every second
            }, //END of success
            /*complete: function() {
                //alert("ajax call completed");
                setTimeout(function() {
                    loadAlarmTable();
                    //loadDownTable();
                }, 1000);
            }*/
        }); //END of CRUD ajax call
    } //END OF loadAlarmTable()

    $(document).on('click', '.closebtn', function() {
        $(this).parent().css("display", "none");
        var dataW = {};
        dataW.actionfunction = "resetAlertMe";
        dataW.id = $(this).attr("id").split("_")[3];
        //alert(dataW.id);
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                if (response != 'error') {

                } //End of if response!=error
            } //END of success
        }) //END of CRUD ajax call
        //alert($(".closebtn").length);
        if ($(".closebtn").length == 1) {
            $("#redAlertsContainer").hide();
            //$(this).parent().parent().parent().css("display", "none");
            //document.getElementById('audiotag1').pause();
        }
    })
});