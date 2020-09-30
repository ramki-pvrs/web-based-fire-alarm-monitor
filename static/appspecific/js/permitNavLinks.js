function permitNavLinks() {
    var dataW = {};
    dataW.actionfunction = "getUserPermissions";
    dataW.loggedUser = $('#loggedUser').val();
    dataW.loggedUserRole = $('#loggedUserRole').val();
    $.ajax({
        url: "CRUD.php",
        cache: false,
        type: "POST",
        data: { dbData: dataW },
        async: false,
        success: function(response) {
            //alert(JSON.stringify(response));
            if (response != 'error') {
                if ($("#loggedUserRole").val() == "SuperAdmin") {
                    //if SuperAdmin all links are visible; so don't do anything here
                } else {
                    if (parseFloat(response["userPermissionValue"]["Updates"]) === 0) {
                        //alert("ramki2");
                        $("#updateNavLItag").addClass("hideNavLinks");
                    }

                    if (parseFloat(response["userPermissionValue"]["Zones"]) === 0) {
                        //alert("ramki2");
                        //$("#zoneNavLItag").hide();
                        $("#zoneNavLItag").addClass("hideNavLinks");
                    }
                    if (parseFloat(response["userPermissionValue"]["Locations"]) === 0) {
                        //alert("ramki2");
                        $("#locationNavLItag").addClass("hideNavLinks");
                    }

                    if (parseFloat(response["userPermissionValue"]["Contacts"]) === 0) {
                        //alert("ramki2");
                        $("#contactNavLItag").addClass("hideNavLinks");
                    }

                    if (parseFloat(response["userPermissionValue"]["ToDo"]) === 0) {
                        //alert("ramki2");
                        $("#taskNavLItag").addClass("hideNavLinks");
                    }

                    if (parseFloat(response["userPermissionValue"]["Users"]) === 0) {
                        //alert("ramki2");
                        $("#userNavLItag").addClass("hideNavLinks");
                    }

                    //if(parseFloat(response["userPermissionValue"]["Admin"]) === 0) {
                    //alert("ramki2");
                    //$("#adminNavLItag").hide();
                    //}
                }

            } //End of if response!=error
        } //END of success
    }) //END of CRUD ajax call
}