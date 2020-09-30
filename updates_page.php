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
    <title>ROOTCAUSE</title>
    <!-- https://codepen.io/sazzad/pen/antDJ -->
    <?php include ("head.php"); ?>
    <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
    <script type="text/javascript" src="static/appspecific/js/updates.js"></script>
    <script type="text/javascript" src="static/public/js/Chart.min.js"></script>
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
                                <h8 id="alarmDownReasonModalTitle" class="modal-title">Update Root Cause</h8>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <form id="alarmDownReasonModalForm" class="form-horizontal">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="form_zone" class="pb-0 mb-0">Zone</label>
                                                <input type="text" name="form_zone" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="form_locationID" class="pb-0 mb-0">Location ID</label>
                                                <input type="text" name="form_locationID" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="lastAlarmTimeDIVid" class="form-group">
                                                <label for="form_alarmTime" class="pb-0 mb-0">Alarm Time</label>
                                                <input type="text" name="form_alarmTime" class="form-control">
                                            </div>
                                            <div id="lastUpdateTimeDIVid" class="form-group">
                                                <label for="form_lastUpdateTime" class="pb-0 mb-0">Down Time</label>
                                                <input type="text" name="form_lastUpdateTime" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="form_status" class="pb-0 mb-0">Current Status</label>
                                                <input type="text" name="form_status" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="form_rootcause" class="pb-0 mb-0">Select Root Cause</label>
                                                <select id="rootCauseSelect" name="form_rootcause" class="form-control" required="required" data-error="Select Cause!">
                                                   <option id="ac_opt_0" value="">Select</option>
                                                </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="form_comment" class="pb-0 mb-0">Comment</label>
                                                <textarea name="form_comment" class="form-control" placeholder="Any comments?" rows="4"></textarea>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group">
                                        <button class="btn btn-success btn-sm mr-2" type="submit">Update</button>
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
        <div class="row pb-5">
            <div class="col col-sm-12">
                <h5>Update root cause for  Fire Alarm(s)</h5>
                <table id="alarmReasonTbl" class="table table-bordered table-sm table-striped">
                    <thead>
                        <tr class="bg-warning">
                            <th>Zone</th>
                            <th>Location</th>
                            <th>Last Alarm</th>
                            <th>Current Status</th>
                            <th>Root Cause</th>
                            <th>Comment</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col col-sm-12">
                <h5>Update root cause why location(s) is/was down</h5>
                <p>Last Update column shows timestamp when last health signal was received from fire panel; if more than 5 seconds old, in this table, it will be reported as fire panel is down</p>
                <p>If you observe same location with multiple rows with close timestamps, that means system is unstable; please investigate</p>
               <table id="downReasonTbl" class="table table-bordered table-sm table-striped">
                    <thead>
                        <tr class="bg-warning">
                            <th>Zone</th>
                            <th>Location</th>
                            <th>Last Update</th>
                            <th>Current Status</th>
                            <th>Root Cause</th>
                            <th>Comment</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
        </div>
    </div>

    <div class="container-fluid p-4">
        <div class="row pt-5">
            <div class="col col-sm-6 pb-1">
                # of past Alarms Data: 
                <input id="numOfAlarms" type="number" value=60 style="width:50px;">
                <button id="alarmReasonChartNumbers">Update</button>
            </div>

            <div class="col col-sm-6 pb-1">
                # of past records when locations were down: 
                <input id="numOfDowns" type="number" value=60 style="width:50px;">
                <button id="downReasonChartNumbers">Update</button>
            </div>
        </div>
        <div class="row">
            <div class="col col-sm-6" id="alarmReasonChartContainer">
                <!-- <canvas id="alarmChart" width="600" height="200" style="background: linear-gradient(darkgray, lightblue);"></canvas> -->
            </div>
            
            <div class="col col-sm-6" id="downReasonChartContainer">
                <!-- <canvas id="alarmChart" width="600" height="200" style="background: linear-gradient(darkgray, lightblue);"></canvas> -->
            </div>
        </div>
    </div> 
       
        
    
    <?php include ("footer.php"); ?>

    
    <script type="text/javascript" src="static/appspecific/js/openNavLinks.js"></script>
    
</body>

</html>