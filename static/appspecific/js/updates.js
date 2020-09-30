var reasonAddEditBtn = '';
var reasonSubmitCancelBtn = '';
var recordCountBtn = '';
var editAHIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="alarmHistory_edit update_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var editDHIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="downHistory_edit update_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var DataTableObject = null;
var js_locationsData = {};

//table column index
var zoneColIndex = 1;
var locIDColIndex = 2;
var alarmTimeColIndex = 3;
var lastStatusUpdate = 3;
var statusColIndex = 4;
var rootCauseColIndex = 5;
var commentColIndex = 6;
var editColIndex = 7;

$(document).ready(function() {
    window.name = 'updatelist';
    if ($("#loggedUserRole").val() == "SuperAdmin") {
        $("#adminNavLItag").show();
    } else {
        $("#adminNavLItag").hide();
    }

    $("#recordAddBtn").hide();
    $.fn.dataTable.moment('DD/MM/YY');

    permitNavLinks();
    $("#logoutLItag").hide();
    
    var dataW = {};
    dataW.actionfunction = "alarmDownCauseList";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    dataW.loggedUser_id = $('#loggedUser_id').val();
    dataW.loggedUserRole_id = $('#loggedUserRole_id').val();
    var alarmReasonTbl = '';
    var downReasonTbl = '';
    $.ajax({
        url: "CRUD.php",
        cache: false,
        type: "POST",
        data: { dbData: dataW },
        async: false,
        success: function(response) {
            //alert(JSON.stringify(response));
            if (response != 'error') {
                //js_zoneLocationsData = $.parseJSON(response);
                loadTableReasonList(response,alarmReasonTbl,downReasonTbl);
                $.each(response, function(key, value) {
                    if(key == 'userPermissionValue') {
                        if ($("#loggedUserRole").val() == "SuperAdmin") {
                            //dont do anything
                        } else {
                           afterAjaxCRUDPermission(parseFloat(value), $("#updateAddBtn"), $(".update_edit"), $(".fa-edit")); 
                        }
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call

    $(document).on('click', '.alarmHistory_edit, .downHistory_edit', function(e) {
        $('#recordListModalID').css("display", "block");
        $('#recordListModalID').removeData();
        $("input[name='form_zone']").attr("readonly", true).css('background-color', '#DEDEDE');
        $("input[name='form_locationID']").attr("readonly", true).css('background-color', '#DEDEDE');
        $("input[name='form_alarmTime']").attr("readonly", true).css('background-color', '#DEDEDE');
        $("input[name='form_lastUpdateTime']").attr("readonly", true).css('background-color', '#DEDEDE');
        $("input[name='form_status']").attr("readonly", true).css('background-color', '#DEDEDE');
        
        var rowid = $(this).parent().parent().attr('id');
        //alert(rowid);
        $('#recordListModalID').data('updateRowID', rowid.slice(3));

        $("#rootCauseSelect").val("");
        $("textarea[name='form_comment']").val("");

        if ($(this).attr("class") == "alarmHistory_edit") {
            $("#lastAlarmTimeDIVid").show();
            $("#lastUpdateTimeDIVid").hide();
            $('#recordListModalID').data("alarmORdown", "updateAlarmHistory");

            $("input[name='form_zone']").val($("#"+rowid+" td:nth-child("+zoneColIndex+")").text());
            $("input[name='form_locationID']").val($("#"+rowid+" td:nth-child("+locIDColIndex+")").text());
            $("input[name='form_alarmTime']").val($("#"+rowid+" td:nth-child("+alarmTimeColIndex+")").text());
            $("input[name='form_status']").val($("#"+rowid+" td:nth-child("+statusColIndex+")").text());

            var currentCause = $("#"+rowid+" td:nth-child("+rootCauseColIndex+")").text();
            var currentCauseID = $("#rootCauseSelect option[value="+currentCause+"]").attr("id");
            $("#rootCauseSelect").val($("#"+currentCauseID).text()).change();

            $("textarea[name='form_comment']").val($("#"+rowid+" td:nth-child("+commentColIndex+")").text());
        } else {
            $("#lastAlarmTimeDIVid").hide();
            $("#lastUpdateTimeDIVid").show();
            $('#recordListModalID').data("alarmORdown", "updateDownHistory");

            $("input[name='form_zone']").val($("#"+rowid+" td:nth-child("+zoneColIndex+")").text());
            $("input[name='form_locationID']").val($("#"+rowid+" td:nth-child("+locIDColIndex+")").text());
            $("input[name='form_lastUpdateTime']").val($("#"+rowid+" td:nth-child("+lastStatusUpdate+")").text());
            $("input[name='form_status']").val($("#"+rowid+" td:nth-child("+statusColIndex+")").text());

            var currentCause = $("#"+rowid+" td:nth-child("+rootCauseColIndex+")").text();
            var currentCauseID = $("#rootCauseSelect option[value="+currentCause+"]").attr("id");
            $("#rootCauseSelect").val($("#"+currentCauseID).text()).change();

            $("textarea[name='form_comment']").val($("#"+rowid+" td:nth-child("+commentColIndex+")").text());
        }
    }); //END of onclick edit

    $("#alarmDownReasonModalForm").submit(function(event) {
        event.preventDefault();
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "updateCause";
        dataW.id = $('#recordListModalID').data('updateRowID');
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUser_id = $('#loggedUser_id').val();
        dataW.todo = $('#recordListModalID').data("alarmORdown");

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
            // v here is the modal fields
            //v['value'] is if name is zone, take the value of that field and store it in dataW
            if (v['name'] === 'form_rootcause') {
                dataW.rootcause_id = parseFloat($('#rootCauseSelect option:selected').attr('id').slice(7)); //ca_opt_
            } else if (v['name'] === 'form_comment') {
                dataW.comment = v['value'];
            }
        });
        //alert(JSON.stringify(dataW));
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                //alert(JSON.stringify(response));
                //if(typeof response =='object') {
                    //alert("response is object");
               //}
                if (typeof response !== 'object' && response.indexOf("Integrity constraint") > -1) {
                    alert("Looks like that value can not be updated; select different;");
                } else {
                    if (dataW.todo === "updateAlarmHistory") {
                        var js_row = response;
                        var rowpk=js_row["alarmhistory"][0]["ahid"];

                        $('#ah_'+rowpk+' td:nth-child('+rootCauseColIndex+')').html(js_row["alarmhistory"][0]["rootCause"]).attr("id", "ac_opt_"+js_row["alarmhistory"][0]["acid"]);
                        $('#ah_'+rowpk+' td:nth-child('+commentColIndex+')').html(js_row["alarmhistory"][0]["comment"]);
                       
                        $('#ah_'+rowpk)[0].scrollIntoView();
                        $('#ah_'+rowpk).css('color', 'red');

                        $("#alarmReasonChartNumbers").click();
                    } else if(dataW.todo === "updateDownHistory") {
                        var js_row = response;
                        var rowpk=js_row["downhistory"][0]["dhid"];

                        $('#dh_'+rowpk+' td:nth-child('+rootCauseColIndex+')').html(js_row["downhistory"][0]["rootCause"]).attr("id", "ac_opt_"+js_row["downhistory"][0]["acid"]);
                        $('#dh_'+rowpk+' td:nth-child('+commentColIndex+')').html(js_row["downhistory"][0]["comment"]);
                       
                        $('#dh_'+rowpk)[0].scrollIntoView();
                        $('#dh_'+rowpk).css('color', 'red');

                        $("#downReasonChartNumbers").click();
                    }
                }
            } //END of success
        }); //END of CRUD ajax call
         $('#recordListModalID').css("display", "none");
         $("body").removeClass("modal-open");
         $('.modal-backdrop').remove();
         $('#recordListModalID').removeClass("show");
    }); //END of alarmDownReasonModalForm Submit

    $(document).on('click', '#alarmReasonChartNumbers, #downReasonChartNumbers', function(e) {
        recordCountBtn = e.target.id;  

        var xaxisCauses = [];
        var yaxisCount = [];

        var dataW = {};
        dataW.loggedUser = $('#loggedUser').val();
        dataW.actionfunction = "getCauseChartData";
        if(recordCountBtn == "alarmReasonChartNumbers") {
            dataW.tblName = "alarmhistory";
            dataW.barChartSize = $("#numOfAlarms").val();
            var chartID = $('#alarmCauseChart');
            var containerID = $('#alarmReasonChartContainer');
            var canvasID = "alarmCauseChart";
            var labelString = "Root causes for fire alarm";
        } else {
            dataW.tblName = "downhistory";
            dataW.barChartSize = $("#numOfDowns").val();
            var chartID = $('#downCauseChart');
            var containerID = $('#downReasonChartContainer');
            var canvasID = "downCauseChart";
            var labelString = "Root causes for no health-signal";
        }
        
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
                        if (key == 'causes') {
                            $.each(value, function(x, y) {
                                xaxisCauses.push(y["rootCause"]);
                                yaxisCount.push(y["count"]);
                            });
                        }
                    }); // end of .each
                } //End of if response!=error
            } //END of success
        }) //END of CRUD ajax call

         drawChart(chartID, containerID, canvasID, xaxisCauses, yaxisCount, labelString);

    }); //END of onclick chartsize


    $("#alarmReasonChartNumbers").click();
    $("#downReasonChartNumbers").click();

    function loadTableReasonList(parsedObj,ahTableVar, dhTableVar) {
        //alert(JSON.stringify(parsedObj));
        $.each(parsedObj, function(key, value) {
            if (key == 'alarmhistory') {
                $.each(value, function(x, y) {
                    ahTableVar += '<tr id="ah_' + y['ahid'] + '"> \
                                    <td>' + y['zone'] + '</td> \
                                    <td>' + y['locationID'] + '</td> \
                                    <td>' + y['lastAlarmTime'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + y['rootCause'] + '</td> \
                                    <td>' + y['comment'] + '</td> \
                                    <td>' + editAHIcon + '</td> \
                                  </tr>';
                    });
                $('#alarmReasonTbl > tbody:last').append(ahTableVar);
                //ahDataTableObject = $('#alarmReasonTbl').DataTable();
            } else if (key == 'downhistory') {
                $.each(value, function(x, y) {
                    dhTableVar += '<tr id="dh_' + y['dhid'] + '"> \
                                    <td>' + y['zone'] + '</td> \
                                    <td>' + y['locationID'] + '</td> \
                                    <td>' + y['lastStatusUpdate'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + y['rootCause'] + '</td> \
                                    <td>' + y['comment'] + '</td> \
                                    <td>' + editDHIcon + '</td> \
                                  </tr>';
                    });
                $('#downReasonTbl > tbody:last').append(dhTableVar);
                //dhDataTableObject = $('#downReasonTbl').DataTable();
            } else if (key == 'causes') {
                $.each(value, function(x,y){
                    $('#rootCauseSelect').append($("<option/>", {
                        id: "ac_opt_"+y['id'],
                        value: y['rootCause'],
                        text: y['rootCause']
                    }));
                });
            }
        }); // end of .each
    }

   
    function drawChart(chartID, containerID, canvasID, causeList, datas, labelString) {
        //chartID is # concated with canvasID;
        chartID.remove(); 
        containerID.append('<canvas id="'+canvasID+'" style="height:250px;width:content-box;"></canvas>');

        var chartData = {
            //labels for x axis
            labels: causeList,
            //datasets is an array of js objects
            datasets: [
                {
                    label : "Causes", //label for legend name
                    data: datas,
                    backgroundColor: ["#F39C12", "#C39BD3","#EC7063","#85C1E9","#F5CBA7","#AAB7B8"],
                    strokeColor: "black",
               }
            ]
        };
       
        //alert(JSON.stringify(chartData));

        var opt = {
            responsive: true, 
            maintainAspectRatio: false,
            events: false,
            tooltips: {
                enabled: false
            },
            legend: {
                display: false
            },
            hover: {
                animationDuration: 0
            },
            scales: {
                yAxes: [{
                    gridLines: {
                        display:true,
                        lineWidth: 2
                    },
                    ticks: {
                        min: 0, // it is for ignoring negative step.
                        beginAtZero: true,
                        //to remove 0.5 as step size
                        /*callback: function(value, index, values) {
                            if (Math.floor(value) === value) {
                                return value;
                            }
                        },*/
                        stepSize: 1,
                        fontStyle: "bold",
                    }, 
                }],
                xAxes: [{
                    gridLines: {
                        display:false
                    },
                    barThickness : 40,
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
                        labelString: labelString,
                        fontColor: "red",
                        fontSize: 18,
                    }
                }]
            },
            animation: {
              onComplete: function () {
                var chartInstance = this.chart;
                var ctx = chartInstance.ctx;
                ctx.textAlign = "right";
                ctx.font = "14px Arial";
                ctx.fillStyle = "black"; 
                ctx.textAlign = "center";
                ctx.textBaseline = "center";
                Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                  var meta = chartInstance.controller.getDatasetMeta(i);
                  Chart.helpers.each(meta.data.forEach(function (bar, index) {
                    ctx.save();
                    // Translate 0,0 to the point you want the text
                    if(dataset.data[index] == 0) {
                        ctx.translate(bar._model.x, bar._model.y);
                    } else {
                        ctx.translate(bar._model.x, bar._model.y+50);
                    }

                    // Rotate context by -90 degrees
                    //ctx.rotate(-Math.PI / 2);

                    // Draw text //fillText is HTML5 canvas function
                    ctx.fillText(dataset.data[index], 0, 0);
                    ctx.restore();
                  }),this)
                }),this);
              }
            }
        };

        var ctx = $("#"+canvasID);
        thisBarChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: opt
        });
    }
});