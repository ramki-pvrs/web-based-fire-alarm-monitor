var zoneAddEditBtn = '';
var zoneSubmitCancelBtn = '';
var editIcon = '<a href="#" data-toggle="modal" data-target="#recordListModalID" class="zone_edit"><i title="Edit" class="fa fa-edit" style="font-size:16px;color:Blue"></i></a>';
var DataTableObject = null;

$(document).ready(function() {
    window.name = 'zonelist';
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
    var zoneTbl = '';

    //alert(JSON.stringify(dataW));
    $.ajax({
        url: "CRUD.php",
        cache: false,
        type: "POST",
        data: { dbData: dataW },
        async: false,
        success: function(response) {
            if (response != 'error') {
                //js_zoneLocationsData = $.parseJSON(response);
                loadTableZoneList(response,zoneTbl);
                //addbutton is by Datatable custom 
                $("div.addbutton").html('<button class="btn btn-secondary float-right ml-4 mr-4  addBtnClass"\
                              id="recordAddBtn" data-toggle="modal" \
                              data-target="#recordListModalID">Add</button>');
                $.each(response, function(key, value) {
                    if(key == 'userPermissionValue') {
                        if ($("#loggedUserRole").val() == "SuperAdmin") {
                            //dont do anything
                        } else {
                           afterAjaxCRUDPermission(parseFloat(value), $("#recordAddBtn"), $(".zone_edit"), $(".fa-edit")); 
                        }
                    }
                }); //end of each for permissionBit
            } //End of if response!=error
        } //END of success
    }); //END of CRUD ajax call

    $(document).on('click', '.zone_edit, #recordAddBtn', function(e) {
        $('#recordListModalID').css("display", "block");

        $("input[name='form_zone']").val('');
        $("textarea[name='form_comment']").val('');
        $("#statusSelect").val('Active');
        $('#recordListModalID').removeData();
        zoneAddEditBtn = e.target.id;
        if (zoneAddEditBtn == "recordAddBtn") {
            $("#zoneModalTitle").html("Add Zone");
            $("#modalClearBtn").show();
            $("#hideStatusSelect").hide();
        } else {
            $("#zoneModalTitle").html("Update Zone");
            $("#modalClearBtn").hide();
            $("#hideStatusSelect").show();

            var rowid = $(this).parent().parent().attr('id');
            $('#recordListModalID').data('zoneRowID', rowid.slice(3));
            //alert($("#"+rowid+" td:nth-child(2)").text());
            $("input[name='form_zone']").val($("#"+rowid+" td:nth-child(1)").text());
            $("#statusSelect").val($("#"+rowid+" td:nth-child(2)").text());
            $("textarea[name='form_comment']").val($("#"+rowid+" td:nth-child(3)").text());
        } // END of if idClicked chk
    }); //END of onclick loc edit


    
    $("#zoneListModalForm").submit(function(event) {
        event.preventDefault();
        var dataW = {};
        var someArr = [];
        dataW.actionfunction = "insertORupdateZone";
        dataW.loggedUser = $('#loggedUser').val();
        if (zoneAddEditBtn != "recordAddBtn") { //locationEditBtn = true
            dataW.todo = "update";
            //data property of the modal is set when a .zone_edit event is fired and used here;
            dataW.id = $('#recordListModalID').data('zoneRowID');
        } else {
            dataW.todo = "insert";
            dataW.id =  '';
        }

        //$this is form here
        someArr = $(this).serializeArray();
        $.each(someArr, function(k, v) {
            if (v['name'] === 'form_zone') {
                dataW.zone = v['value'];
            } else if (v['name'] === 'form_comment') {
                dataW.comment = v['value'];
            } else if (v['name'] === 'form_status') {
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
                if (typeof response !== 'object' && response.indexOf("Integrity constraint") > -1) {
                    alert("Looks like that zone and location combo exists already!; use Edit.");
                } else {
                    if (dataW.todo === "insert") {
                        var js_row = response;
                        var thisnewRow = '';
                        var rowNode = DataTableObject
                        .row.add([js_row["zones"][0]["zone"], js_row["zones"][0]["status"], 
                                  js_row["zones"][0]["comment"], editIcon])
                        .draw()
                        .node();
                        $(rowNode)
                            .css('color', 'red')
                            .animate({ color: 'black' })
                            .attr('id', "zn_"+js_row["zones"][0]["id"]);

                       var rowid = $(rowNode).attr("id");
                        $('#'+rowid)[0].scrollIntoView();
                    } else {
                        var js_row = response;
                        var rowpk=js_row["zones"][0]["id"];
                        //when the table body rows are loaded with loadTable function, each tbody > tr is set with id attribute
                        // like l_+dB id; so each row can be accessed with concatenated l_ + rowpk
                        $('#zn_'+rowpk+' td:nth-child(1)').html(js_row["zones"][0]["zone"]);
                        $('#zn_'+rowpk+' td:nth-child(2)').html(js_row["zones"][0]["status"]);
                        $('#zn_'+rowpk+' td:nth-child(3)').html(js_row["zones"][0]["comment"]);
                                                    
                        $('#zn_'+rowpk)[0].scrollIntoView();
                        $('#zn_'+rowpk).css('color', 'red');
                    }
                }
            } //END of success

        }); //END of CRUD ajax call
         $('#recordListModalID').css("display", "none");
         $("body").removeClass("modal-open");
         $('.modal-backdrop').remove();
         $('#recordListModalID').removeClass("show");
    }); //END of zoneListModalForm Submit

    function loadTableZoneList(parsedObj,tableVar) {
        $.each(parsedObj, function(key, value) {
            if (key == 'zones') {
                $.each(value, function(x, y) {
                    tableVar += '<tr id="zn_' + y['id'] + '"> \
                                    <td>' + y['zone'] + '</td> \
                                    <td>' + y['status'] + '</td> \
                                    <td>' + y['comment'] + '</td> \
                                    <td>' + editIcon + '</td> \
                                  </tr>';
                    });
                $('#zoneListTbl > tbody:last').append(tableVar);
                DataTableObject = $('#zoneListTbl').DataTable({
                    "scrollY":        "900px",
                    "scrollCollapse": true,
                    "paging":         false,
                    //https://datatables.net/examples/advanced_init/dom_toolbar.html
                    "dom": '<"addbutton">frtip'
                    //"dom": '<"caption">frtip'
                });
            } // END if key=zones
        }); // end of .each
    }
});