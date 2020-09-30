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
        <title>LOCATIONS</title>
        <!-- https://codepen.io/sazzad/pen/antDJ -->
        <?php include ("head.php"); ?>
        <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
        <script type="text/javascript" src="static/appspecific/js/locations.js"></script>
        <link rel="stylesheet" href="static/public/css/bootstrap-multiselect.css">
        <script type="text/javascript" src="static/public/js/bootstrap-multiselect.js"></script>
        <style type="text/css">
            #locationNavItag {
                aria-hidden:  true;
                font-size: 32px;
                color:  blue;
            }

            .fa-disabled {
              opacity: 0.6;
              cursor: not-allowed;
            }
            .modal-lg {
                max-width: 80% !important;
                max-height: 80% !important;
            }
            ul {
                /*-moz-column-count: 4;
                -moz-column-gap: 5px;
                -webkit-column-count: 4;
                -webkit-column-gap: 5px;
                column-count: 4;
                column-gap: 5px;*/
               /* -webkit-column-width: 100px;
                -moz-column-width: 100px;
                column-width: 100px;*/
                -webkit-column-count: 4; /* Chrome, Safari, Opera */
                -moz-column-count: 4; /* Firefox */
                column-count: 4;
                -webkit-column-gap: 10px; /* Chrome, Safari, Opera */
                -moz-column-gap: 10px; /* Firefox */
                column-gap: 10px;
                background-coler:red;
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
                                <h8 id="locationModalTitle" class="modal-title">Add Location</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="locationListModalForm" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_zone" class="pb-0 mb-0">Select Zone *</label>
                                                <select id="zoneSelect" name="form_zone" class="form-control" required="required" data-error="Select Zone!">
                                                    <option id="z_0" value="Select">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_locationID" class="pb-0 mb-0">Location ID *(unique; map to fire panel number)</label>
                                                <input type="text" name="form_locationID" class="form-control" placeholder="Location ID *" required="required">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_panelID" class="pb-0 mb-0">Panel ID</label>
                                                <input type="text" name="form_panelID" class="form-control" placeholder="Fire Panel ID">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="form_locationName" class="pb-0 mb-0">Location Name</label>
                                                <input type="text" name="form_locationName" class="form-control" placeholder="Ener Location Name">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_primaryContact" class="pb-0 mb-0">Primary Contact #</label>
                                                <input id="form_primaryContact" type="text" name="form_primaryContact" class="form-control" placeholder="Enter primary contact">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_contactName" class="pb-0 mb-0">Contact Name (auto filled on primary contact entry)</label>
                                                <input id="form_contactName" type="text" name="form_contactName" class="form-control" readonly="true">
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
                                    <div id="hideStatusSelect" class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_status">Update Status: (if Down, need location visit to fix status)</label>
                                                <select id='statusSelect' name="form_status" class="form-control">
                                                    <option id="s_0" value="Down">Down</option>
                                                    <option id="s_1" value="Active" selected>Active</option>
                                                    <option id="s_-1" value="Inactive">Inactive</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-success btn-sm mr-2" type="submit">Submit</button>
                                        <button id="modalClearBtn" type="reset" class="btn btn-sm" value="clear" onclick="resetLocForm()">Clear</button>
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


        <!-- START Location Group Modal -->

        <div id="locationGroupModalContainer" class="container">
            <div class="row mb-4">
                <!-- The Modal -->
                <!-- https://stackoverflow.com/questions/25874001/how-to-put-scrollbar-only-for-modal-body-in-bootstrap-modal-dialog -->
                <div class="modal hide fade" id="locationGroupModalID">
                    <div class="modal-dialog modal-lg modal-centered">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h8 id="locationModalTitle" class="modal-title">Map Multiple Locations to one Contact</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="locationGroupModalForm" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="locgrp_form_zone" class="pb-0 mb-0">Select Zone *</label>
                                                <select id="locgrp_zoneSelect" name="locgrp_form_zone" class="form-control" required="required" data-error="Select Zone!">
                                                    <option id="locgrp_z_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="locgrp_form_locID" class="pb-0 mb-0">Select Location IDs *</label>
                                                <select id="locgrp_locIDSelect" name="locgrp_form_locID" class="form-control" multiple required="required" data-error="Select Location IDs!">
                                                    <!-- <option id="locgrp_l_0" value="Select">Select</option> -->
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="locgrp_form_primaryContact" class="pb-0 mb-0">Primary Contact #</label>
                                                <input id="locgrp_form_primaryContact" type="text" name="locgrp_form_primaryContact" class="form-control" placeholder="Enter primary contact">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="locgrp_form_contactName" class="pb-0 mb-0">Contact Name (auto filled on primary contact entry)</label>
                                                <input id="locgrp_form_contactName" type="text" name="locgrp_form_contactName" class="form-control" readonly="true">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="locgrp_form_comment" class="pb-0 mb-0">Comment</label>
                                                <textarea name="locgrp_form_comment" class="form-control" placeholder="Any comments?" rows="2"></textarea>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-success btn-sm mr-2" type="submit">Submit</button>
                                        <button id="locationGroupModalClearBtn" type="reset" class="btn btn-sm" value="clear" onclick="resetLocGroupForm()">Clear</button>
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
        <!-- END Location Group Modal -->











        <div class="container-fluid p-4">
            <div class="row">
                <div class="table-responsive">
                    <table id="locationListTbl" class="table table-bordered table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Zone</th>
                                <th>Loc. ID</th>
                                <th>Fire Panel ID</th>
                                <th>Location Name</th>
                                <th>Contact Name</th>
                                <th>E-Mail</th>
                                <th>Primary #</th>
                                <th>Alternate #</th>
                                <th class="bg-danger">Alarm</th>
                                <th>Last Alarm Time</th>
                                <th>Notes</th>
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
        function resetLocForm() {
            document.getElementById("locationListModalForm").reset();
            $("input[name='form_locationID']").attr("readonly", false).css('background-color', '#FFFFFF');
        }

        function resetLocGroupForm() {
            document.getElementById("locationGroupModalForm").reset();
            $("input[name='form_locationID']").attr("readonly", false).css('background-color', '#FFFFFF');
        }

        $('#chooseFile').bind('change', function() {
            var filename = $("#chooseFile").val();
            if (/^\s*$/.test(filename)) {
                $(".file-upload").removeClass('active');
                $("#noFile").text("No file chosen...");
            } else {
                $(".file-upload").addClass('active');
                $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
            }
        });
        </script>
        <script type="text/javascript" src="static/appspecific/js/openNavLinks.js"></script>
    </body>

    </html>