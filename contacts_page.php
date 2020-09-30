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
    <title>CONTACTS</title>
    <!-- https://codepen.io/sazzad/pen/antDJ -->
    <?php include ("head.php"); ?>
    <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
    <script type="text/javascript" src="static/appspecific/js/contacts.js"></script>
    <style type="text/css">
        .fa-disabled {
              opacity: 0.6;
              cursor: not-allowed;
        }
        caption { 
          caption-side: top;
          align: left;
          color: red;
        }
    </style>
    <style type="text/css">
        #contactNavItag {
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
                                <h8 id="contactModalTitle" class="modal-title">Add Contact</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="contactListModalForm" class="form-horizontal">
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
                                                <label for="form_gender" class="pb-2 mb-0">Select Gender *</label>
                                                <select id="genderSelect" name="form_gender" class="form-control" required="required" data-error="Select Gender">
                                                   <option id="gen_opt_0" value="">Select</option>
                                                   <option id="gen_opt_1" value="Female">Female</option>
                                                   <option id="gen_opt_2" value="Male">Male</option>
                                                   <option id="gen_opt_3" value="Both">Both</option>
                                                   <option id="gen_opt_4" value="Na">NA</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_email" class="pb-2 mb-0">Email *</label>
                                                <input id="form_email" type="email" name="form_email" class="form-control" placeholder="Enter employee E-Mail">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_primaryContact" class="pb-0 mb-0">Primary Contact #</label>
                                                <input id="form_primaryContact" type="text" name="form_primaryContact" class="form-control" placeholder="Enter user primary contact">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_alternateContact" class="pb-0 mb-0">Alternate Contact #</label>
                                                <input type="text" name="form_alternateContact" class="form-control" placeholder="Enter user alternate contact">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div id="hideStatusSelect" class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_status" class="pb-0 mb-0">Select Status</label>
                                                <select id="statusSelect" name="form_status" class="form-control" required="required" data-error="Select Status">
                                                    <option id="s_1" value="Active">Active</option>
                                                    <option id="s_0" value="Inactive">Inactive</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="form_comment" class="pb-0 mb-0">Comment</label>
                                                <textarea name="form_comment" class="form-control" placeholder="Any comments?" rows="4"></textarea>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <!-- <input id="contactEditFormSubmitBtn" class="btn btn-success btn-sm mr-2" type="submit" value="Submit"> -->
                                        <button id="contactEditFormSubmitBtn" class="btn btn-success btn-sm mr-2" type="submit">Submit</button>
                                        <button id="modalClearBtn" type="reset" class="btn btn-sm" value="clear" onclick="resetContactForm()">Clear</button>
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
                <table id="contactListTbl" class="table table-bordered table-sm table-striped">
                    <!--<caption>Zone and Location ID can be changed only in Locations tab</caption>-->
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>E-Mail</th>
                            <th>Gender</th>
                            <th>Primary Contact</th>
                            <th>Alternate Contact</th>
                            <th>Status</th>
                            <th>Zone</th>
                            <th>Location ID</th>
                            <th>Comment</th>
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
        function resetContactForm() {
            document.getElementById("contactListModalForm").reset();
            //$("input[name='form_employeeID']").attr("readonly", false).css('background-color', '#FFFFFF');
            //$("input[name='form_userID']").attr("readonly", false).css('background-color', '#FFFFFF');
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