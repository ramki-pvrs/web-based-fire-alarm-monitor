var locAddEditBtn = '';
var primaryContactFromModal = '';
var locSubmitCancelBtn = '';
var editIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="location_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var DataTableObject = null;
var js_locationsData = {};

var selectedLocationIDs = [];
var location_ids = [];

//table column index
var zoneColIndex = 1;
var locIDColIndex = 2;
var panelIDColIndex = 3;
var locNameColIndex = 4;
var contactColIndex = 5;
var emailColIndex = 6;
var primaryColIndex = 7;
var alternateColIndex = 8;
var alarmColIndex = 9;
var lastAlarmColIndex = 10;
var commentColIndex = 11;
var statusColIndex = 12;
var editColIndex = 13;

$(document).ready(function() {
    window.name = 'locationlist';
    if ($("#loggedUserRole").val() == "SuperAdmin") {
        $("#adminNavLItag").show();
    } else {
        $("#adminNavLItag").hide();
    }
    $.fn.dataTable.moment('DD/MM/YY');
    
    permitNavLinks();
    $("#logoutLItag").hide();



    var dataW = {};
    dataW.actionfunction = "locationZoneList";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    var locationTbl = '';
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
                loadTableZoneList(response,locationTbl);
                $("div.addbutton").html('<button class="btn btn-secondary float-left ml-2"\
                              id="multiRecordAddBtn" data-toggle="modal" \
                              data-target="#locationGroupModalID">Location Group <-> Contact Map</button><button class="btn btn-secondary float-right ml-4 mr-4"\
                              id="recordAddBtn" data-toggle="modal" \
                              data-target="#recordListModalID">ADD</button>');
                
                $.each(response, function(key, value) {
                    if(key == 'userPermissionValue') {
                        //alert(value);
                        if ($("#loggedUserRole").val() == "SuperAdmin") {
                            //dont do anything
                        } else {
                            //var addBtn = $("#recordAddBtn");
                            //alert($("#recordAddBtn").attr("id"));
                           afterAjaxCRUDPermission(parseFloat(value), $("#recordAddBtn"), $(".location_edit"), $(".fa-edit")); 
                        }
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call
    //$('#locgrp_locIDSelect').find('option').remove();
        

    $('#locgrp_zoneSelect').on('change', function (e) {
        //alert($(this).val());
        if($(this).val() == "") {
            $('#locgrp_locIDSelect').find('option').remove();
            $('#locgrp_locIDSelect').multiselect('rebuild');
            $('#locgrp_locIDSelect').multiselect('deselectAll', false);
            $("#locgrp_locIDSelect").multiselect('updateButtonText');
            $('#locgrp_locIDSelect').multiselect('enable');
            return false;
        }
        var dataW = {};
        dataW.actionfunction = "getLocationsOfZone";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUserRole = $('#loggedUserRole').val();
        dataW.zone_id = parseFloat($('#locgrp_zoneSelect option:selected').attr('id').slice(13)); //locgrp_z_opt_
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
                    $.each(response, function(key, value) {
                        if(key == 'locationIDs') {
                            $.each(value, function(x,y){
                                $('#locgrp_locIDSelect').append($("<option/>", {
                                    id: "lid_opt_"+y['lid'],
                                    value: y['locationID'],
                                    text: y['locationID']
                                }));
                            });
                            $('#locgrp_locIDSelect').multiselect('rebuild');
                            $('#locgrp_locIDSelect').multiselect('deselectAll', false);
                            $("#locgrp_locIDSelect").multiselect('updateButtonText');
                            $('#locgrp_locIDSelect').multiselect('enable');
                        }
                    }); //end of each for permissionBit
                } //End of if response!=error
            } //END of success
        }); //END of CRUD ajax call
    }); //END of on zone select

    //https://stackoverflow.com/questions/20401703/bootstrap-multiselect-update-option-list-on-flow
    //



    $('#locgrp_locIDSelect').multiselect({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: '800px',
            onDropdownHide: function(event) {
                //console.log($('#entitySelect').val());
                //selectedLocationIDs = [];
                //selectedLocationIDs = $('#locgrp_locIDSelect').val();
                //alert(selectedLocationIDs);
                  var locations = $('#locgrp_locIDSelect option:selected');
                  location_ids = [];
                  $(locations).each(function(index, location){
                    //alert($(this).attr('id').split("_")[2]);
                        location_ids.push($(this).attr('id').split("_")[2]);
                  });
                  //alert(location_ids);
            }
    });
    $('#locgrp_locIDSelect').multiselect('rebuild');
    $('#locgrp_locIDSelect').multiselect('deselectAll', false);
    $("#locgrp_locIDSelect").multiselect('updateButtonText');
    $('#locgrp_locIDSelect').multiselect('enable');

     $(document).on('click', '#importFileBtn', function(e) {
        alert("Make sure it is .xlsx file and first sheet is named 'locations'");

     });

    $(document).on('click', '.location_edit, #recordAddBtn', function(e) {
        $('#recordListModalID').css("display", "block");
        $("#zoneSelect").val('Select');
        $("input[name='form_locationID']").val('');
        $("input[name='form_panelID']").val('');
        $("input[name='form_locationName']").val('');
        $("input[name='form_email']").val('');
        $("input[name='form_primaryContact']").val('');
        $("input[name='form_contactName']").val('');
        $("textarea[name='form_comment']").val('');
        $("#statusSelect").val('Active').change();
        $('#recordListModalID').removeData();
        locAddEditBtn = e.target.id;
        if (locAddEditBtn == "recordAddBtn") {
            $("#locationModalTitle").html("Add Location");
            $("input[name='form_locationID']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("input[name='form_email']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("input[name='form_primaryContact']").attr("readonly", false).css('background-color', '#FCF3CF');
            $("input[name='form_contactName']").attr("readonly", true).css('background-color', '#DEDEDE');
            $("#z_0").prop('disabled', false).css('background-color', '#FFFFFF');
            $('#zoneSelect option:not(:selected)').prop('disabled', false).css('background-color', '#FFFFFF');
            $('#statusSelect option:not(:selected)').prop('disabled', false).css('background-color', '#FFFFFF');
            $("#modalClearBtn").show();
            $("#hideStatusSelect").hide();
        } else {
            //alert($('#statusSelect option[value="Down"]').attr("id"));
            //$("select option[value='Down']").prop("disabled", true);
            //$("#s_0").prop("disabled", true);
            $("#locationModalTitle").html("Update Location (Zone and LocationID being unique, can not be edited)");
            $("#modalClearBtn").hide();
            $("#hideStatusSelect").show();
            $("#z_0").prop('disabled', true).css('background-color', '#DEDEDE');
            $('#zoneSelect option:not(:selected)').prop('disabled', false).css('background-color', '#FFFFFF');
            $("input[name='form_locationID']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("input[name='form_email']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("input[name='form_primaryContact']").attr("readonly", false).css('background-color', '#FCF3CF');
            $("input[name='form_contactName']").attr("readonly", true).css('background-color', '#DEDEDE');

            var rowid = $(this).parent().parent().attr('id');
            //alert(rowid);
            $('#recordListModalID').data('locationRowID', rowid.slice(2));
            var currentZoneID = $("#"+rowid+" td:nth-child("+zoneColIndex+")").attr("id");
            $('#recordListModalID').data('zone_id', currentZoneID);
            $("#zoneSelect").val($("#"+currentZoneID).text()).change();

            var currentContactID = $("#"+rowid+" td:nth-child("+contactColIndex+")").attr("id");
            //alert(currentContactID);
            $('#recordListModalID').data('currentContact_id', currentContactID);
            //alert($('#recordListModalID').data('currentContact_id'));

            $("input[name='form_locationID']").val($("#"+rowid+" td:nth-child("+locIDColIndex+")").text());
            $("input[name='form_panelID']").val($("#"+rowid+" td:nth-child("+panelIDColIndex+")").text());
            $("input[name='form_locationName']").val($("#"+rowid+" td:nth-child("+locNameColIndex+")").text());
            $("input[name='form_contactName']").val($("#"+rowid+" td:nth-child("+contactColIndex+")").text());
            $("input[name='form_email']").val($("#"+rowid+" td:nth-child("+emailColIndex+")").text());
            $("input[name='form_primaryContact']").val($("#"+rowid+" td:nth-child("+primaryColIndex+")").text());
            $("textarea[name='form_comment']").val($("#"+rowid+" td:nth-child("+commentColIndex+")").text());
            var currentStatus = $("#"+rowid+" td:nth-child("+statusColIndex+")").text();
            var currentStatusID = $("#statusSelect option[value="+currentStatus+"]").attr("id");
            $("#statusSelect").val($("#"+currentStatusID).text()).change();
            if(currentStatus == "Down") {
                $('#statusSelect option:not(:selected)').prop('disabled', true).css('background-color', '#DEDEDE');
            } else {
                $('#statusSelect option:not(:selected)').prop('disabled', false).css('background-color', '#FFFFFF');
                //Down option can not be set by user; it is set by MySQL event when signal age is more than X seconds
                document.getElementById("statusSelect").options[0].disabled = true;
            }
        } // END of if idClicked chk
    }); //END of onclick loc edit


    $('#form_primaryContact, #locgrp_form_primaryContact').on('blur', function(e){
        $("#form_contactName").val('');
        $("#locgrp_form_contactName").val('');
        //$('#locgrp_form_primaryContact').val('');
        primaryContactFromModal = e.target.id;
        //alert(primaryContactFromModal);
        var dataW = {};
        dataW.actionfunction = "getContactName";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUserRole = $('#loggedUserRole').val();
        //alert($('#locgrp_form_primaryContact').val());

        if(primaryContactFromModal == 'form_primaryContact') {
            dataW.primaryContact = $('#form_primaryContact').val();
        } else if(primaryContactFromModal == 'locgrp_form_primaryContact') {
            dataW.primaryContact = $('#locgrp_form_primaryContact').val();
        }
        
        //alert(JSON.stringify(dataW));
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                //alert(JSON.stringify(response));
                if(response["contacts"][0]["count"] == 1) {
                    if (response != 'error') {
                        //js_zoneLocationsData = $.parseJSON(response);
                        $.each(response, function(key, value) {
                            if(key == 'contacts') {
                                $.each(value, function(x,y){
                                    if(primaryContactFromModal == 'form_primaryContact') {
                                        $("#form_contactName").val(y["contactName"]);
                                        //$("#form_primaryContact").val(y["primaryContact"]);
                                        //on update, if contact primaryContact is changed, new contact id value is stored in modal data
                                        $('#recordListModalID').data('newContact_id', "conid_"+y["id"]);
                                        //alert($('#recordListModalID').data('newContact_id'));
                                    } else if(primaryContactFromModal == 'locgrp_form_primaryContact') {
                                        $("#locgrp_form_contactName").val(y["contactName"]);
                                        $('#locationGroupModalID').data('contact_id', "locgrp_conid_"+y["id"]);
                                    }
                                });
                            }
                        }); //end of each for permissionBit
                    } //End of if response!=error
                } else if(response["contacts"][0]["count"] == 0) {
                    alert("No contact person/team with that number; Check Contacts list and update again! If not found in Contacts, add and update");
                }
            } //END of success
        }); //END of CRUD ajax call
    });

    $("#locationListModalForm").submit(function(event) {
        //alert($('#taskModalID').parent().parent().prop('tagName'));
        //console.log( $( this ).serializeArray() );
        //DataTableObject.destroy();
        event.preventDefault();
        //alert($('#recordListModalID').data('contact_id'));
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "insertORupdateLocation";
        dataW.loggedUser = $('#loggedUser').val();
        if (locAddEditBtn != "recordAddBtn") { //locationEditBtn = true
            dataW.todo = "update";
            //data property of the modal is set when a .location_edit event is fired and used here;
            dataW.id = $('#recordListModalID').data('locationRowID');
            dataW.zone_id = $('#recordListModalID').data('zone_id').split("_")[4]; //z_td_
            if($('#recordListModalID').data('currentContact_id') === undefined) {
               dataW.currentContact_id =  0;
            } else {
               dataW.currentContact_id =  $('#recordListModalID').data('currentContact_id').split("_")[3]; //conid_ 
            }
            if($('#recordListModalID').data('newContact_id') === undefined) {
                    dataW.newContact_id =  dataW.currentContact_id;
            } else {
                    dataW.newContact_id =  $('#recordListModalID').data('newContact_id').split("_")[1]; //conid_
            } 
            
        } else {
            dataW.todo = "insert";
            dataW.id =  '';
            dataW.zone_id =  parseFloat($('#zoneSelect option:selected').attr('id').slice(6)); //z_opt_
            dataW.currentContact_id =  0; // on new location add, currentContact_id is set to 0 so that we do not get undefined error from php
            if($('#recordListModalID').data('newContact_id') === undefined) {
               dataW.newContact_id =  0;
            } else {
               dataW.newContact_id =  $('#recordListModalID').data('newContact_id').split("_")[1]; //conid_ 
            }
            dataW.status = 0; //not used in insert though
            //alert($('#recordListModalID').data('newContact_id').split("_")[1]);
        }

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
            // v here is the modal fields
            //v['value'] is if name is zone, take the value of that field and store it in dataW
            if (v['name'] === 'form_zone') {
                //get the id of the Zone option selected; get only number from for example z_1
                dataW.zone_id = parseFloat($('#zoneSelect option:selected').attr('id').slice(6)); //z_opt_
            } else if (v['name'] === 'form_locationID') {
                dataW.locID = v['value'];
            } else if (v['name'] === 'form_panelID') {
                dataW.panelID = v['value'];
            } else if (v['name'] === 'form_locationName') {
                dataW.locName = v['value'];
            } 

            /*else if (v['name'] === 'form_contactName') {
                dataW.contactName = v['value'];
            } else if (v['name'] === 'form_email') {
                dataW.email = v['value'];
            } else if (v['name'] === 'form_primaryContact') {
                dataW.primary = v['value'];
            }*/

            else if (v['name'] === 'form_comment') {
                dataW.comment = v['value'];
            } else if (v['name'] === 'form_status') {
                //alert(parseFloat(v['value']));
                dataW.status =  parseFloat($('#statusSelect option:selected').attr('id').slice(2));
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
                    alert("Looks like that zone and location combo exists already!; use Edit.");
                } else {
                    if (dataW.todo === "insert") {
                        //var js_row = $.parseJSON(response);
                        //alert(JSON.stringify(js_row));
                        var js_row = response;
                        //alert(JSON.stringify(response));
                        var thisnewRow = '';
                        var rowCount = DataTableObject.rows().count();
                        var rowNode = DataTableObject
                        .row.add([js_row["locations"][0]["zone"], js_row["locations"][0]["locationID"], js_row["locations"][0]["panelID"],
                                  js_row["locations"][0]["locationName"], js_row["locations"][0]["contactName"], 
                                  js_row["locations"][0]["email"],
                                  js_row["locations"][0]["primaryContact"], js_row["locations"][0]["alternateContact"], 
                                  js_row["locations"][0]["lastAlarm"], js_row["locations"][0]["lastAlarmTime"], 
                                  js_row["locations"][0]["comment"], js_row["locations"][0]["status"], editIcon])
                        .draw()
                        .node();
                        $(rowNode)
                            .css('color', 'red')
                            .animate({ color: 'black' })
                            .attr('id', "l_"+js_row["locations"][0]["lid"]);

                        var rowid = $(rowNode).attr("id");

                        $("#"+rowid+" td:nth-child("+zoneColIndex+")").attr("id", "row_"+rowCount+"_z_td_"+js_row["locations"][0]["zid"]);
                        $("#"+rowid+" td:nth-child("+contactColIndex+")").attr("id", "row_"+rowCount+"_conid_"+js_row["locations"][0]["conid"]).attr("value", js_row["locations"][0]["email"]);
                        $('#'+rowid)[0].scrollIntoView();
                        //loadTable(js_row, thisnewRow);
                    } else {
                        //var js_row = $.parseJSON(response);
                        //alert(JSON.stringify(js_row));
                        //locations is the js object, with one array element so index 0 and that array is of js objects again
                        var js_row = response;
                        var rowpk=js_row["locations"][0]["lid"];
                        //when the table body rows are loaded with loadTable function, each tbody > tr is set with id attribute
                        // like l_+dB id; so each row can be accessed with concatenated l_ + rowpk
                        $('#l_'+rowpk+' td:nth-child('+zoneColIndex+')').html(js_row["locations"][0]["zone"]);
                        $('#l_'+rowpk+' td:nth-child('+locIDColIndex+')').html(js_row["locations"][0]["locationID"]);
                        $('#l_'+rowpk+' td:nth-child('+locNameColIndex+')').html(js_row["locations"][0]["locationName"]);
                        $('#l_'+rowpk+' td:nth-child('+contactColIndex+')').html(js_row["locations"][0]["contactName"]);
                        $('#l_'+rowpk+' td:nth-child('+emailColIndex+')').html(js_row["locations"][0]["email"]);
                        $('#l_'+rowpk+' td:nth-child('+primaryColIndex+')').html(js_row["locations"][0]["primaryContact"]);
                        $('#l_'+rowpk+' td:nth-child('+alternateColIndex+')').html(js_row["locations"][0]["alternateContact"]);
                        $('#l_'+rowpk+' td:nth-child('+alarmColIndex+')').html(js_row["locations"][0]["lastAlarm"]);
                        $('#l_'+rowpk+' td:nth-child('+lastAlarmColIndex+')').html(js_row["locations"][0]["lastAlarmTime"]);
                        $('#l_'+rowpk+' td:nth-child('+commentColIndex+')').html(js_row["locations"][0]["comment"]);
                        $('#l_'+rowpk+' td:nth-child('+statusColIndex+')').html(js_row["locations"][0]["status"]);

                        //set the contact table id as id and email as value for contactName td element
                        var rowIndex = $('#l_'+rowpk).index();
                        $('#l_'+rowpk+' td:nth-child('+zoneColIndex+')').attr("id", "row_"+rowIndex+"_z_td_"+js_row["locations"][0]["zid"]);
                        $('#l_'+rowpk+' td:nth-child('+contactColIndex+')').attr("id", "row_"+rowIndex+"_conid_"+js_row["locations"][0]["conid"]);
                        

                        $('#l_'+rowpk)[0].scrollIntoView();
                        $('#l_'+rowpk).css('color', 'red');
                    }
                }
            } //END of success

        }); //END of CRUD ajax call
          //$('#recordListModalID').modal('hide');
           //$("#my_modal").modal("hide");
          //$("body").removeClass("modal-open");
          //$('.modal-backdrop').remove();
          //$('#recordListModalID').on('hidden.bs.modal', function(){
              //remove the backdrop
              //$('.modal-backdrop').remove();
         // })
         $('#recordListModalID').css("display", "none");
         $("body").removeClass("modal-open");
         $('.modal-backdrop').remove();
         $('#recordListModalID').removeClass("show");
    }); //END of locationListModalForm Submit

    $("#locationGroupModalForm").submit(function(event) {
        event.preventDefault();
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "updateLocationGroupContact";
        dataW.loggedUser = $('#loggedUser').val();

       //location_ids is jquery global var coming from above
        dataW.location_ids = location_ids;
        dataW.contact_id = $('#locationGroupModalID').data('contact_id').split("_")[2];
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
          if (v['name'] === 'locgrp_form_comment') {
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
                window.location.reload();
            }, //END of success
            complete: function(){
                $('#locgrp_locIDSelect').multiselect('deselectAll', false);
                $("#locgrp_locIDSelect").multiselect('updateButtonText');
                $('#locgrp_locIDSelect').multiselect('enable');
            }

        }); //END of CRUD ajax call
         $('#locationGroupModalID').css("display", "none");
         $("body").removeClass("modal-open");
         $('.modal-backdrop').remove();
         $('#locationGroupModalID').removeClass("show");
    }); //END of locationGroupModalForm Submit

    function loadTableZoneList(parsedObj,tableVar) {
        $.each(parsedObj, function(key, value) {
            if (key == 'locations') {
                $.each(value, function(x, y) {
                    tableVar += '<tr id="l_' + y['lid'] + '"> \
                                    <td id="row_'+x+'_z_td_' + y['zid'] + '">' + y['zone'] + '</td> \
                                    <td>' + y['locationID'] + '</td> \
                                    <td>' + y['panelID'] + '</td> \
                                    <td>' + y['locationName'] + '</td> \
                                    <td id="row_'+x+'_conid_' + y['conid'] + '">' + y['contactName'] + '</td> \
                                    <td>' + y['email'] + '</td> \
                                    <td>' + y['primaryContact'] + '</td> \
                                    <td>' + y['alternateContact'] + '</td> \
                                    <td>' + y['lastAlarm'] + '</td> \
                                    <td>' + y['lastAlarmTime'] + '</td> \
                                    <td>' + y['comment'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + editIcon + '</td> \
                                  </tr>';
                    });
                $('#locationListTbl > tbody:last').append(tableVar);
                DataTableObject = $('#locationListTbl').DataTable({
                    "scrollY":        "900px",
                    "scrollCollapse": true,
                    "paging":         true,
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    //https://datatables.net/examples/advanced_init/dom_toolbar.html
                    "dom": '<"addbutton">frtip'
                });
            } // END if key=locations
            else if (key == 'zones') {
                $.each(value, function(x,y){
                    $('#zoneSelect').append($("<option/>", {
                        id: "z_opt_"+y['id'],
                        value: y['zone'],
                        text: y['zone']
                    }));
                    $('#locgrp_zoneSelect').append($("<option/>", {
                        id: "locgrp_z_opt_"+y['id'],
                        value: y['zone'],
                        text: y['zone']
                    }));
                });
            }
        }); // end of .each
    }
    
});