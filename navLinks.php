<nav class="navbar navbar-expand-md navbar-light" style="background-color: #e3f2fd;">
    <!-- <a class="navbar-brand" href="#"><i class="fa fa-bullhorn" style="font-size:24px;color:blue"></i> Alarm List</a>-->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav">
            <li id="alarmNavLItag" class="nav-item ml-1">
                <a onclick="goHome()" class="btn "><i id="alarmNavItag" class="fa fa-bullhorn navLinkItags"></i> ALARMS</a>
            </li>
            <li id="updateNavLItag" class="nav-item ml-1">
                <a onclick="openUpdateList()" class="btn"><i id="updateNavItag" class="fa fa-edit navLinkItags"></i> Update Cause</a>
            </li>
            <li id="zoneNavLItag" class="nav-item ml-1">
                <a onclick="openZoneList()" class="btn"><i id="zoneNavItag" class="fa fa-object-ungroup navLinkItags"></i> Zones</a>
            </li>
            <li id="locationNavLItag" class="nav-item ml-1">
                <a onclick="openLocationList()" class="btn"><i id="locationNavItag" class="fa fa-building navLinkItags"></i> Locations</a>
            </li>
            <li id="contactNavLItag" class="nav-item ml-1">
                <a onclick="openContactList()" class="btn"><i id="contactNavItag" class="fa fa-address-card navLinkItags"></i> Contacts</a>
            </li>
            <li id="taskNavLItag" class="nav-item ml-1">
                <a onclick="openTaskList()" class="btn"><i id="taskNavItag" class="fa fa-list navLinkItags"></i> To-Do</a>
            </li>
            <li id="userNavLItag" class="nav-item ml-1">
                <a onclick="openUserList()" class="btn"><i id="userNavItag" class="fa fa-users navLinkItags"></i> Users</a>
            </li>
            <li id="adminNavLItag"  class="nav-item ml-1">
                <a onclick="openAdminPage()" class="btn"><i id="adminNavItag" class="fa fa-user-cog navLinkItags"></i> Admin</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <span class="mr-2">User: <?php echo $logged_userFirstName?></span>
            <!-- <button class="btn btn-info mr-1 addBtnClass" id="recordAddBtn" data-toggle="modal" data-target="#recordListModalID" style="width:auto;margin-left:20px;">ADD</button> -->
            <li id="logoutLItag" class="nav-item">
                <a style="" href="logout.php"><i class="fa fa-lock"></i>Logout</a>
            </li>
        </ul>
    </div>
</nav>