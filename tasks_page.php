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
    <title>TASKS</title>
    <!-- https://codepen.io/sazzad/pen/antDJ -->
    <?php include ("head.php"); ?>
    <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
    <script type="text/javascript" src="static/appspecific/js/tasks.js"></script>
    <style type="text/css">
        #taskNavItag {
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
                                <h8 id="taskModalTitle" class="modal-title">Add Task</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="taskListModalForm" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_task" class="pb-0 mb-0">Task Name</label>
                                                <input type="text" name="form_task" class="form-control" placeholder="Task todo" required="required" >
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_creator" class="pb-0 mb-0">Created By</label>
                                                <input type="text" name="form_creator" value='<?php echo $logged_userName?>' class="form-control" readonly>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_owner" class="pb-0 mb-0">Task Owner</label>
                                                <select id='ownerSelect' name="form_owner" class="form-control">
                                                    <option id="ta_opt_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_duedate" class="pb-0 mb-0">Due Date (default today)</label>
                                                <input id="duedefault" type="date" name="form_duedate" class="form-control">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="showStatesOnEdit">
                                            <div class="form-group">
                                                <label for="form_status" class="pb-0 mb-0">Update Status:</label>
                                                <select id='statusSelect' name="form_status" class="form-control">
                                                    <option value="Open" selected>Open</option>
                                                    <option value="Completed">Completed</option>
                                                    <option value="OnHold">OnHold</option>
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
                                        <button id="modalClearBtn" type="reset" class="btn btn-sm" value="clear" onclick="resetTaskForm()">Clear</button>
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
                <table id="taskListTbl" class="table table-bordered table-sm  table-striped">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Created By</th>
                            <th>Owner</th>
                            <th>Due By</th>
                            <th>Status</th>
                            <th>Comments</th>
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
        function resetTaskForm() {
            document.getElementById("taskListModalForm").reset();
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