<?php
session_start();
include_once __DIR__ .'/static/public/CSRF-Protector-PHP/libs/csrf/csrfprotector.php';
//Initialise CSRFGuard library
csrfProtector::init();
include dirname(__FILE__).'/loggedUserData.php';
if(!isset($logged_user)){
    header('location: ../index.php'); // Redirecting To Home Page

    //grid
    //row
    //col-4, col-12..
    //navbar
    //
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>USERS</title>
    <!-- https://codepen.io/sazzad/pen/antDJ -->
    <?php include ("head.php"); ?>
    <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
    <script type="text/javascript" src="static/appspecific/js/users.js"></script>
    <style type="text/css">
        .fa-disabled {
              opacity: 0.6;
              cursor: not-allowed;
        }
    </style>
    <style type="text/css">
        #userNavItag {
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

    <div id="modalContainer" class="container">
        <div class="row mb-4">
                <!-- The Modal -->
                <div class="modal hide fade" id="recordListModalID">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h8 id="userModalTitle" class="modal-title">Add User</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="userListModalForm" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_employeeID" class="pb-0 mb-0">Employee ID *</label>
                                                <input type="text" name="form_employeeID" class="form-control" placeholder="Employee ID *" required="required" >
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_firstName" class="pb-0 mb-0">First Name *</label>
                                                <input type="text" name="form_firstName" class="form-control" placeholder="First Name" required="required" >
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_middleName" class="pb-0 mb-0">Middle Name</label>
                                                <input type="text" name="form_middleName" class="form-control" placeholder="Middle Name">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_lastName"  class="pb-0 mb-0">Last Name</label>
                                                <input type="text" name="form_lastName" class="form-control" placeholder="Last Name">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_userID" class="pb-0 mb-0">User ID (login name) *</label>
                                                <input type="text" name="form_userID" class="form-control" placeholder="User ID" required="required" >
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_email" class="pb-2 mb-0">User Email *</label>
                                                <input id="form_email" type="email" name="form_email" class="form-control" placeholder="Enter employee E-Mail">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_title" class="pb-2 mb-0">Select User Title *</label>
                                                <select id="titleSelect" name="form_title" class="form-control" required="required" data-error="Select Title">
                                                   <option id="ti_opt_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_primaryContact" class="pb-0 mb-0">User Primary Contact #</label>
                                                <input id="form_primaryContact" type="text" name="form_primaryContact" class="form-control" placeholder="Enter user primary contact">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_alternateContact" class="pb-0 mb-0">User Alternate Contact #</label>
                                                <input type="text" name="form_alternateContact" class="form-control" placeholder="Enter user alternate contact">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_manager" class="pb-2 mb-0">Select Manager *</label>
                                                <select id="managerSelect" name="form_manager" class="form-control" required="required" data-error="Select Manager">
                                                   <option id="mid_opt_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_usertype" class="pb-0 mb-0">Select User Type *</label>
                                                <select id="userTypeSelect" name="form_usertype" class="form-control" required="required" data-error="Select User Type">
                                                   <option id="ut_opt_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_role" class="pb-0 mb-0">Select Role *</label>
                                                <select id="roleSelect" name="form_role" class="form-control" required="required" data-error="Select Role">
                                                   <option id="r_opt_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div id="hideStatusSelect" class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_status" class="pb-0 mb-0">Select Status</label>
                                                <select id="statusSelect" name="form_status" class="form-control" required="required" data-error="Select Status">
                                                    <option id="s_-1" value="Yet2Login" selected>Yet2Login</option>
                                                    <option id="s_1" value="Active">Active</option>
                                                    <option id="s_0" value="Inactive">Inactive</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="passwordRow" class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_password" class="pb-0 mb-0">Change Password:</label>
                                                <input id="form_password" type="password" name="form_password" class="form-control" placeholder="Enter new password">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_confirmpassword" class="pb-0 mb-0">Confirm Password:</label>
                                                <input id="form_confirmpassword" type="password" name="form_confirmpassword" class="form-control" placeholder="Confirm password">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <!-- <input id="userEditFormSubmitBtn" class="btn btn-success btn-sm mr-2" type="submit" value="Submit"> -->
                                        <button id="userEditFormSubmitBtn" class="btn btn-success btn-sm mr-2" type="submit">Submit</button>
                                        <button id="modalClearBtn" type="reset" class="btn btn-sm" value="clear" onclick="resetUserForm()">Clear</button>
                                    </div>
                                </form>
                            </div>
                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button id="modalCloseBtn" type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <div class="container-fluid p-4">
        <div class="row">
            <div class="table-responsive">
                <table id="userListTbl" class="table table-bordered table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>User ID</th>
                            <th>E-Mail</th>
                            <th>Title</th>
                            <th>Primary Contact</th>
                            <th>Alternate Contact</th>
                            <th>Manager</th>
                            <th>User Type</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
        
    
    <?php include ("footer.php"); ?>

    
    <script>
        function resetUserForm() {
            document.getElementById("userListModalForm").reset();
            $("input[name='form_employeeID']").attr("readonly", false).css('background-color', '#FFFFFF');
            $("input[name='form_userID']").attr("readonly", false).css('background-color', '#FFFFFF');
        }
        $('#chooseFile').bind('change', function () {
          var filename = $("#chooseFile").val();
          if (/^\s*$/.test(filename)) {
            $(".file-upload").removeClass('active');
            $("#noFile").text("No file chosen..."); 
          }
          else {
            $(".file-upload").addClass('active');
            $("#noFile").text(filename.replace("C:\\fakepath\\", "")); 
          }
        });
    </script>
    <script type="text/javascript" src="static/appspecific/js/openNavLinks.js"></script>
    
</body>

</html>