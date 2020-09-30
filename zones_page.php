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
    <title>ZONES</title>
    <!-- https://codepen.io/sazzad/pen/antDJ -->
    <?php include ("head.php"); ?>
    <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
    <script type="text/javascript" src="static/appspecific/js/zones.js"></script>
    <style type="text/css">
        .fa-disabled {
              opacity: 0.6;
              cursor: not-allowed;
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
                                <h8 id="zoneModalTitle" class="modal-title">Add Zone</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="zoneListModalForm" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_zone" class="pb-0 mb-0">Zone Name</label>
                                                <input type="text" name="form_zone" class="form-control" placeholder="Zone Name" required="required" >
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div id="hideStatusSelect" class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_status">Update Status:</label>
                                                <select id='statusSelect' name="form_status" class="form-control">
                                                    <option id="s_1" value="Active" selected>Active</option>
                                                    <option id="s_-1" value="Inactive">Inactive</option>
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
                                        <button class="btn btn-success btn-sm mr-2" type="submit">Submit</button>
                                        <button id="modalClearBtn" type="reset" class="btn btn-sm" value="clear" onclick="resetZoneForm()">Clear</button>
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
                <table id="zoneListTbl" class="table table-bordered table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Zone</th>
                            <th>Status</th>
                            <th>Notes</th>
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
        function resetZoneForm() {
            document.getElementById("zoneListModalForm").reset();
            $("input[name='form_zone']").attr("readonly", false).css('background-color', '#FFFFFF');
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