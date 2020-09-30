var timerButton = '';
var selectedEntities = [];

$(document).ready(function() {
    window.name = 'adminpage';
    $("#logoutLItag").hide();

    permitNavLinks();

    $(document).on('click', '#popupTimerBtn, #healthTimerBtn', function(e) {
        //alert("ramki");
        timerButton = e.target.id;

        var dataW = {};
        dataW.actionfunction = "updateTimer";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUserRole = $('#loggedUserRole').val();
        dataW.loggedUser_id = $('#loggedUser_id').val();
        dataW.loggedUserRole_id = $('#loggedUserRole_id').val();

        $("#roleSelect").val("");
        //$('#entitySelect').find('option').remove();
        $('#entitySelect').multiselect('deselectAll', false);
        $("#entitySelect").multiselect('updateButtonText');
        $('#entitySelect').multiselect('enable');
        $("#readChkBox").prop("checked", false);
        $("#updateChkBox").prop("checked", false);
        $("#createChkBox").prop("checked", false);

        if (timerButton == "popupTimerBtn") {
            dataW.whichTimer = "popupTimer";
            dataW.timerValue = $("#popupTimer").val();
        } else if (timerButton == "healthTimerBtn") {
            dataW.whichTimer = "healthTimer";
            dataW.timerValue = $("#healthTimer").val();
        }
        //alert(JSON.stringify(dataW));
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                alert("Update Successful");
            } //END of success
        }); //END of CRUD ajax call
    }); //END of onclick edit
    
    var dataW = {};
    dataW.actionfunction = "getRoleEntityData";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    dataW.loggedUser_id = $('#loggedUser_id').val();
    dataW.loggedUserRole_id = $('#loggedUserRole_id').val();
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
                    if(key == 'userPermissionValue') {
                        //alert(value);
                        if(value === 15) { 
                            // CRUD 1111
                           //all permissions for this logged in user
                        } else if(value === 6) {
                            //Update and Read permission CRUD 0110
                            //since no Create action on this page no change
                        } else if(value === 4) {
                            //only Read Permission CRUD 0100
                        } else if(value === 0) {
                            //CRUD 0000
                            //not even read permission; should work on this
                        }
                    } else if (key == 'roles') {
                        $.each(value, function(x,y){
                            if(y['role'] != "SuperAdmin") {
                                $('#roleSelect').append($("<option/>", {
                                    id: "r_opt_"+y['id'],
                                    value: y['role'],
                                    text: y['role']
                                }));
                            }
                        });
                    } else if (key == 'entities') {
                        //$('#entitySelect').children().remove();
                        $.each(value, function(x,y){
                            //alert(JSON.stringify(y['entity']));
                            //alert(JSON.stringify(y));
                            $('#entitySelect').append($("<option/>", {
                                //id: "e_opt_"+y['id'],
                                value: y['entity'],
                                text: y['entity']
                            }));
                        });
                        //$('#entitySelect').multiselect('rebuild');
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call

    //https://developer.snapappointments.com/bootstrap-select/options/
   /*$('#entitySelect').selectpicker({
                            actionsBox: true,
                            title: 'Select'
                                  //style: 'btn-primary',
                                  //size: 4
                                })*/
     //http://davidstutz.de/bootstrap-multiselect/#configuration-options-selectAllText
     $('#entitySelect').multiselect({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: '150px',
            onDropdownHide: function(event) {
                //console.log($('#entitySelect').val());
                selectedEntities = [];
                selectedEntities = $('#entitySelect').val();
                //alert(selectedEntities);
            }
    });
     //jquery.multiselect.js and .css in static/public
    /*$('#entitySelect').multiselect({
        columns: 1,
        selectAll: 'Select all',
        noneSelected   : 'None Selected'
    });*/

    

    $("#rolepermissionform").submit(function(event) {
        //alert(selectedEntities);
        event.preventDefault();
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "updatePermission";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUser_id = $('#loggedUser_id').val();
        //alert(document.activeElement.id);

       if(document.activeElement.id=="addPermissionBtn"){
        dataW.todo = "Update";
       } else {
        dataW.todo = "Delete";
       }

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
            // v here is the modal fields
            //v['value'] is if name is zone, take the value of that field and store it in dataW
            if (v['name'] === 'form_role') {
                dataW.role_id = parseFloat($('#roleSelect option:selected').attr('id').slice(6)); //r_opt_
            } else if (v['name'] === 'form_entity') {
                dataW.entities = $('#entitySelect').val();
            }
        });
        
        dataW.Read = document.getElementById("readChkBox").checked;
        dataW.Update = document.getElementById("updateChkBox").checked;
        dataW.Create = document.getElementById("createChkBox").checked;

        if(dataW.Read == false && dataW.Update == false && dataW.Create == false) {
            alert("One of permission options in Read, Update, Create must be selected");
            return false;
        }
        //alert(JSON.stringify(dataW));
        $.ajax({
            url: "CRUD.php",
            cache: false,
            type: "POST",
            data: { dbData: dataW },
            async: false,
            success: function(response) {
                alert("update successful!");
            }, //END of success
            complete: function() {
              $("#roleSelect").val("");
              //$("#entitySelect").val("");
              $('#entitySelect').multiselect('deselectAll', false);
              $("#entitySelect").multiselect('updateButtonText');
              $('#entitySelect').multiselect('enable');
              $("#readChkBox").prop("checked", false);
              $("#updateChkBox").prop("checked", false);
              $("#createChkBox").prop("checked", false);
            }
        }); //END of CRUD ajax call
    }); //END of alarmDownReasonModalForm Submit
});