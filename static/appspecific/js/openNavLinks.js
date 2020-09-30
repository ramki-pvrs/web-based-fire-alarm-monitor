function goHome() {
    javascript: void(window.open("alarms_page.php", target = "alarmlist"));
}

function openUpdateList() {
    javascript: void(window.open("updates_page.php", target = "updatelist"));
}

function openContactList() {
    javascript: void(window.open("contacts_page.php", target = "contactlist"));
}

function openZoneList() {
    javascript: void(window.open("zones_page.php", target = "zonelist"));
}

function openLocationList() {
    javascript: void(window.open("locations_page.php", target = "locationlist"));
}

function openTaskList() {
    javascript: void(window.open("tasks_page.php", target = "tasklist"));
}

function openUserList() {
    javascript: void(window.open("users_page.php", target = "userlist"));
}

function openAdminPage() {
    javascript: void(window.open("admin_page.php", target = "adminpage"));
}


function afterAjaxCRUDPermission(value, addBtnID, editBtnsClass, faeditClass, ) {
	if(value === 15 || value === 14) {
        //alert("ramki 1");
        // CRUD 1111 OR 1110
       //all permissions for this logged in user
    } else if(value === 6) {
        //alert("ramki 2");
        //Read and Update permission CRUD 0110
        //alert(addBtnID.attr("id"));
        addBtnID.prop("disabled", true).addClass("fa-disabled").attr("title", "restricted access");
    } else if(value === 4) {
        //alert("ramki 3");
        //only Read Permission CRUD 0100
        editBtnsClass.prop("disabled", true).addClass("fa-disabled").attr("title", "restricted access");
        faeditClass.addClass("fa-disabled").attr("title", "restricted access");
        addBtnID.prop("disabled", true).addClass("fa-disabled").attr("title", "restricted access");
    } else if(value === 0) {
        //alert("ramki 4");
        //CRUD 0000
        //not even read permission; should work on this
        editBtnsClass.prop("disabled", true).addClass("fa-disabled").attr("title", "restricted access");
        faeditClass.addClass("fa-disabled").attr("title", "restricted access");
        addBtnID.prop("disabled", true).addClass("fa-disabled").attr("title", "restricted access");
    }
}