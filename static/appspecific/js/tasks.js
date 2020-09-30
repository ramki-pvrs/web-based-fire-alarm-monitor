var taskAddEditBtn = '';
var taskSubmitCancelBtn = '';
var editIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="task_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var DataTableObject = null;

//table column index
var taskColIndex = 1;
var creatrorColIndex = 2;
var ownerColIndex = 3;
var duedateColIndex = 4;
var statusColIndex = 5;
var commentColIndex = 6;
var editColIndex = 7;


$(document).ready(function() {
    window.name = 'tasklist';
    if ($("#loggedUserRole").val() == "SuperAdmin") {
        $("#adminNavLItag").show();
    } else {
        $("#adminNavLItag").hide();
    }
    $.fn.dataTable.moment('DD/MM/YY');

    permitNavLinks();
    $("#logoutLItag").hide();
    
    var dataW = {};
    dataW.actionfunction = "taskList";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    var taskTbl = '';
    $.ajax({
        url: "CRUD.php",
        cache: false,
        type: "POST",
        data: { dbData: dataW },
        async: false,
        success: function(response) {
            if (response != 'error') {
                //js_zoneLocationsData = $.parseJSON(response);
                loadTableTaskList(response,taskTbl);
                $("div.addbutton").html('<button class="btn btn-secondary float-right ml-4 mr-4  addBtnClass"\
                              id="recordAddBtn" data-toggle="modal" \
                              data-target="#recordListModalID">Add</button>');
                $.each(response, function(key, value) {
                    if(key == 'userPermissionValue') {
                        if ($("#loggedUserRole").val() == "SuperAdmin") {
                            //dont do anything
                        } else {
                           afterAjaxCRUDPermission(parseFloat(value), $("#recordAddBtn"), $(".task_edit"), $(".fa-edit")); 
                        }
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call

    $(document).on('click', '.task_edit, #recordAddBtn', function(e) {
        $('#recordListModalID').css("display", "block");

        $("input[name='form_task']").val('');
        $("textarea[name='form_comment']").val('');

        //$("#creator_id").val('Select');
        $("#ownerSelect").val('');
        $("#statusSelect").val('Open');

        $('#recordListModalID').removeData();
        taskAddEditBtn = e.target.id;
        if (taskAddEditBtn == "recordAddBtn") {
            $("#taskModalTitle").html("Add Task");
            $("input[name='form_creator']").val($('#loggedUserName_id').val());
            $("input[name='form_creator']").attr("id", $('#loggedUser_id').val());
            
            var today = moment().format('YYYY-MM-DD');
            $("#duedefault").val(today);
            $("#showStatesOnEdit").hide();
            $("#modalClearBtn").show();
            $("#taskModalTitle").html("Add Task");
        } else {
            $("#taskModalTitle").html("Update Task");

            $("#showStatesOnEdit").show();
            $("#modalClearBtn").hide();

            var rowid = $(this).parent().parent().attr('id');
            $('#recordListModalID').data('taskRowID', rowid.slice(5));

            var creatorid = $("#"+rowid+" td:nth-child("+creatrorColIndex+")").attr("id");
            //alert(creatorid);
            var ownerid = $("#"+rowid+" td:nth-child("+ownerColIndex+")").attr("id");

            //var creator = $("#14_tc_td_14").text();
            //alert(creator);
            //alert($("#14_tc_td_14").text());
             //var creatorVal = $("#"+creatorid).val();
             //alert(creatorVal);

            //split by _ gives ["","row","tc","td","id"]; 0th index is empty; so 4 gives only id number 
            $('#recordListModalID').data('creatorid', creatorid.split("_")[4]);
            $('#recordListModalID').data('ownerid', ownerid.split("_")[4]);
            $("input[name='form_task']").val($("#"+rowid+" td:nth-child("+taskColIndex+")").text());
            //alert($("#"+creatorid).text());
            //$("#creatorSelect option[]").prop('selected',true);
            //$('#creatorSelect option#tc_opt_2').prop('selected',true);
            //$("#creatorSelect").val($("#"+creatorid).text()).change();
            //$("#ownerSelect").val($("#"+ownerid).text()).change();
            //$("#creatorSelect").val(creatorVal);
            //$("#creatorSelect").val($("#"+creatorid).text()).change();
            $("input[name='form_creator']").val($("#"+rowid+" td:nth-child("+creatrorColIndex+")").text());
            $("#ownerSelect").val($("#"+ownerid).text()).change();
            $("input[name='form_duedate']").val($("#"+rowid+" td:nth-child("+duedateColIndex+")").text());
            $("input[name='form_status']").val($("#"+rowid+" td:nth-child("+statusColIndex+")").text());
            //alert($("#"+rowid+" td:nth-child("+commentColIndex+")").text());
            $("textarea[name='form_comment']").val($("#"+rowid+" td:nth-child("+commentColIndex+")").text());
        } // END of if idClicked chk
    }); //END of onclick loc edit


    
    $("#taskListModalForm").submit(function(event) {
        event.preventDefault();
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "insertORupdateTask";
        dataW.loggedUser = $('#loggedUser').val();
        if (taskAddEditBtn != "recordAddBtn") { //editbutton clicked
            dataW.todo = "update";
            //data property of the modal is set when a .task_edit event is fired and used here;
            dataW.id = $('#recordListModalID').data('taskRowID');
            dataW.tcid = $('#recordListModalID').data('creatorid');
            dataW.taid = $("#ownerSelect option:selected").attr("id").slice(7);
        } else {
            dataW.todo = "insert";
            dataW.id =  '';
            dataW.tcid = $('#loggedUser_id').val();
            dataW.taid = $("#ownerSelect option:selected").attr("id").slice(7);
        }

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(key, value) {
            if (value['name'] === 'form_task') {
                dataW.task = value['value'];
            } else if (value['name'] === 'form_duedate') {
                dataW.dueDate = value['value'];
            } else if (value['name'] === 'form_status') {
                dataW.status = value['value'];
            } else if (value['name'] === 'form_comment') {
                dataW.comment = value['value'];
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
                if (typeof response !== 'object' && response.indexOf("Integrity constraint") > -1) {
                    alert("Looks like that zone and location combo exists already!; use Edit.");
                } else {
                    if (dataW.todo === "insert") {
                        var js_row = response;
                        var thisnewRow = '';
                        //rowCount is starting from 1
                        //whereas x used when the table got created when the page loads start from 0
                        // so 0_tc_td_ would be the starting for example
                        var rowCount = DataTableObject.rows().count();
                        //alert(rowCount);

                        var rowNode = DataTableObject
                        .row.add([js_row["tasks"][0]["task"], js_row["tasks"][0]["tcfullName"],
                                  js_row["tasks"][0]["tafullName"], js_row["tasks"][0]["dueDate"], 
                                  js_row["tasks"][0]["status"], js_row["tasks"][0]["comment"], editIcon])
                        .draw()
                        .node();
                      
                        $(rowNode)
                            .css('color', 'red')
                            .animate({ color: 'black' })
                            .attr('id', "todo_"+js_row["tasks"][0]["id"]);

                        var rowid = $(rowNode).attr("id");
                        $("#"+rowid+" td:nth-child("+creatrorColIndex+")").attr("id", "row_"+rowCount+"_tc_td_"+js_row["tasks"][0]["tcid"]);
                        $("#"+rowid+" td:nth-child("+ownerColIndex+")").attr("id", "row_"+rowCount+"_ta_td_"+js_row["tasks"][0]["taid"]);
                        $('#'+rowid)[0].scrollIntoView();
                    } else {
                        var js_row = response;
                        var rowid=js_row["tasks"][0]["id"];
                        if(js_row["tasks"][0]["status"] === "Completed") {
                            $('#todo_'+rowid).remove();
                        } else {
                            $('#todo_'+rowid+' td:nth-child('+taskColIndex+')').html(js_row["tasks"][0]["task"]);
                            $('#todo_'+rowid+' td:nth-child('+creatrorColIndex+')').html(js_row["tasks"][0]["tcfullName"]);
                            $('#todo_'+rowid+' td:nth-child('+ownerColIndex+')').html(js_row["tasks"][0]["tafullName"]);
                            $('#todo_'+rowid+' td:nth-child('+duedateColIndex+')').html(js_row["tasks"][0]["dueDate"]);
                            $('#todo_'+rowid+' td:nth-child('+statusColIndex+')').html(js_row["tasks"][0]["status"]);
                            $('#todo_'+rowid+' td:nth-child('+commentColIndex+')').html(js_row["tasks"][0]["comment"]);
                            
                            $('#todo_'+rowid)[0].scrollIntoView();
                            $('#todo_'+rowid).css('color', 'red');
                        }
                    }
                }
            } //END of success

        }); //END of CRUD ajax call
         $('#recordListModalID').css("display", "none");
         $("body").removeClass("modal-open");
         $('.modal-backdrop').remove();
         $('#recordListModalID').removeClass("show");
    }); //END of taskListModalForm Submit

    function loadTableTaskList(parsedObj,tableVar) {
        $.each(parsedObj, function(key, value) {
            if (key == 'tasks') {
                $.each(value, function(x, y) {
                    //because many tasks with same creator or owner can be there
                    //the id attribute should also have row index to uniquely id that row-td value
                    // x is the index in above function(x,y) and we wil use it

                    //in that case slice(6) alone will not help because x may be 0 or 12 or 112
                    //count two _ _ and get the string

                    tableVar += '<tr id="todo_' + y['id'] + '"> \
                                    <td>' + y['task'] + '</td> \
                                    <td id="row_'+x+'_tc_td_' + y['tcid'] + '">' + y['tcfullName'] + '</td> \
                                    <td id="row_'+x+'_ta_td_' + y['taid'] + '">' + y['tafullName'] + '</td> \
                                    <td>' + y['dueDate'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + y['comment'] + '</td> \
                                    <td>' + editIcon + '</td> \
                                  </tr>';
                    });
                //$("#taskListTbl  tbody tr").remove();
                $('#taskListTbl > tbody:last').append(tableVar);
                DataTableObject = $('#taskListTbl').DataTable({
                    "scrollY":        "900px",
                    "scrollCollapse": true,
                    "paging":         false,
                    "bAutoWidth": false, // Disable the auto width calculation 
                    "aoColumns": [
                      { "sWidth": "25%" }, // 1st column width 
                      { "sWidth": "10%" }, // 2nd column width 
                      { "sWidth": "10%" }, // ...
                      { "sWidth": "10%" },
                      { "sWidth": "10%" },
                      { "sWidth": "25%" },
                      { "sWidth": "5%" },
                    ],
                    //https://datatables.net/examples/advanced_init/dom_toolbar.html
                    "dom": '<"addbutton">frtip'
                    //"dom": '<"caption">frtip'
                });    
            } else if (key == 'users') {
                $.each(value, function(x,y){
                    //alert(x); // x is actually index
                    /*$('#creatorSelect').append($("<option/>", {
                        id: "tc_opt_"+y['id'],
                        value: y['fullName'],
                        text: y['fullName']
                    }));*/
                    //Select option in Edit Modal will be visible only when value and text are same in this jQuery version
                    //is it a bug??
                    $('#ownerSelect').append($("<option/>", {
                        id: "ta_opt_"+y['id'],
                        value: y['fullName'],
                        text: y['fullName']
                    }));
                });
            }
        }); // end of .each
        //alert(DataTableObject.rows().count());
    }
});