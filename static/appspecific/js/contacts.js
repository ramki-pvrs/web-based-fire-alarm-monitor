var contactAddEditBtn = '';
var contactSubmitCancelBtn = '';
var editIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="contact_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var DataTableObject = null;
var js_contactData = {};

//table column index
var firstNameColIndex = 1;
var middleNameColIndex = 2;
var lastNameColIndex = 3;
var emailColIndex = 4;
var genderColIndex = 5;
var primaryColIndex = 6;
var alternateColIndex = 7;
var statusColIndex = 8;
var zoneColIndex = 9;
var locationIDColIndex = 10;
var commentColIndex = 11;
var editColIndex = 12;

$(document).ready(function() {
    window.name = 'contactlist';
    if ($("#loggedUserRole").val() == "SuperAdmin") {
        $("#adminNavLItag").show();
    } else {
        $("#adminNavLItag").hide();
    }
    $.fn.dataTable.moment('DD/MM/YY');

    permitNavLinks();
    $("#logoutLItag").hide();
    
    var dataW = {};
    dataW.actionfunction = "contactList";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    //alert(JSON.stringify(dataW));
    var contactTbl = '';
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
                loadTableContactList(response,contactTbl);
                $("div.addbutton").html('<button class="btn btn-secondary float-right ml-4 mr-4  addBtnClass"\
                              id="recordAddBtn" data-toggle="modal" \
                              data-target="#recordListModalID">Add</button>');
                $.each(response, function(key, value) {
                    if(key == 'userPermissionValue') {
                        if ($("#loggedUserRole").val() == "SuperAdmin") {
                            //dont do anything
                        } else {
                           afterAjaxCRUDPermission(parseFloat(value), $("#recordAddBtn"), $(".contact_edit"), $(".fa-edit")); 
                        }
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call

     $(document).on('click', '#importFileBtn', function(e) {
        alert("Make sure it is .xlsx file and first sheet is named 'locations'");
     });

    $(document).on('click', '.contact_edit, #recordAddBtn', function(e) {
        $('#recordListModalID').css("display", "block");

      
        $("input[name='form_firstName']").val('');
        $("input[name='form_middleName']").val('');
        $("input[name='form_lastName']").val('');
        $("input[name='form_email']").val('');
        $("input[name='form_primaryContact']").val('');
        $("input[name='form_alternateContact']").val('');
        $("textarea[name='form_comment']").val('');

        $("#genderSelect").val("");
        $("#statusSelect").val("Active");

        $('#recordListModalID').removeData();
        contactAddEditBtn = e.target.id;
        if (contactAddEditBtn == "recordAddBtn") {
            $("#contactModalTitle").html("Add Contact  (* fields are mandatory)");
            $("#modalClearBtn").show();
            $("#hideStatusSelect").hide();
        } else {
            $("#contactModalTitle").html("Update Contact (make sure data is correct before submit!)");
            $("#modalClearBtn").hide();
            $("#hideStatusSelect").show();

            var rowid = $(this).parent().parent().attr('id');
            //alert(rowid);
            $('#recordListModalID').data('contactRowID', rowid.slice(4));

            
            $("input[name='form_firstName']").val($("#"+rowid+" td:nth-child("+firstNameColIndex+")").text());
            $("input[name='form_middleName']").val($("#"+rowid+" td:nth-child("+middleNameColIndex+")").text());
            $("input[name='form_lastName']").val($("#"+rowid+" td:nth-child("+lastNameColIndex+")").text());
            $("input[name='form_email']").val($("#"+rowid+" td:nth-child("+emailColIndex+")").text());
            $("input[name='form_primaryContact']").val($("#"+rowid+" td:nth-child("+primaryColIndex+")").text());
            $("input[name='form_alternateContact']").val($("#"+rowid+" td:nth-child("+alternateColIndex+")").text());
            $("textarea[name='form_comment']").val($("#"+rowid+" td:nth-child("+commentColIndex+")").text());
            //alert($("#"+rowid+" td:nth-child("+genderColIndex+")").text());
            
            $("#genderSelect").val($("#"+rowid+" td:nth-child("+genderColIndex+")").text()).change();

            var currentStatus = $("#"+rowid+" td:nth-child("+statusColIndex+")").text();
            var currentStatusID = $("#statusSelect option[value="+currentStatus+"]").attr("id");
            $('#recordListModalID').data('status_id', currentStatusID);
            $("#statusSelect").val($("#"+currentStatusID).text()).change();
        } // END of if idClicked chk
    }); //END of onclick contact edit

    $("#contactListModalForm").submit(function(event) {
        event.preventDefault();
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "insertORupdateContact";
        dataW.loggedUser = $('#loggedUser').val();
        dataW.loggedUserRole = $('#loggedUserRole').val();
        if (contactAddEditBtn != "recordAddBtn") { //locationEditBtn = true
            dataW.todo = "update";
            dataW.id = parseFloat($('#recordListModalID').data('contactRowID'));
        } else {
            dataW.todo = "insert";
            dataW.id =  '';
        }

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
            // v here is the modal fields
            //v['value'] is if name is zone, take the value of that field and store it in dataW
            if (v['name'] === 'form_firstName') {
                dataW.firstName = v['value'];
            } else if (v['name'] === 'form_middleName') {
                dataW.middleName = v['value'];
            } else if (v['name'] === 'form_lastName') {
                dataW.lastName = v['value'];
            } else if (v['name'] === 'form_email') {
                dataW.email = v['value'];
            } else if (v['name'] === 'form_gender') {
                dataW.gender = $('#genderSelect option:selected').val();
            } else if (v['name'] === 'form_primaryContact') {
                dataW.primaryContact = v['value'];
            } else if (v['name'] === 'form_alternateContact') {
                dataW.alternateContact = v['value'];
            } else if (v['name'] === 'form_status') {
                dataW.status_id =  parseFloat($('#statusSelect option:selected').attr('id').slice(2));
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
                if (typeof response !== 'object' && response.indexOf("Integrity constraint") > -1) {
                    alert("Looks like that email exists already!; use Edit.");
                } else {
                    if (dataW.todo === "insert") {
                        //var js_row = $.parseJSON(response);
                        //alert(JSON.stringify(js_row));
                        var js_row = response;
                        var thisnewRow = '';
                        var rowNode = DataTableObject
                        .row.add([js_row["contacts"][0]["firstName"], js_row["contacts"][0]["middleName"], js_row["contacts"][0]["lastName"], 
                                  js_row["contacts"][0]["email"],js_row["contacts"][0]["gender"],
                                  js_row["contacts"][0]["primaryContact"], js_row["contacts"][0]["alternateContact"], 
                                  js_row["contacts"][0]["status"],
                                  js_row["contacts"][0]["zone"],js_row["contacts"][0]["locationID"], js_row["contacts"][0]["comment"], editIcon])
                        .draw()
                        .node();
                        $(rowNode)
                            .css('color', 'red')
                            .animate({ color: 'black' })
                            .attr('id', "con_"+js_row["contacts"][0]["rid"]);

                        var rowid = $(rowNode).attr("id");
                        $('#'+rowid)[0].scrollIntoView();
                    } else {
                        //contacts is the js object, with one array element so index 0 and that array is of js objects again
                        var js_row = response;
                        //alert(JSON.stringify(response));
                        var rowpk=js_row["contacts"][0]["rid"];
                        //alert(rowpk);
                        //when the table body rows are loaded with loadTable function, each tbody > tr is set with id attribute
                        // like l_+dB id; so each row can be accessed with concatenated l_ + rowpk
                        $('#con_'+rowpk+' td:nth-child('+firstNameColIndex+')').html(js_row["contacts"][0]["firstName"]);
                        $('#con_'+rowpk+' td:nth-child('+middleNameColIndex+')').html(js_row["contacts"][0]["middleName"]);
                        $('#con_'+rowpk+' td:nth-child('+lastNameColIndex+')').html(js_row["contacts"][0]["lastName"]);
                        $('#con_'+rowpk+' td:nth-child('+emailColIndex+')').html(js_row["contacts"][0]["email"]);
                        $('#con_'+rowpk+' td:nth-child('+genderColIndex+')').html(js_row["contacts"][0]["gender"]);
                        $('#con_'+rowpk+' td:nth-child('+primaryColIndex+')').html(js_row["contacts"][0]["primaryContact"]);
                        $('#con_'+rowpk+' td:nth-child('+alternateColIndex+')').html(js_row["contacts"][0]["alternateContact"]);
                        $('#con_'+rowpk+' td:nth-child('+statusColIndex+')').html(js_row["contacts"][0]["status"]);
                        $('#con_'+rowpk+' td:nth-child('+zoneColIndex+')').html(js_row["contacts"][0]["zone"]);
                        $('#con_'+rowpk+' td:nth-child('+locationIDColIndex+')').html(js_row["contacts"][0]["locationID"]);
                        $('#con_'+rowpk+' td:nth-child('+commentColIndex+')').html(js_row["contacts"][0]["comment"]);

                        $('#con_'+rowpk)[0].scrollIntoView();
                        $('#con_'+rowpk).css('color', 'red');
                    }
                }
            } //END of success
        }); //END of CRUD ajax call
         $('#recordListModalID').css("display", "none");
         $("body").removeClass("modal-open");
         $('.modal-backdrop').remove();
         $('#recordListModalID').removeClass("show");
    }); //END of contactListModalForm Submit

    function loadTableContactList(parsedObj,tableVar) {
        $.each(parsedObj, function(key, value) {
            if (key == 'contacts') {
                $.each(value, function(x, y) {
                    tableVar += '<tr id="con_' + y['rid'] + '"> \
                                    <td>' + y['firstName'] + '</td> \
                                    <td>' + y['middleName'] + '</td> \
                                    <td>' + y['lastName'] + '</td> \
                                    <td>' + y['email'] + '</td> \
                                    <td>' + y['gender'] + '</td> \
                                    <td>' + y['primaryContact'] + '</td> \
                                    <td>' + y['alternateContact'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + y['zone'] + '</td> \
                                    <td>' + y['locationID'] + '</td> \
                                    <td>' + y['comment'] + '</td> \
                                    <td>' + editIcon + '</td> \
                                  </tr>';
                    });
                $('#contactListTbl > tbody:last').append(tableVar);
                DataTableObject = $('#contactListTbl').DataTable({
                    "scrollY":        "900px",
                    "scrollCollapse": true,
                    "paging":         false,
                    //https://datatables.net/examples/advanced_init/dom_toolbar.html
                    "dom": '<"addbutton">frtip'
                    //"dom": '<"caption">frtip'
                });
            } // END if key=contacts
        }); // end of .each
    } //END of function loadTableContactList
    
    //$("div.caption").html('<p>Testing caption</p>');
});