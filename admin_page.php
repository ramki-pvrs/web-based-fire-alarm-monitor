<?php
session_start();
include_once __DIR__ .'/static/public/CSRF-Protector-PHP/libs/csrf/csrfprotector.php';
//Initialise CSRFGuard library
csrfProtector::init();
include dirname(__FILE__).'/loggedUserData.php';
if(!isset($logged_user)){
    header('location: ../index.php'); // Redirecting To Home Page
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ADMIN</title>
    <?php include ("head.php"); ?>
    <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
    <script type="text/javascript" src="static/appspecific/js/alarms.js"></script>
    <!-- http://davidstutz.de/bootstrap-multiselect/#configuration-options-selectAllText -->
    <link rel="stylesheet" href="static/public/css/bootstrap-multiselect.css">
    <script type="text/javascript" src="static/public/js/bootstrap-multiselect.js"></script>
    <!-- <link rel="stylesheet" href="static/public/css/jquery.multiselect.css">
    <script type="text/javascript" src="static/public/js/jquery.multiselect.js"></script> -->
    <style type="text/css">
        #adminNavItag {
            aria-hidden:  true;
            font-size: 32px;
            color:  blue;
        }
    </style>

</head>
<body>
	  <?php include ("hiddenInputs.php"); ?>
    <!-- Top container -->
    <?php include ("navLinks.php"); ?>
    
    

    <div class="container-fluid p-5">
      <div class="card bg-light col col-sm-6">
        <h5 class="card-header">TIMER UPDATES</h5>
      <div class="card-body">
        <!-- <h5 class="row pb-5">Update Timers for Alarm pop-up, Fire Panel Health check frequency</h5> -->
        <div class="row pb-5">
        	<div class="card" style="background-color: #D4E6F1;">
  			    <div class="card-body">
		    	      Alarm Pop-up Timer in Seconds (minimum 60):
                <input id="popupTimer" type="number" value=60 style="width:50px;">
                <button id="popupTimerBtn" class="btn btn-primary btn-sm mr-2">Update</button>
  			    </div>
			    </div>
        </div>
        <div class="row">
        	<div class="card" style="background-color: #D4E6F1;">
  			    <div class="card-body">
		    	      HealthCheck Frequency in Seconds (minimum 6):
                <input id="healthTimer" type="number" value=6 style="width:50px;">
                <button id="healthTimerBtn" class="btn btn-primary btn-sm mr-2">Update</button>
  			    </div>
			    </div>
        </div>
      </div>
    </div>
    </div>

    <div class="container-fluid p-5">
      <div class="card border-primary bg-light col col-sm-6">
        <h5 class="card-header">ROLE-PERMISSION</h5>
        <div class="card-body">
          <!-- <h5 class="card-title">Role-Table Access Permissions</h5> -->
            <form id="rolepermissionform" class="form-horizontal">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="form_role" class="pb-0 mb-0">Select Role</label>
                            <select id="roleSelect" name="form_role" class="form-control" required="required" data-error="Select Role!">
                               <option id="r_opt_0" value="">Select</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="form_entity" class="pb-0 mb-0">Select Table(s)</label>
                            <select id="entitySelect" name="form_entity" class="form-control" required="required" data-error="Select Table!" multiple>
                               <!-- <option id="e_opt_0" value="">Select</option> -->
                               <!-- <option id="e_opt_-1" value="SelectAll">Select All</option> -->
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check-inline">
                          <label class="form-check-label mr-4" for="readChkBox">Read:</label>
                          <input type="checkbox" class="form-check-input" id="readChkBox" name="form_Read" value = "R" style="width:3vw; height:3vh;">
                        </div>
                        <div class="form-check-inline">
                          <label class="form-check-label mr-2" for="updateChkBox">Update:</label>
                          <input type="checkbox" class="form-check-input" id="updateChkBox" name="form_Update" value = "U" style="width:3vw; height:3vh;">
                        </div>
                        <div class="form-check-inline">
                          <label class="form-check-label mr-3" for="createChkBox">Create:</label>
                          <input type="checkbox" class="form-check-input" id="createChkBox" name="form_Create" value = "C" style="width:3vw; height:3vh;">
                        </div>
                    </div>
                </div>
                <div class="btn-group">
                     <button id="addPermissionBtn" class="btn btn-primary btn-sm mr-2" type="submit" name="updateBtn" value="Update">Add Permission</button>
                     <button id="deletePermissionBtn" class="btn btn-warning btn-sm mr-2" type="submit" name="deleteBtn" value="Delete">Remove Permission</button>
                </div>
            </form>
        </div>
      </div>
    </div>
	
	<?php include ("footer.php"); ?>
	<script>
        function resetAdminUserForm() {
            document.getElementById("add_edit_AdminUserForm").reset();
            $("#editAdminUserInput").val('');
            $("input[name='adminUserID']").attr("readonly", false).css('background-color', '#FFFFFF');
        }

        //setInterval("my_function();", 50000000000000000000000000000000000000000);

        function my_function() {
            window.location = location.href;
        }
  </script>
  <script type="text/javascript" src="static/appspecific/js/openNavLinks.js"></script>
  <script type="text/javascript" src="static/appspecific/js/admin.js"></script>
</body>
</html>