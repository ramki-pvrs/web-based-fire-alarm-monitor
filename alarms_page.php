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
        <title>ALARMS</title>
        <!-- https://codepen.io/sazzad/pen/antDJ -->
        <?php include ("head.php"); ?>
        <script type="text/javascript" src="static/appspecific/js/permitNavLinks.js"></script>
        <script type="text/javascript" src="static/appspecific/js/alarms.js"></script>
        <script type="text/javascript" src="static/public/js/Chart.min.js"></script>
        <!-- ROTATION NOT POSSIBLE<script type="text/javascript" src="static/public/js/chartjs-plugin-datalabels.js"></script> -->
        <style>
        @-webkit-keyframes argh-my-eyes {
            0% {
                background-color: red;
            }
            49% {
                background-color: red;
            }
            50% {
                background-color: #fff;
            }
            99% {
                background-color: #fff;
            }
            100% {
                background-color: red;
            }
        }

        @-moz-keyframes argh-my-eyes {
            0% {
                background-color: red;
            }
            49% {
                background-color: red;
            }
            50% {
                background-color: #fff;
            }
            99% {
                background-color: #fff;
            }
            100% {
                background-color: red;
            }
        }

        @keyframes argh-my-eyes {
            0% {
                background-color: red;
            }
            49% {
                background-color: red;
            }
            50% {
                background-color: #fff;
            }
            99% {
                background-color: #fff;
            }
            100% {
                background-color: red;
            }
        }

        .blinking {
            -webkit-animation: argh-my-eyes 1s infinite;
            -moz-animation: argh-my-eyes 1s infinite;
            animation: argh-my-eyes 1s infinite;
        }
        </style>
        <style type="text/css">
        .alert {
            padding-top: 50px;
            background-color: #f44336;
            color: white;
            height: 100px;
            text-align: center;
        }

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .closebtn:hover {
            color: black;
        }
        </style>
        <style type="text/css">
        /* The close button */

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 32px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }
        </style>
        <style type="text/css">
            #alarmNavItag {
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

        <!-- !PAGE CONTENT! -->
        <!-- id="locationListModalForm" https://v4-alpha.getbootstrap.com/components/forms/ -->
        <!-- https://www.w3schools.com/bootstrap4/bootstrap_forms_input_group.asp
                                https://www.w3schools.com/bootstrap4/tryit.asp?filename=trybs_form_input_group_multiple&stacked=h -->
        <!-- IMPORTANT https://bootstrapious.com/p/how-to-build-a-working-bootstrap-contact-form -->
        <div id="tableContainer" class="container-fluid p-5">
            <h5>Locations with Active Alarm (under maintenance (inactive) locations display no alarm)</h5>
            <div class="row">
                <div class="table-responsive">
                    <table id="alarmTbl" class="table table-bordered table-sm table-striped">
                        <thead>
                            <tr class="bg-danger">
                                <th>Zone</th>
                                <th>Location</th>
                                <th>Contact Name</th>
                                <th>Primary Contact</th>
                                <th>Alternate Contact</th>
                                <th>Alarm Start</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="redAlertsContainer" class="container-fluid p-5">
            <div id="redalerts" class="row">
            </div>
        </div>
        <div class="container-fluid p-5">
            <h5>Locations down from past X seconds (in CRUD.php remove LIMIT 1 in alarmList function)</h5>
            <div class="row">
                <div class="table-responsive">
                    <table id="downTbl" class="table table-bordered table-sm table-striped">
                        <thead>
                            <tr class="bg-warning">
                                <th>Zone</th>
                                <th>Location</th>
                                <th>Contact Name</th>
                                <th>Primary Contact</th>
                                <th>Alternate Contact</th>
                                <th>Last Update</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- https://www.buildingtechnologies.siemens.com/bt/global/en/firesafety/fire-detection/cerberus-pro-fire-safety-system/peripherals/alarm-equipment/pages/alarm-sounds.aspx -->
        <audio id="audiotag1" src="Cuttedfile.mp3" preload="auto"></audio>
        <div class="container-fluid p-5">
            <div class="pb-1">
                Recent number of alarms:
                <input id="numOfAlarms" type="number" value=10 style="width:50px;">
                <button id="barChartSize">Update</button> click to refresh chart!
            </div>
            <div class="row">
                <div class="col col-sm-12" id="chartContainer">
                    <!-- <canvas id="alarmChart" width="600" height="200" style="background: linear-gradient(darkgray, lightblue);"></canvas> -->
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
        //5000 milliseconds
        setInterval("my_function();", 5000);

        function my_function() {
            window.location = location.href;
        }
        </script>
        <script type="text/javascript" src="static/appspecific/js/openNavLinks.js"></script>

    </body>

    </html>