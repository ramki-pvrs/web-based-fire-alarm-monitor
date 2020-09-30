var userAddEditBtn = '';
var userSubmitCancelBtn = '';
var editIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="user_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var DataTableObject = null;
var js_usersData = {};

//table column index
var empIDColIndex = 1;
var firstNameColIndex = 2;
var middleNameColIndex = 3;
var lastNameColIndex = 4;
var userIDColIndex = 5;
var emailColIndex = 6;
var titleColIndex = 7;
var primaryColIndex = 8;
var alternateColIndex = 9;
var managerColIndex = 10;
var userTypeColIndex = 11;
var roleColIndex = 12;
var statusColIndex = 13;
var editColIndex = 14;

$(document).ready(function() {
    window.name = 'userlist';
    if ($("#loggedUserRole").val() == "SuperAdmin") {
        $("#adminNavLItag").show();
    } else {
        $("#adminNavLItag").hide();
    }
    $.fn.dataTable.moment('DD/MM/YY');

    permitNavLinks();
    $("#logoutLItag").hide();
    
    var dataW = {};
    dataW.actionfunction = "userList";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    var userTbl = '';
    $.ajax({
        url: "CRUD.php",
        cache: false,
        type: "POST",
        data: { dbData: dataW },
        async: false,
        success: function(response) {
           // alert(JSON.stringify(response["userPermissionValue"]));
            if (response != 'error') {
                //js_zoneLocationsData = $.parseJSON(response);
                loadTableUserList(response,userTbl);
                $("div.addbutton").html('<button class="btn btn-secondary float-right ml-4 mr-4  addBtnClass"\
                              id="recordAddBtn" data-toggle="modal" \
                              data-target="#recordListModalID">Add</button>');
                $.each(response, function(key, value) {
                    if(key == 'userPermissionValue') {
                        if ($("#loggedUserRole").val() == "SuperAdmin") {
                            //dont do anything
                        } else {
                            //alert($("#recordAddBtn").attr("id"));
                           // alert(value);
                           afterAjaxCRUDPermission(parseFloat(value), $("#recordAddBtn"), $(".user_edit"), $(".fa-edit")); 
                        }
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call

     $(document).on('click', '#importFileBtn', function(e) {
        alert("Make sure it is .xlsx file and first sheet is named 'locations'");

     });

    $(document).on('click', '.user_edit, #recordAddBtn', function(e) {
        $('#recordListModalID').css("display", "block");

        $("input[name='form_employeeID']").val('');
        $("input[name='form_firstName']").val('');
        $("input[name='form_middleName']").val('');
        $("input[name='form_lastName']").val('');
        $("input[name='form_userID']").val('');
        $("input[name='form_email']").val('');
        //$("input[name='form_title']").val('');
        $("input[name='form_password']").val('');
        $("input[name='form_primaryContact']").val('');
        $("input[name='form_alternateContact']").val('');
        $("input[name='form_password']").val('');
        $("input[name='form_confirmpassword']").val('');

        $("#titleSelect").val("");
        $("#managerSelect").val("");
        $("#userTypeSelect").val("");
        $("#roleSelect").val("");
        $("#statusSelect").val("Yet2Login");

        $('#recordListModalID').removeData();
        userAddEditBtn = e.target.id;
        if (userAddEditBtn == "recordAddBtn") {
            $("#userModalTitle").html("Add User  (* fields are mandatory)");
            $("input[name='form_employeeID']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("input[name='form_userID']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("#modalClearBtn").show();
            $("#hideStatusSelect").hide();
            $("#passwordRow").hide();
        } else {
            $("#userModalTitle").html("Update User (employeeID, userID being unique, can not be edited)");
            $("#modalClearBtn").hide();
            $("#hideStatusSelect").show();
            $("#passwordRow").show();
            $("input[name='form_employeeID']").attr("readonly", true).css('background-color', '#DEDEDE');
            $("input[name='form_userID']").attr("readonly", true).css('background-color', '#DEDEDE');

            var rowid = $(this).parent().parent().attr('id');
            //alert(rowid);
            $('#recordListModalID').data('userRowID', rowid.slice(4));

            $("input[name='form_employeeID']").val($("#"+rowid+" td:nth-child("+empIDColIndex+")").text());
            $("input[name='form_firstName']").val($("#"+rowid+" td:nth-child("+firstNameColIndex+")").text());
            $("input[name='form_middleName']").val($("#"+rowid+" td:nth-child("+middleNameColIndex+")").text());
            $("input[name='form_lastName']").val($("#"+rowid+" td:nth-child("+lastNameColIndex+")").text());
            $("input[name='form_userID']").val($("#"+rowid+" td:nth-child("+userIDColIndex+")").text());
            $("input[name='form_email']").val($("#"+rowid+" td:nth-child("+emailColIndex+")").text());
            $("input[name='form_primaryContact']").val($("#"+rowid+" td:nth-child("+primaryColIndex+")").text());
            $("input[name='form_alternateContact']").val($("#"+rowid+" td:nth-child("+alternateColIndex+")").text());
            
            var currentTitleID = $("#"+rowid+" td:nth-child("+titleColIndex+")").attr("id");
            var currentManagerID = $("#"+rowid+" td:nth-child("+managerColIndex+")").attr("id");
            //alert(currentManagerID);
            var currentUserTypeID = $("#"+rowid+" td:nth-child("+userTypeColIndex+")").attr("id");
            var currentRoleID = $("#"+rowid+" td:nth-child("+roleColIndex+")").attr("id");
            var currentStatus = $("#"+rowid+" td:nth-child("+statusColIndex+")").text();
            var currentStatusID = $("#statusSelect option[value="+currentStatus+"]").attr("id");

            $('#recordListModalID').data('title_id', currentTitleID);
            $('#recordListModalID').data('manager_id', currentManagerID);
            $('#recordListModalID').data('usertype_id', currentUserTypeID);
            $('#recordListModalID').data('role_id', currentRoleID);
            $('#recordListModalID').data('status_id', currentStatusID);

            $("#titleSelect").val($("#"+currentTitleID).text()).change();
            $("#managerSelect").val($("#"+currentManagerID).text()).change();
            //alert($("#managerSelect option:selected").text());
            $("#userTypeSelect").val($("#"+currentUserTypeID).text()).change();
            $("#roleSelect").val($("#"+currentRoleID).text()).change();
            $("#statusSelect").val($("#"+currentStatusID).text()).change();
        } // END of if idClicked chk
    }); //END of onclick loc edit

    $('#form_confirmpassword').on('blur', function(){
        if($("#form_password").val() != $("#form_confirmpassword").val()) {
            $("#userEditFormSubmitBtn").prop("disabled", true);
            alert("passwords don't match");
        } else {
            $("#userEditFormSubmitBtn").prop("disabled", false);
        }
    });

    $("#userListModalForm").submit(function(event) {
        //alert($('#taskModalID').parent().parent().prop('tagName'));
        //console.log( $( this ).serializeArray() );
        //DataTableObject.destroy();
        event.preventDefault();
        //alert($('#recordListModalID').data('contact_id'));
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "insertORupdateUser";
        dataW.loggedUser = $('#loggedUser').val();
        if (userAddEditBtn != "recordAddBtn") { //locationEditBtn = true
            dataW.todo = "update";
            dataW.id = parseFloat($('#recordListModalID').data('userRowID'));
            dataW.title_id = parseFloat($('#recordListModalID').data('title_id').slice(6)); //ti_td_
            dataW.manager_id = parseFloat($('#recordListModalID').data('manager_id').slice(7)); //mid_td_
            dataW.usertype_id = parseFloat($('#recordListModalID').data('usertype_id').slice(6)); //ut_td_
            dataW.role_id = parseFloat($('#recordListModalID').data('role_id').slice(5)); //r_td_
        } else {
            dataW.todo = "insert";
            dataW.id =  '';
            dataW.title_id = parseFloat($('#titleSelect option:selected').attr('id').slice(7)); //ti_opt_
            dataW.manager_id = parseFloat($('#managerSelect option:selected').attr('id').slice(8)); //mid_opt_
            dataW.usertype_id = parseFloat($('#userTypeSelect option:selected').attr('id').slice(7)); //ut_opt_
            dataW.role_id = parseFloat($('#roleSelect option:selected').attr('id').slice(6)); //r_opt_
        }

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
            // v here is the modal fields
            //v['value'] is if name is zone, take the value of that field and store it in dataW
            if (v['name'] === 'form_employeeID') {
                dataW.employeeID = v['value'];
            } else if (v['name'] === 'form_firstName') {
                dataW.firstName = v['value'];
            } else if (v['name'] === 'form_middleName') {
                dataW.middleName = v['value'];
            } else if (v['name'] === 'form_lastName') {
                dataW.lastName = v['value'];
            } else if (v['name'] === 'form_userID') {
                dataW.userID = v['value'];
            } else if (v['name'] === 'form_email') {
                dataW.email = v['value'];
            } else if (v['name'] === 'form_title') {
                dataW.title_id = parseFloat($('#titleSelect option:selected').attr('id').slice(7));
            } else if (v['name'] === 'form_primaryContact') {
                dataW.primaryContact = v['value'];
            } else if (v['name'] === 'form_alternateContact') {
                dataW.alternateContact = v['value'];
            } else if (v['name'] === 'form_manager') {
                dataW.manager_id = parseFloat($('#managerSelect option:selected').attr('id').slice(8));
            } else if (v['name'] === 'form_usertype') {
                dataW.usertype_id = parseFloat($('#userTypeSelect option:selected').attr('id').slice(7));
            } else if (v['name'] === 'form_role') {
                dataW.role_id = parseFloat($('#roleSelect option:selected').attr('id').slice(6));
            } else if (v['name'] === 'form_status') {
                dataW.status_id =  parseFloat($('#statusSelect option:selected').attr('id').slice(2));
            } else if (v['name'] === 'form_password') {
                 if (dataW.todo === "insert") {
                    dataW.password = "x123";
                } else {
                    dataW.password = v['value'];
                }
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
                    alert("Looks like that userid and exists already!; use Edit.");
                } else {
                    if (dataW.todo === "insert") {
                        //var js_row = $.parseJSON(response);
                        //alert(JSON.stringify(js_row));
                        var js_row = response;
                        var thisnewRow = '';
                        var rowNode = DataTableObject
                        .row.add([js_row["users"][0]["employeeID"], js_row["users"][0]["firstName"], 
                                  js_row["users"][0]["middleName"], js_row["users"][0]["lastName"], 
                                  js_row["users"][0]["userID"], js_row["users"][0]["email"],js_row["users"][0]["title"],
                                  js_row["users"][0]["primaryContact"], js_row["users"][0]["alternateContact"], 
                                  js_row["users"][0]["Manager"], js_row["users"][0]["userType"], 
                                  js_row["users"][0]["role"], js_row["users"][0]["status"], editIcon])
                        .draw()
                        .node();
                        $(rowNode)
                            .css('color', 'red')
                            .animate({ color: 'black' })
                            .attr('id', "usr_"+js_row["users"][0]["id"]);
                        
                        /*$('#titleSelect').append($("<option/>", {
                            id: "ti_opt_"+js_row["users"][0]["title_id"],
                            value: js_row["users"][0]["title"],
                            text: js_row["users"][0]["title"]
                        }));

                        $('#managerSelect').append($("<option/>", {
                            id: "mid_opt_"+js_row["users"][0]["manager_id"],
                            value: js_row["users"][0]["Manager"],
                            text: js_row["users"][0]["Manager"]
                        }));
                        $('#userTypeSelect').append($("<option/>", {
                            id: "ut_opt_"+js_row["users"][0]["usertype_id"],
                            value: js_row["users"][0]["userType"],
                            text: js_row["users"][0]["userType"]
                        }));
                        $('#roleSelect').append($("<option/>", {
                            id: "r_opt_"+js_row["users"][0]["role_id"],
                            value: js_row["users"][0]["role"],
                            text: js_row["users"][0]["role"]
                        }));*/

                        var rowid = $(rowNode).attr("id");
                        $("#"+rowid+" td:nth-child("+titleColIndex+")").attr("id", "ti_td_"+js_row["users"][0]["title_id"]);
                        $("#"+rowid+" td:nth-child("+managerColIndex+")").attr("id", "mid_td_"+js_row["users"][0]["manager_id"]);
                        $("#"+rowid+" td:nth-child("+userTypeColIndex+")").attr("id", "ut_td_"+js_row["users"][0]["usertype_id"]);
                        $("#"+rowid+" td:nth-child("+roleColIndex+")").attr("id", "r_td_"+js_row["users"][0]["role_id"]);
                    
                        $('#'+rowid)[0].scrollIntoView();
                    } else {
                        //var js_row = $.parseJSON(response);
                        //alert(JSON.stringify(js_row));
                        //users is the js object, with one array element so index 0 and that array is of js objects again
                        var js_row = response;
                        var rowpk=js_row["users"][0]["id"];
                        //when the table body rows are loaded with loadTable function, each tbody > tr is set with id attribute
                        // like l_+dB id; so each row can be accessed with concatenated l_ + rowpk
                        $('#usr_'+rowpk+' td:nth-child('+empIDColIndex+')').html(js_row["users"][0]["employeeID"]);
                        $('#usr_'+rowpk+' td:nth-child('+firstNameColIndex+')').html(js_row["users"][0]["firstName"]);
                        $('#usr_'+rowpk+' td:nth-child('+middleNameColIndex+')').html(js_row["users"][0]["middleName"]);
                        $('#usr_'+rowpk+' td:nth-child('+lastNameColIndex+')').html(js_row["users"][0]["lastName"]);
                        $('#usr_'+rowpk+' td:nth-child('+userIDColIndex+')').html(js_row["users"][0]["userID"]);
                        $('#usr_'+rowpk+' td:nth-child('+emailColIndex+')').html(js_row["users"][0]["email"]);
                        $('#usr_'+rowpk+' td:nth-child('+titleColIndex+')').html(js_row["users"][0]["title"]);
                        $('#usr_'+rowpk+' td:nth-child('+primaryColIndex+')').html(js_row["users"][0]["primaryContact"]);
                        $('#usr_'+rowpk+' td:nth-child('+alternateColIndex+')').html(js_row["users"][0]["alternateContact"]);
                        $('#usr_'+rowpk+' td:nth-child('+managerColIndex+')').html(js_row["users"][0]["Manager"]);
                        $('#usr_'+rowpk+' td:nth-child('+userTypeColIndex+')').html(js_row["users"][0]["userType"]);
                        $('#usr_'+rowpk+' td:nth-child('+roleColIndex+')').html(js_row["users"][0]["role"]);
                        $('#usr_'+rowpk+' td:nth-child('+statusColIndex+')').html(js_row["users"][0]["status"]);

                        $('#usr_'+rowpk)[0].scrollIntoView();
                        $('#usr_'+rowpk).css('color', 'red');
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
    }); //END of userListModalForm Submit

    function loadTableUserList(parsedObj,tableVar) {
        $.each(parsedObj, function(key, value) {
            if (key == 'users') {
                $.each(value, function(x, y) {
                    tableVar += '<tr id="usr_' + y['id'] + '"> \
                                    <td>' + y['employeeID'] + '</td> \
                                    <td>' + y['firstName'] + '</td> \
                                    <td>' + y['middleName'] + '</td> \
                                    <td>' + y['lastName'] + '</td> \
                                    <td>' + y['userID'] + '</td> \
                                    <td>' + y['email'] + '</td> \
                                    <td id="ti_td_' + y['title_id'] + '">' + y['title'] + '</td> \
                                    <td>' + y['primaryContact'] + '</td> \
                                    <td>' + y['alternateContact'] + '</td> \
                                    <td id="mid_td_' + y['manager_id'] + '">' + y['Manager'] + '</td> \
                                    <td id="ut_td_' + y['usertype_id'] + '">' + y['userType'] + '</td> \
                                    <td id="r_td_' + y['role_id'] + '">' + y['role'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + editIcon + '</td> \
                                  </tr>';
                    });
                $('#userListTbl > tbody:last').append(tableVar);
                DataTableObject = $('#userListTbl').DataTable({
                    "scrollY":        "900px",
                    "scrollCollapse": true,
                    "paging":         false,
                    //https://datatables.net/examples/advanced_init/dom_toolbar.html
                    "dom": '<"addbutton">frtip'
                    //"dom": '<"caption">frtip'
                });
            } // END if key=users
            else if (key == 'titles') {
                $.each(value, function(x,y){
                    $('#titleSelect').append($("<option/>", {
                        id: "ti_opt_"+y['id'],
                        value: y['title'],
                        text: y['title']
                    }));
                });
            }
            else if (key == 'managers') {
                $.each(value, function(x,y){
                    $('#managerSelect').append($("<option/>", {
                        id: "mid_opt_"+y['id'],
                        value: y['Manager'],
                        text: y['Manager']
                    }));
                });
            } else if (key == 'usertypes') {
                $.each(value, function(x,y){
                    $('#userTypeSelect').append($("<option/>", {
                        id: "ut_opt_"+y['id'],
                        value: y['userType'],
                        text: y['userType']
                    }));
                });
            } else if (key == 'roles') {
                $.each(value, function(x,y){
                    $('#roleSelect').append($("<option/>", {
                        id: "r_opt_"+y['id'],
                        value: y['role'],
                        text: y['role']
                    }));
                });
            }
        }); // end of .each
    } //END of function loadTableUserList
});