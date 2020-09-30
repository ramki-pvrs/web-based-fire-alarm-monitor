<?php
//echo(__DIR__);
session_start();
include_once __DIR__ .'/static/public/CSRF-Protector-PHP/libs/csrf/csrfprotector.php';
//Initialise CSRFGuard library
csrfProtector::init();

date_default_timezone_set('America/New_York');
include dirname(__FILE__).'/dbconnect.php';
//the minus number in below gives past date
$GLOBALS['terminationDate']=date("Y-m-d", mktime(0, 0, 0, date("m")-3, 1, date("Y")));
  function date_range($first, $last, $step='+1 month', $output_format='Y-m-t' ) {
    $dates=array();
    $current=strtotime($first);
    $last=strtotime($last);
    while( $current <= $last ) {
        $dates[]=date($output_format, $current);
        $current=strtotime($step, $current);
    }
    return $dates;
  };//END of date_range function

  //http://logging.apache.org/log4php/quickstart.html
  // Insert the path where you unpacked log4php
 //include dirname(__FILE__).'/../public/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
  //include('/../public/lib/apache-log4php-2.3.0/src/main/php/Logger.php');
  // Tell log4php to use our configuration file.
  //Logger::configure('config.xml');
  // Fetch a logger, it will inherit settings from the root logger
  //$log=Logger::getLogger('myLogger');

if(isset($_POST['dbData']['actionfunction']) && $_POST['dbData']['actionfunction']!=''){
  $actionfunction=$_POST['dbData']['actionfunction'];
  call_user_func($actionfunction,$_POST,$con);
}

function alarmList($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];

  $selectAlarmsArray=array();

  $selectAlarmsQuery="SELECT l.id AS lid, z.id AS zid, z.zone, l.locationID, 
                             CONCAT(con.firstName, ' ', con.lastName) AS contactName, 
                             con.primaryContact, con.alternateContact, 
                             DATE_FORMAT(l.lastAlarmTime,'%d-%m-%Y %H:%i:%S') AS lastAlarmTime, l.comment, 
                             IF((l.alertMe=1 OR closeAlertTime < DATE_SUB(NOW(),INTERVAL (SELECT popupTimer FROM hootus.apptimers WHERE id=1) SECOND)), 'alertMe', 'closeAlert') AS alertMe
                     FROM hootus.location AS l
                     LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                     LEFT JOIN hootus.locationcontact AS lc on lc.location_id=l.id  AND lc.currentContact = 1
                     LEFT JOIN hootus.contact AS con on con.id=lc.contact_id
                     WHERE l.status!=-1 AND l.lastAlarm=1 
                     ORDER BY l.zone_id,l.locationID;";
  $selectAlarms=$con->prepare($selectAlarmsQuery);
  $selectAlarms->execute();
  $selectAlarmsArray['alarms']=$selectAlarms->fetchAll(PDO::FETCH_ASSOC);

  $selectDownsQuery="SELECT l.id AS lid, z.id AS zid, z.zone, l.locationID, 
                            CONCAT(con.firstName, ' ', con.lastName) AS contactName, 
                            con.primaryContact, con.alternateContact, 
                            DATE_FORMAT(l.lastStatusUpdate,'%d-%m-%Y %H:%i:%S') AS lastStatusUpdate, l.comment
                     FROM hootus.location AS l
                     LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                     LEFT JOIN hootus.locationcontact AS lc on lc.location_id=l.id   AND lc.currentContact = 1
                     LEFT JOIN hootus.contact AS con on con.id=lc.contact_id
                     WHERE l.status=0 
                     ORDER BY l.zone_id,l.locationID LIMIT 1;";
  $selectDowns=$con->prepare($selectDownsQuery);
  $selectDowns->execute();
  $selectAlarmsArray['downs']=$selectDowns->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json;charset=utf-8");
  echo json_encode($selectAlarmsArray);
} //END OF alarmList

function setAlertMe($data,$con) { 
  $sql="UPDATE hootus.location SET alertMe=1, closeAlertTime = NULL WHERE lastAlarm=1;";
  $sql=$con->prepare($sql);
 
  $con->beginTransaction(); 
  $sql->execute();
  $con->commit();
} //END OF setAlertMe

function resetAlertMe($data,$con) { 
  $id=$data['dbData']['id']; //primary key 'id'
  $sql="UPDATE hootus.location SET alertMe=0, closeAlertTime=CURRENT_TIME WHERE id=:id;";
  $sql=$con->prepare($sql);

  $sql->bindParam(':id', $id, PDO::PARAM_INT);
 
  $con->beginTransaction(); 
  $sql->execute();
  $con->commit();
} //END OF resetAlertMe

function alarmDownCauseList($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $selectCausesArray=array();

  $selectCausesQuery="SELECT id, rootCause FROM hootus.rootcause;";
  $selectCauses=$con->prepare($selectCausesQuery);
  $selectCauses->execute();
  $selectCausesArray['causes']=$selectCauses->fetchAll(PDO::FETCH_ASSOC);

  $selectAlarmCausesQuery="SELECT ah.id AS ahid, l.id AS lid, z.id AS zid, z.zone, l.locationID, 
                             DATE_FORMAT(l.lastAlarmTime,'%d-%m-%Y %H:%i:%S') AS lastAlarmTime,
                             IF(l.status=1,'Active',IF(l.status=-1,'Inactive','Down')) AS status,
                             rc.id AS rcid, rc.rootCause, ah.comment
                     FROM hootus.alarmhistory AS ah
                     LEFT JOIN hootus.location AS l ON l.id = ah.location_id
                     LEFT JOIN hootus.rootcause AS rc ON rc.id = ah.rootcause_id
                     LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                     WHERE l.status!=-1 
                     ORDER BY l.zone_id,l.locationID;";
  $selectAlarmCauses=$con->prepare($selectAlarmCausesQuery);
  $selectAlarmCauses->execute();
  $selectCausesArray['alarmhistory']=$selectAlarmCauses->fetchAll(PDO::FETCH_ASSOC);

  $selectDownCausesQuery="SELECT dh.id AS dhid, l.id AS lid, z.id AS zid, z.zone, l.locationID, 
                             DATE_FORMAT(l.lastStatusUpdate,'%d-%m-%Y %H:%i:%S') AS lastStatusUpdate,
                             IF(l.status=1,'Active',IF(l.status=-1,'Inactive','Down')) AS status,
                             rc.id AS rcid, rc.rootCause,dh.comment
                     FROM hootus.downhistory AS dh
                     LEFT JOIN hootus.location AS l ON l.id = dh.location_id
                     LEFT JOIN hootus.rootcause AS rc ON rc.id = dh.rootcause_id
                     LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                     WHERE l.status=0 
                     ORDER BY l.zone_id,l.locationID LIMIT 5;";
  $selectDownCauses=$con->prepare($selectDownCausesQuery);
  $selectDownCauses->execute();
  $selectCausesArray['downhistory']=$selectDownCauses->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json;charset=utf-8");
  echo json_encode($selectCausesArray);
} //END OF alarmDownCauseList

function updateCause($data,$con){
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUser_id=$data['dbData']['loggedUser_id'];
  $id=$data['dbData']['id']; //primary key 'id' of alarmhistory or downhistory tables
  $rootcause_id = $data['dbData']['rootcause_id'];
  $comment=$data['dbData']['comment'];
  $todo=$data['dbData']['todo'];
  
  if($todo === "updateAlarmHistory") {
    $sql="UPDATE hootus.alarmhistory SET rootcause_id=:rootcause_id, comment=:comment, user_id=:loggedUser_id  WHERE id=:id;";
    $sql=$con->prepare($sql);

    $sql->bindParam(':rootcause_id', $rootcause_id, PDO::PARAM_INT);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':loggedUser_id', $loggedUser_id, PDO::PARAM_INT);
    $sql->bindParam(':id', $id, PDO::PARAM_INT);

    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();

    $selectAlarmHistoryArray=array();
    $selectAlarmHistoryQuery="SELECT ah.id AS ahid, rc.id AS rcid, rc.rootCause, ah.comment 
                       FROM hootus.alarmhistory AS ah
                       LEFT JOIN hootus.rootcause AS rc ON rc.id = ah.rootcause_id
                       WHERE ah.id=:id;";
    $selectAlarmHistory=$con->prepare($selectAlarmHistoryQuery);
    $selectAlarmHistory->bindParam(':id', $id, PDO::PARAM_INT);
    $selectAlarmHistory->execute();
    $selectAlarmHistoryArray['alarmhistory']=$selectAlarmHistory->fetchAll(PDO::FETCH_ASSOC);
    
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($selectAlarmHistoryArray);
  } else {
    $sql="UPDATE hootus.downhistory SET rootcause_id=:rootcause_id, comment=:comment, user_id=:loggedUser_id WHERE id=:id;";
    $sql=$con->prepare($sql);

    $sql->bindParam(':rootcause_id', $rootcause_id, PDO::PARAM_INT);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':loggedUser_id', $loggedUser_id, PDO::PARAM_INT);
    $sql->bindParam(':id', $id, PDO::PARAM_INT);

    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();

    $selectDownHistoryArray=array();
    $selectDownHistoryQuery="SELECT dh.id AS dhid, rc.id AS rcid, rc.rootCause, dh.comment 
                             FROM hootus.downhistory AS dh
                             LEFT JOIN hootus.rootcause AS rc ON rc.id = dh.rootcause_id
                             WHERE dh.id=:id;";
    $selectDownHistory=$con->prepare($selectDownHistoryQuery);
    $selectDownHistory->bindParam(':id', $id, PDO::PARAM_INT);
    $selectDownHistory->execute();
    $selectDownHistoryArray['downhistory']=$selectDownHistory->fetchAll(PDO::FETCH_ASSOC);
    
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($selectDownHistoryArray);
  }
  
} //END of updateCause

//this function is not used now
function getLineChartData($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $selectHistoryArray=array();
  $chartSize=$data['dbData']['chartSize'];

  $selectHistoryQuery="SELECT * FROM (SELECT setreset, SUM(alarm) AS raised, SUM(!alarm) AS reset 
                                      FROM hootus.alarmhistory 
                                      GROUP BY setreset 
                                      ORDER BY setreset DESC 
                                      LIMIT :chartSize) AS s 
                       ORDER BY setreset ASC;";
  $selectHistory=$con->prepare($selectHistoryQuery);
  $selectHistory->bindValue(':chartSize', (int)$chartSize, PDO::PARAM_INT);
  $selectHistory->execute();
  $selectHistoryArray['history']=$selectHistory->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectHistoryArray);
} //END OF getLineChartData

function getBarChartData($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $selectHistoryArray=array();
  $barChartSize=$data['dbData']['barChartSize'];

  $selectHistoryQuery="SELECT CONCAT(z.zone,':',l.locationID) AS locationID, 
                              ah.alarmON, ah.alarmONTime, ah.alarmOFF, 
                              IFNULL(ah.alarmOFFTime,0) AS alarmOFFTime
                      FROM hootus.alarmhistory AS ah 
                      LEFT JOIN hootus.location l ON l.id=ah.location_id
                      LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                      ORDER BY ah.alarmOFF
                      LIMIT :barChartSize;";
  $selectHistory=$con->prepare($selectHistoryQuery);
  $selectHistory->bindValue(':barChartSize', (int)$barChartSize, PDO::PARAM_INT);
  $selectHistory->execute();
  $selectHistoryArray['history']=$selectHistory->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectHistoryArray);
} //END OF getBarChartData

function getCauseChartData($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $selectCauseArray=array();
  $barChartSize=$data['dbData']['barChartSize'];
  $tblName=$data['dbData']['tblName'];
  //the query is set such that even when a specific cause does not have a value, 
  // for the chart to show zero for that cause, that cause is output from this query, including 'No-update'
  $selectCauseQuery="SELECT rootCause, SUM(count) AS count FROM (
                                     (SELECT IFNULL(rc.rootCause,'No-Update') AS rootCause, COUNT(h.id) AS count
                                      FROM hootus.".$tblName." AS h                      
                                      LEFT JOIN hootus.rootcause AS rc ON rc.id = h.rootcause_id
                                      GROUP BY rc.rootCause)
                                      UNION 
                                      (SELECT rootCause, 0 AS count FROM rootcause)
                                      UNION 
                                      (SELECT 'No-Update' AS rootCause, 0 AS count)) AS s
                        GROUP BY s.rootCause
                        ORDER BY FIELD(s.rootCause,'No-Update', 'False','Electrical','Non-Electrical','Personnel','Others')
                       LIMIT :barChartSize;";
  $selectCause=$con->prepare($selectCauseQuery);
  $selectCause->bindValue(':barChartSize', (int)$barChartSize, PDO::PARAM_INT);
  $selectCause->execute();
  $selectCauseArray['causes']=$selectCause->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectCauseArray);
} //END OF getCauseChartData

function updateTimer($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $whichTimer = $data['dbData']['whichTimer'];
  $timerValue = $data['dbData']['timerValue'];
  if($whichTimer === "popupTimer") {

    if($timerValue < 60) {
      $timerValue = 60;
    }
    $sql="UPDATE hootus.apptimers SET popupTimer=:timerValue WHERE id=1;";
    $sql=$con->prepare($sql);
    $sql->bindParam(':timerValue', $timerValue, PDO::PARAM_INT);
    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();

    $dropTblquery = "DROP TRIGGER IF EXISTS before_location_update;";
    $dropTmpTbl=$con->prepare($dropTblquery);
    $dropTmpTbl->execute();
    $dropTmpTbl= null;

    $selectTimerQuery="CREATE TRIGGER before_location_update 
                            BEFORE UPDATE ON location FOR EACH ROW 
                        BEGIN
                          IF (NEW.lastAlarm = 1) THEN
                            -- NEW.closeAlertTime > one minute back means, it is within one minute range; 
                            -- if less, age is more than a minute and set alertMe=1 again because fire alarm is still active (NEW.lastAlarm=1)
                                SET NEW.lastAlarmTime = CURRENT_TIMESTAMP, NEW.alertMe=IF(NEW.closeAlertTime = NULL, 1, 
                                    IF(NEW.closeAlertTime > DATE_SUB(NOW(),INTERVAL (SELECT popupTimer FROM hootus.apptimers WHERE id=1) SECOND),0,1));
                            END IF;
                            IF (NEW.lastAlarm = 0) THEN 
                                SET NEW.alertMe = 0, NEW.closeAlertTime = NULL;
                            END IF;
                            IF (NEW.lastAlarm != OLD.lastAlarm) THEN
                              IF (NEW.lastAlarm = 1) THEN 
                                INSERT INTO alarmhistory
                                SET  location_id = OLD.id,
                                   alarmON = 1,
                                           alarmOnTime = CURRENT_TIMESTAMP;
                                END IF;
                                  IF (NEW.lastAlarm = 0) THEN 
                                UPDATE alarmhistory
                                SET  alarmOFF = 1,
                                           alarmOFFTime = CURRENT_TIMESTAMP
                                WHERE location_id = OLD.id AND alarmON = 1 AND alarmONTime IS NOT NULL AND alarmOFFTime IS NULL;
                                END IF;
                            END IF;
                            IF (OLD.status = 1 AND NEW.status = 0) THEN
                              INSERT INTO downhistory
                              SET  location_id = OLD.id;
                            END IF;
                          END";
    $selectTimer=$con->prepare($selectTimerQuery);
    //$selectTimer->bindValue(':timerValue', (int)$timerValue, PDO::PARAM_INT);
    $selectTimer->execute();
  } else if($whichTimer === "healthTimer") {

    if($timerValue < 6) {
      $timerValue = 6;
    }
    $sql="UPDATE hootus.apptimers SET healthTimer=:timerValue WHERE id=1;";
    $sql=$con->prepare($sql);
    $sql->bindParam(':timerValue', $timerValue, PDO::PARAM_INT);
    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();

    $dropTblquery = "DROP EVENT IF EXISTS statusCheck;";
    $dropTmpTbl=$con->prepare($dropTblquery);
    $dropTmpTbl->execute();
    $dropTmpTbl= null;

    $sqlX = "SET @healthTimerValue = (SELECT healthTimer FROM hootus.apptimers WHERE id=1);";
    $sqlXX=$con->prepare($sqlX);
    $sqlXX->execute();
    $sqlXX= null;

    $selectTimerQuery="CREATE EVENT statusCheck
                            ON SCHEDULE
                              EVERY @healthTimerValue SECOND
                        DO 
                        BEGIN
                          SET @timenow = NOW();
                          UPDATE `hootus`.`location` SET `status` = 0
                          WHERE `panelID` IS NOT NULL AND `status` != -1 AND `lastStatusUpdate` < DATE_SUB(@timenow, INTERVAL @healthTimerValue SECOND);

                          UPDATE `hootus`.`location` SET `status` = 1
                          WHERE `panelID` IS NOT NULL AND `status` != -1 AND `lastStatusUpdate` > DATE_SUB(@timenow, INTERVAL @healthTimerValue SECOND);
                        END";
    $selectTimer=$con->prepare($selectTimerQuery);
    $selectTimer->execute();
  }

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode("Successful");
} //END OF updateTimer

function getRoleEntityData($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];


  $selectRoleEntityArray=array();

  $selectRolesQuery="SELECT id, role FROM hootus.role ORDER BY role;";
  $selectRoles=$con->prepare($selectRolesQuery);
  $selectRoles->execute();
  $selectRoleEntityArray['roles']=$selectRoles->fetchAll(PDO::FETCH_ASSOC);

  $selectEntitiesQuery="SELECT DISTINCT entity FROM hootus.permission ORDER BY entity;";
  $selectEntities=$con->prepare($selectEntitiesQuery);
  $selectEntities->execute();
  $selectRoleEntityArray['entities']=$selectEntities->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json;charset=utf-8");
  echo json_encode($selectRoleEntityArray);
} //END OF getRoleEntityData

function updatePermission($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $todo = $data['dbData']['todo'];
  $role_id = $data['dbData']['role_id'];
  $entities = $data['dbData']['entities'];
  //echo json_encode($entity);
  $Read = $data['dbData']['Read'];
  $Update = $data['dbData']['Update'];
  $Create = $data['dbData']['Create'];

  $permissions = [];

  if($todo == "Update") {
    if($Create == "true") {
      $permissions = ["C", "U", "R"];
      permissionTblsUpdate($con, $entities, $permissions, $role_id);
    } else if($Update == "true") {
      $permissions = ["U", "R"];
      permissionTblsUpdate($con, $entities, $permissions, $role_id);
    } else if($Read == "true") {
      $permissions = ["R"];
      permissionTblsUpdate($con, $entities, $permissions, $role_id);
    }
  } else { //delete
    if($Read == "true") {
      $permissions = ["C", "U", "R"];
      rolepermissionTblDelete($con, $entities, $permissions, $role_id);
    } else if($Update == "true") {
      $permissions = ["C", "U"];
      rolepermissionTblDelete($con, $entities, $permissions, $role_id);
    } else if($Create == "true") {
      $permissions = ["C"];
      rolepermissionTblDelete($con, $entities, $permissions, $role_id);
    }
  }
  header("Content-Type: application/json;charset=utf-8");
  echo json_encode("Successful");
} //END OF updatePermission

function locationZoneList($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];
  $selectZoneLocationsArray=array();

  $userPermissionValue = checkPermission($con, $loggedUser, $loggedUserRole, "Locations");
  //echo (json_encode($userPermissionValue["Locations"]));

  $selectZonesQuery="SELECT id, zone, IF(status=1,'Active','Inactive') AS status, comment  FROM hootus.zone ORDER BY zone;";
  $selectZones=$con->prepare($selectZonesQuery);
  $selectZones->execute();
  $selectZoneLocationsArray['zones']=$selectZones->fetchAll(PDO::FETCH_ASSOC);

  $selectLocationsQuery="SELECT l.id AS lid, z.id AS zid, z.zone, l.locationID, l.panelID, l.locationName, 
                                con.id AS conid, con.email, CONCAT(con.firstName, ' ',con.lastName) AS contactName, 
                                con.primaryContact, con.alternateContact, 
                                l.lastAlarm, DATE_FORMAT(l.lastAlarmTime,'%d-%m-%Y %H:%i:%S') AS lastAlarmTime, 
                                l.comment, IF(l.status=1,'Active',IF(l.status=-1,'Inactive','Down')) AS status
                        FROM hootus.location AS l
                        LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                        LEFT JOIN hootus.locationcontact AS lc on lc.location_id=l.id AND lc.currentContact = 1
                        LEFT JOIN hootus.contact AS con on con.id=lc.contact_id
                        ORDER BY l.lastAlarm DESC, z.id;";
  $selectLocations=$con->prepare($selectLocationsQuery);
  $selectLocations->execute();
  $selectZoneLocationsArray['locations']=$selectLocations->fetchAll(PDO::FETCH_ASSOC);

  $selectZoneLocationsArray['userPermissionValue']=$userPermissionValue["Locations"];

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectZoneLocationsArray);
} //END OF locationZoneList

function getLocationsOfZone($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];
  $zone_id=$data['dbData']['zone_id']; //primary key 'id'
  $selectLocationIDsArray=array();

  $selectLocationsQuery="SELECT l.id AS lid, l.locationID
                        FROM hootus.location AS l WHERE zone_id=".$zone_id.";";
  $selectLocations=$con->prepare($selectLocationsQuery);
  $selectLocations->execute();
  $selectLocationIDsArray['locationIDs']=$selectLocations->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectLocationIDsArray);
} //END OF getLocationsOfZone

function insertORupdateZone($data,$con){
  $loggedUser=$data['dbData']['loggedUser'];
  $id=$data['dbData']['id']; //primary key 'id'
  $zone=$data['dbData']['zone']; 
  $status=$data['dbData']['status'];
  $comment=$data['dbData']['comment'];
  $todo=$data['dbData']['todo'];
  $whereid=null;
  if($todo === "insert") {
    $sql="INSERT INTO hootus.zone(zone, comment) VALUES (:zone,:comment)";
    $sql=$con->prepare($sql);

    $sql->bindParam(':zone', $zone, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);

    $con->beginTransaction(); 
    $sql->execute();
    $whereid=$con->lastInsertId();
    $con->commit();
  } else {
    $sql="UPDATE hootus.zone SET zone=:zone, comment=:comment, status=:status WHERE id=:id;";
    $sql=$con->prepare($sql);

    $sql->bindParam(':zone', $zone, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':status', $status, PDO::PARAM_INT);
    $sql->bindParam(':id', $id, PDO::PARAM_INT);
   
    $whereid=$id;
    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();
  }
  $selectZonesArray=array();
  $selectZonesQuery="SELECT id, zone, comment, IF(status=1,'Active','Inactive') AS status 
                          FROM hootus.zone
                          WHERE id=:id;";
  $selectZones=$con->prepare($selectZonesQuery);
  $selectZones->bindParam(':id', $whereid, PDO::PARAM_INT);
  $selectZones->execute();
  $selectZonesArray['zones']=$selectZones->fetchAll(PDO::FETCH_ASSOC);
  
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectZonesArray);
} //END of insertORupdateZone

function getContactName($data,$con){
  $loggedUser=$data['dbData']['loggedUser'];
  $primaryContact=$data['dbData']['primaryContact']; 

  $selectContactsArray=array();
  $selectContactsQuery="SELECT id, COUNT(id) AS count, CONCAT(firstName, ' ', lastName) AS contactName, primaryContact
                          FROM hootus.contact
                          WHERE primaryContact=:primaryContact;";
  $selectContacts=$con->prepare($selectContactsQuery);
  $selectContacts->bindParam(':primaryContact', $primaryContact, PDO::PARAM_STR);
  $selectContacts->execute();
  $selectContactsArray['contacts']=$selectContacts->fetchAll(PDO::FETCH_ASSOC);
  
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectContactsArray);
} //END of getContactName

function insertORupdateLocation($data,$con){
  $loggedUser=$data['dbData']['loggedUser'];
  $id=$data['dbData']['id']; //primary key 'id'
  $zone_id=$data['dbData']['zone_id'];
  $currentContact_id=$data['dbData']['currentContact_id'];
  $newContact_id=$data['dbData']['newContact_id']; 
  $locID=$data['dbData']['locID'];
  $panelID=$data['dbData']['panelID'];
  $locName=$data['dbData']['locName'];
  $status=$data['dbData']['status'];
  $comment=$data['dbData']['comment'];
  $todo=$data['dbData']['todo'];
  $whereid=null;
  if($todo === "insert") {
    $sql="INSERT INTO hootus.location(zone_id, locationID, panelID, locationName, comment) 
            VALUES (:zone_id,:locationID,:panelID, :locationName,:comment)";
    $sql=$con->prepare($sql);

    $sql->bindParam(':zone_id', $zone_id, PDO::PARAM_INT);
    $sql->bindParam(':locationID', $locID, PDO::PARAM_INT);
    $sql->bindParam(':panelID', $panelID, PDO::PARAM_INT);
    $sql->bindParam(':locationName', $locName, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);

    $con->beginTransaction(); 
    $sql->execute();
    $location_id=$con->lastInsertId();
    $con->commit();

    if($newContact_id != 0) {
      $sql="INSERT INTO hootus.locationcontact(location_id, contact_id) 
              VALUES (:location_id, :newContact_id)";
      $sql=$con->prepare($sql);

      $sql->bindParam(':location_id', $location_id, PDO::PARAM_INT);
      $sql->bindParam(':newContact_id', $newContact_id, PDO::PARAM_INT);
      $con->beginTransaction(); 
      $sql->execute();
      $locationcontact_id=$con->lastInsertId();
      $con->commit();
    }
  } else {
    $sql="UPDATE hootus.location SET zone_id=:zone_id, locationID=:locationID, locationName=:locationName, comment=:comment, status=:status WHERE id=:id;";
    $sql=$con->prepare($sql);
    //echo json_encode($sql);

    $sql->bindParam(':zone_id', $zone_id, PDO::PARAM_INT);
    $sql->bindParam(':locationID', $locID, PDO::PARAM_INT);
    $sql->bindParam(':locationName', $locName, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':status', $status, PDO::PARAM_INT);
    $sql->bindParam(':id', $id, PDO::PARAM_INT);
   
    $location_id=$id;
    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();

    if($currentContact_id != $newContact_id) {
        $sql="UPDATE hootus.locationcontact SET currentContact=0, actualChkOut=CURRENT_TIMESTAMP 
              WHERE location_id=:location_id AND contact_id=:currentContact_id;";
        $sql=$con->prepare($sql);

        $sql->bindParam(':location_id', $location_id, PDO::PARAM_INT);
        $sql->bindParam(':currentContact_id', $currentContact_id, PDO::PARAM_INT);
       
        $con->beginTransaction(); 
        $sql->execute();
        $con->commit();

        $sql="INSERT INTO hootus.locationcontact(location_id, contact_id) 
                VALUES (:location_id, :newContact_id)";
        $sql=$con->prepare($sql);

        $sql->bindParam(':location_id', $location_id, PDO::PARAM_INT);
        $sql->bindParam(':newContact_id', $newContact_id, PDO::PARAM_INT);
        $con->beginTransaction(); 
        $sql->execute();
        $locationcontact_id=$con->lastInsertId();
        $con->commit();
    }

  }
  $selectLocsArray=array();
  $selectLocationsQuery="SELECT l.id AS lid, z.id AS zid, z.zone, l.locationID, l.panelID, l.locationName, 
                                con.id AS conid, con.email, CONCAT(con.firstName, ' ', con.lastName) AS contactName, 
                                con.primaryContact, con.alternateContact, 
                                l.lastAlarm, l.lastAlarmTime, l.comment, IF(l.status=1,'Active',IF(l.status=-1,'Inactive','Down')) AS status,
                                lc.id AS lcid 
                          FROM hootus.location AS l
                          LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                          LEFT JOIN hootus.locationcontact AS lc on lc.location_id=l.id AND lc.currentContact = 1
                          LEFT JOIN hootus.contact AS con on con.id=lc.contact_id
                          WHERE l.id=:id;";
  $selectLocations=$con->prepare($selectLocationsQuery);
  $selectLocations->bindParam(':id', $location_id, PDO::PARAM_INT);
  $selectLocations->execute();
  $selectLocationsArray['locations']=$selectLocations->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectLocationsArray);
} //END of insertORupdateLocation

function updateLocationGroupContact($data,$con){
  $loggedUser=$data['dbData']['loggedUser'];
  $location_ids=$data['dbData']['location_ids']; //primary key 'id'
  $contact_id=$data['dbData']['contact_id']; //primary key 'id'
  //$primaryContact=$data['dbData']['primaryContact'];
  $comment=$data['dbData']['comment'];


  //when you receive a list of locations where you have to update the primaryContact
  //first for those locations  rows set currentContact flag to zero

  // may be there will be duplicate rows, for now lets keep it ok to have duplicates
  $sql="UPDATE locationcontact AS lc
        INNER JOIN contact AS c ON c.id = lc.contact_id
        INNER JOIN location AS l ON l.id = lc.location_id
        SET currentContact = 0, actualChkOut = CURRENT_TIMESTAMP
        WHERE FIND_IN_SET(l.id, :location_id);";
  $sql=$con->prepare($sql);
  foreach($location_ids AS $location_id) {
    $sql->bindParam(':location_id', $location_id, PDO::PARAM_INT);
    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();
  }
  

  $sql="INSERT INTO hootus.locationcontact(location_id, contact_id, comment) 
          VALUES (:location_id, :contact_id, :comment)";
  $sql=$con->prepare($sql);

  $sql->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
  $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
  foreach($location_ids AS $location_id) {
    $sql->bindParam(':location_id', $location_id, PDO::PARAM_INT);
    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();
  }

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode("Update Successful!");
} //END of updateLocationGroupContact

function taskList($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];

  $selectTasksArray=array();
  $taskPageDataArray=array();

  $selectUsersQuery="SELECT id, employeeID, userID, firstName, lastName, CONCAT(firstName, ' ', lastName) AS fullName FROM hootus.User WHERE status != 0 ORDER BY firstName;";
  $selectUsers=$con->prepare($selectUsersQuery);
  $selectUsers->execute();
  $selectUserTasksArray['users']=$selectUsers->fetchAll(PDO::FETCH_ASSOC);


  //tc - task creator, ta is for Task Owner (to is keyword so as alias will not work)
  $selectTasksQuery="SELECT t.id, t.task, DATE(t.dueDate) AS dueDate, t.status, t.comment,
                            tc.id AS tcid, tc.userID AS tcuserID, CONCAT(tc.firstName, ' ', tc.lastName) AS tcfullName, 
                            ta.id AS taid, ta.userID AS tauserID, CONCAT(ta.firstName, ' ', ta.lastName) AS tafullName
                       FROM hootus.todo AS t
                       LEFT JOIN hootus.user AS tc on tc.id=t.creator_id
                       LEFT JOIN hootus.user AS ta on ta.id=t.owner_id
                       WHERE t.status IN ('Open','OnHold') ORDER BY DATE(dueDate);";
  $selectTasks=$con->prepare($selectTasksQuery);
  $selectTasks->execute();
  $selectTasksArray['tasks']=$selectTasks->fetchAll(PDO::FETCH_ASSOC);

  $userPermissionValue = checkPermission($con, $loggedUser, $loggedUserRole, "ToDo");
  $taskPageDataArray['userPermissionValue']=$userPermissionValue["ToDo"];

  $taskPageDataArray=array_merge($selectTasksArray,$selectUserTasksArray);
  //var_dump($firstPageDataArray);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($taskPageDataArray);
} //END OF taskList


function insertORupdateTask($data,$con) {
  $loggedUser=$data['dbData']['loggedUser'];
  $id=$data['dbData']['id'];
  $task=$data['dbData']['task'];
  $tcid=$data['dbData']['tcid'];
  $taid=$data['dbData']['taid'];
  $dueDate=$data['dbData']['dueDate'];
  $status=$data['dbData']['status'];
  $comment=$data['dbData']['comment'];
  $todo=$data['dbData']['todo'];
  if($todo === "insert") {
    $sql="INSERT INTO hootus.todo(task,creator_id,owner_id,dueDate,comment) VALUES (:task, :tcid, :taid, :dueDate,:comment)";
    $sql=$con->prepare($sql);

    $sql->bindParam(':task', $task, PDO::PARAM_STR);
    $sql->bindParam(':tcid', $tcid, PDO::PARAM_INT);
    $sql->bindParam(':taid', $taid, PDO::PARAM_INT);
    $sql->bindParam(':dueDate', $dueDate, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);

    $con->beginTransaction(); 
    $sql->execute();
    $whereid=$con->lastInsertId();
    $con->commit();
  } else {
    $sql="UPDATE hootus.todo SET task=:task, creator_id=:tcid, owner_id=:taid, dueDate=:dueDate, status=:status, comment=:comment WHERE id=:id;";
    $sql=$con->prepare($sql);

    $sql->bindParam(':task', $task, PDO::PARAM_STR);
    $sql->bindParam(':tcid', $tcid, PDO::PARAM_INT);
    $sql->bindParam(':taid', $taid, PDO::PARAM_INT);
    $sql->bindParam(':dueDate', $dueDate, PDO::PARAM_STR);
    $sql->bindParam(':status', $status, PDO::PARAM_STR);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':id', $id, PDO::PARAM_INT);

    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();
    $whereid=$id;
  }
  $selectTasksQuery="SELECT t.id, t.task, DATE(t.dueDate) AS dueDate, t.created, t.updated, t.status,t.comment,
                            tc.id AS tcid, CONCAT(tc.firstName, ' ', tc.lastName) AS tcfullName, 
                            ta.id AS taid, CONCAT(ta.firstName, ' ', ta.lastName) AS tafullName
                     FROM hootus.todo AS t
                     LEFT JOIN hootus.user AS tc on tc.id=t.creator_id
                     LEFT JOIN hootus.user AS ta on ta.id=t.owner_id
                     WHERE t.id=:whereid;";
  $selectTasks=$con->prepare($selectTasksQuery);
  $selectTasks->bindParam(':whereid', $whereid, PDO::PARAM_INT);
  $selectTasks->execute();
  $selectTasksArray['tasks']=$selectTasks->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectTasksArray);
} //END of insertORupdateTask

function contactList($data,$con) { 
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];
  $contactPageDataArray=array();
  //DATE_FORMAT(dueDate,'%d-%m-%Y') AS dueDate

  //check the user permission while loading the page itself
  // so that Add and Edit buttons can be disabled
  //"User" passed is entity field value in permission; for userList it is User
  // for Locations, it will be Location 
  $userPermissionValue = checkPermission($con, $loggedUser, $loggedUserRole, "Contacts");

  $selectContactsQuery="SELECT r.id rid, r.firstName, r.middleName, r.lastName, r.userID, r.email, r.gender,
                                r.primaryContact, r.alternateContact, 
                                IF(r.status=1,'Active','Inactive') AS status,
                                IF(r.middleName != NULL, CONCAT(r.firstName, ' ', r.middleName, '. ', r.lastName), CONCAT(r.firstName, ' ', r.lastName)) AS fullName, 
                                r.comment,z.zone,l.locationID
                          FROM hootus.contact AS r
                          LEFT JOIN hootus.locationcontact AS lc on lc.contact_id=r.id
                          LEFT JOIN hootus.location AS l on l.id=lc.location_id
                          LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                          WHERE r.status != 0 ORDER BY r.firstName;";
  $selectContacts=$con->prepare($selectContactsQuery);
  $selectContacts->execute();
  $contactPageDataArray['contacts']=$selectContacts->fetchAll(PDO::FETCH_ASSOC);
  $contactPageDataArray['userPermissionValue']=$userPermissionValue["Contacts"];

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($contactPageDataArray);
} //END OF contactList

function insertORupdateContact($data,$con) {
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];

  $id=$data['dbData']['id'];
  $firstName=$data['dbData']['firstName'];
  $middleName=$data['dbData']['middleName'];
  $lastName=$data['dbData']['lastName'];
  $email=$data['dbData']['email'];
  $gender=$data['dbData']['gender'];
  //$password=$data['dbData']['password'];
  $primaryContact=$data['dbData']['primaryContact'];
  $alternateContact=$data['dbData']['alternateContact'];
  $comment=$data['dbData']['comment'];
  $status_id=$data['dbData']['status_id'];

  $options = [
    'cost' => 12, // default is 10
  ];
  //$password=password_hash($data['dbData']['password'], PASSWORD_BCRYPT, $options);
  //next line is required when contact creates their own password
  
  $todo=$data['dbData']['todo'];
  //$hashed_password=password_hash($password, PASSWORD_BCRYPT);

  if($todo === "insert") {
    $sql="INSERT INTO hootus.contact(firstName,middleName,lastName,email,gender,
                                       primaryContact,alternateContact,status,comment) 
          VALUES (:firstName, :middleName, :lastName, :email, :gender, :primaryContact, :alternateContact,:status_id,:comment)";
    $sql=$con->prepare($sql);

    $sql->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $sql->bindParam(':middleName', $middleName, PDO::PARAM_STR);
    $sql->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->bindParam(':gender', $gender, PDO::PARAM_STR);
    //$sql->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $sql->bindParam(':primaryContact', $primaryContact, PDO::PARAM_STR);
    $sql->bindParam(':alternateContact', $alternateContact, PDO::PARAM_STR);
    $sql->bindParam(':status_id', $status_id, PDO::PARAM_INT);
    $sql->bindParam(':comment', $comment, PDO::PARAM_STR);

    $con->beginTransaction(); 
    $sql->execute();
    $whereid=$con->lastInsertId();
    $con->commit();
  } else {
      $sql="UPDATE hootus.contact SET firstName=:firstName, middleName=:middleName, 
                                   lastName=:lastName, email=:email, gender=:gender,
                                   primaryContact=:primaryContact, alternateContact=:alternateContact, 
                                   status=:status_id, comment=:comment
            WHERE id=:id;";
      $sql=$con->prepare($sql);

      
      $sql->bindParam(':firstName', $firstName, PDO::PARAM_STR);
      $sql->bindParam(':middleName', $middleName, PDO::PARAM_STR);
      $sql->bindParam(':lastName', $lastName, PDO::PARAM_STR);
      $sql->bindParam(':email', $email, PDO::PARAM_STR);
      $sql->bindParam(':gender', $gender, PDO::PARAM_STR);
      $sql->bindParam(':primaryContact', $primaryContact, PDO::PARAM_STR);
      $sql->bindParam(':alternateContact', $alternateContact, PDO::PARAM_STR);
      $sql->bindParam(':status_id', $status_id, PDO::PARAM_INT);
      $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
      $sql->bindParam(':id', $id, PDO::PARAM_INT);

      $con->beginTransaction(); 
      $sql->execute();
      $con->commit();
      $whereid=$id;
  }
  $selectContactsQuery="SELECT r.id rid, r.firstName, r.middleName, r.lastName, r.email, r.gender,
                                r.primaryContact, r.alternateContact, IF(r.status=1,'Active','Inactive') AS status, 
                                IFNULL(z.zone,'NA') AS zone, IFNULL(l.locationID,'NA') AS locationID, r.comment
                          FROM hootus.contact AS r
                          LEFT JOIN hootus.locationcontact AS lc on lc.contact_id=r.id
                          LEFT JOIN hootus.location AS l on l.id=lc.location_id
                          LEFT JOIN hootus.zone AS z on z.id=l.zone_id
                          WHERE r.id=:whereid ORDER BY r.firstName;";
  $selectContacts=$con->prepare($selectContactsQuery);
  $selectContacts->bindParam(':whereid', $whereid, PDO::PARAM_INT);
  $selectContacts->execute();
  $contactPageDataArray['contacts']=$selectContacts->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($contactPageDataArray);
} //END of insertORupdateContact


function userList($data,$con) { 
  //$loggedUser=strtoupper($data['dbData']['loggedUser']);
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];
  $userPageDataArray=array();
  //DATE_FORMAT(dueDate,'%d-%m-%Y') AS dueDate

  //check the user permission while loading the page itself
  // so that Add and Edit buttons can be disabled
  //"User" passed is entity field value in permission; for userList it is User
  // for Locations, it will be Location 
  $userPermissionValue = checkPermission($con, $loggedUser, $loggedUserRole, "Users");

  $selectUsersQuery="SELECT u.id id, u.employeeID, u.firstName, u.middleName, u.lastName, u.userID, u.email, u.title_id, t.title,
                            u.primaryContact, u.alternateContact, u.manager_id, u.usertype_id, u.role_id, 
                            IF(u.status=1,'Active',IF(u.status=0,'Inactive','Yet2Login')) AS status,
                            IF(u.middleName != NULL, CONCAT(u.firstName, ' ', u.middleName, '. ', u.lastName), CONCAT(u.firstName, ' ', u.lastName)) AS fullName,
                            m.employeeID AS mEmployeeID, IF(m.middleName != NULL, CONCAT(m.firstName, ' ', m.middleName, '. ', m.lastName), CONCAT(m.firstName, ' ', m.lastName)) AS Manager, 
                            ut.userType, r.role
                    FROM hootus.user AS u
                    LEFT JOIN hootus.user AS m on m.id=u.manager_id
                    LEFT JOIN hootus.title AS t on t.id=u.title_id
                    LEFT JOIN hootus.usertype AS ut on ut.id=u.usertype_id
                    LEFT JOIN hootus.role AS r on r.id=u.role_id
                    WHERE u.status != 0 AND u.firstName != 'GOD' AND u.firstName != 'Admin' ORDER BY u.firstName;";
                    //AND u.firstName != 'GOD' AND u.firstName != 'Admin'
  $selectUsers=$con->prepare($selectUsersQuery);
  $selectUsers->execute();
  $userPageDataArray['users']=$selectUsers->fetchAll(PDO::FETCH_ASSOC);

  $userPageDataArray['userPermissionValue']=$userPermissionValue["Users"];

  $selectTitlesQuery="SELECT id, title FROM hootus.title ORDER BY title;";
  $selectTitles=$con->prepare($selectTitlesQuery);
  $selectTitles->execute();
  $userPageDataArray['titles']=$selectTitles->fetchAll(PDO::FETCH_ASSOC);

  $selectManagersQuery="SELECT m.id, m.employeeID, 
                               IF(m.middleName != NULL, CONCAT(m.firstName, ' ', m.middleName, '. ', m.lastName), CONCAT(m.firstName, ' ', m.lastName)) AS Manager 
                        FROM hootus.user AS m 
                        WHERE m.title_id IN(3,4,6)
                        ORDER BY m.firstName,m.lastName;";
  $selectManagers=$con->prepare($selectManagersQuery);
  $selectManagers->execute();
  $userPageDataArray['managers']=$selectManagers->fetchAll(PDO::FETCH_ASSOC);

  $selectUserTypeQuery="SELECT id, userType FROM hootus.usertype ORDER BY userType;";
  $selectUserTypes=$con->prepare($selectUserTypeQuery);
  $selectUserTypes->execute();
  $userPageDataArray['usertypes']=$selectUserTypes->fetchAll(PDO::FETCH_ASSOC);

  $selectRolesQuery="SELECT id, role FROM hootus.role  WHERE  role!='SuperAdmin' ORDER BY role;"; //WHERE  role!='SuperAdmin'
  $selectRoles=$con->prepare($selectRolesQuery);
  $selectRoles->execute();
  $userPageDataArray['roles']=$selectRoles->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($userPageDataArray);
} //END OF userList

function insertORupdateUser($data,$con) {
  $loggedUser=$data['dbData']['loggedUser'];

  $id=$data['dbData']['id'];
  $employeeID=$data['dbData']['employeeID'];
  $firstName=$data['dbData']['firstName'];
  $middleName=$data['dbData']['middleName'];
  $lastName=$data['dbData']['lastName'];
  $userID=$data['dbData']['userID'];
  $email=$data['dbData']['email'];
  $title_id=$data['dbData']['title_id'];
  //$password=$data['dbData']['password'];
  $primaryContact=$data['dbData']['primaryContact'];
  $alternateContact=$data['dbData']['alternateContact'];
  $manager_id=$data['dbData']['manager_id'];
  $usertype_id=$data['dbData']['usertype_id'];
  $role_id=$data['dbData']['role_id'];
  $status_id=$data['dbData']['status_id'];
  $password = $data['dbData']['password'];

  $options = [
    'cost' => 12, // default is 10
  ];
  //$password=password_hash($data['dbData']['password'], PASSWORD_BCRYPT, $options);
  //next line is required when user creates their own password
  
  $todo=$data['dbData']['todo'];
  $hashed_password=password_hash($password, PASSWORD_BCRYPT);

  if($todo === "insert") {
    $sql="INSERT INTO hootus.user(employeeID,firstName,middleName,lastName,userID,email,title_id,
                                  password,primaryContact,alternateContact,manager_id,usertype_id,role_id,status) 
          VALUES (:employeeID, :firstName, :middleName, :lastName, :userID, :email, :title_id, :password, :primaryContact, :alternateContact, :manager_id, :usertype_id, :role_id, :status_id)";
    $sql=$con->prepare($sql);

    $sql->bindParam(':employeeID', $employeeID, PDO::PARAM_STR);
    $sql->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $sql->bindParam(':middleName', $middleName, PDO::PARAM_STR);
    $sql->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $sql->bindParam(':userID', $userID, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->bindParam(':title_id', $title_id, PDO::PARAM_INT);
    $sql->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $sql->bindParam(':primaryContact', $primaryContact, PDO::PARAM_STR);
    $sql->bindParam(':alternateContact', $alternateContact, PDO::PARAM_STR);
    $sql->bindParam(':manager_id', $manager_id, PDO::PARAM_INT);
    $sql->bindParam(':usertype_id', $usertype_id, PDO::PARAM_INT);
    $sql->bindParam(':role_id', $role_id, PDO::PARAM_INT);
    $sql->bindParam(':status_id', $status_id, PDO::PARAM_INT);

    $con->beginTransaction(); 
    $sql->execute();
    $whereid=$con->lastInsertId();
    $con->commit();
  } else {
      if($password != '') {
        $sql="UPDATE hootus.user SET employeeID=:employeeID, firstName=:firstName, middleName=:middleName, 
                                     lastName=:lastName, userID=:userID, email=:email, title_id=:title_id, password=:password,
                                     primaryContact=:primaryContact, alternateContact=:alternateContact, 
                                     manager_id=:manager_id, usertype_id=:usertype_id, role_id=:role_id, status=:status_id
              WHERE id=:id;";
      } else {
        $sql="UPDATE hootus.user SET employeeID=:employeeID, firstName=:firstName, middleName=:middleName, 
                                     lastName=:lastName, userID=:userID, email=:email, title_id=:title_id,
                                     primaryContact=:primaryContact, alternateContact=:alternateContact, 
                                     manager_id=:manager_id, usertype_id=:usertype_id, role_id=:role_id, status=:status_id
              WHERE id=:id;";
      }
    $sql=$con->prepare($sql);

    $sql->bindParam(':employeeID', $employeeID, PDO::PARAM_STR);
    $sql->bindParam(':firstName', $firstName, PDO::PARAM_STR);
    $sql->bindParam(':middleName', $middleName, PDO::PARAM_STR);
    $sql->bindParam(':lastName', $lastName, PDO::PARAM_STR);
    $sql->bindParam(':userID', $userID, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->bindParam(':title_id', $title_id, PDO::PARAM_INT);
    if($password != '') {
      $sql->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    }
    $sql->bindParam(':primaryContact', $primaryContact, PDO::PARAM_STR);
    $sql->bindParam(':alternateContact', $alternateContact, PDO::PARAM_STR);
    $sql->bindParam(':manager_id', $manager_id, PDO::PARAM_INT);
    $sql->bindParam(':usertype_id', $usertype_id, PDO::PARAM_INT);
    $sql->bindParam(':role_id', $role_id, PDO::PARAM_INT);
    $sql->bindParam(':status_id', $status_id, PDO::PARAM_INT);
    $sql->bindParam(':id', $id, PDO::PARAM_INT);

    $con->beginTransaction(); 
    $sql->execute();
    $con->commit();
    $whereid=$id;
  }
  $selectUsersQuery="SELECT u.id id, u.employeeID, u.firstName, u.middleName, u.lastName, u.userID, u.email, u.title_id, t.title,
                            u.primaryContact, u.alternateContact, u.manager_id, u.usertype_id, u.role_id,
                            IF(u.status=1,'Active',IF(u.status=0,'Inactive','Yet2Login')) AS status, 
                            IF(u.middleName != NULL, CONCAT(u.firstName, ' ', u.middleName, '. ', u.lastName), CONCAT(u.firstName, ' ', u.lastName)) AS fullName,  
                            m.id AS mid,IF(m.middleName != NULL, CONCAT(m.firstName, ' ', m.middleName, '. ', m.lastName), CONCAT(m.firstName, ' ', m.lastName)) AS Manager,
                            ut.userType, r.role
                    FROM hootus.user AS u
                    LEFT JOIN hootus.user AS m on m.id=u.manager_id
                    LEFT JOIN hootus.title AS t on t.id=u.title_id
                    LEFT JOIN hootus.usertype AS ut on ut.id=u.usertype_id
                    LEFT JOIN hootus.role AS r on r.id=u.role_id
                    WHERE u.id=:whereid ORDER BY u.firstName;";
  $selectUsers=$con->prepare($selectUsersQuery);
  $selectUsers->bindParam(':whereid', $whereid, PDO::PARAM_INT);
  $selectUsers->execute();
  $userPageDataArray['users']=$selectUsers->fetchAll(PDO::FETCH_ASSOC);

  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($userPageDataArray);
} //END of insertORupdateUser

function getUserPermissions($data, $con) {
  $loggedUser=$data['dbData']['loggedUser'];
  $loggedUserRole=$data['dbData']['loggedUserRole'];
  $selectPermissionsArray=array();

  $userPermissionValue = checkPermission($con, $loggedUser, $loggedUserRole, "All");
  $selectPermissionsArray["userPermissionValue"] = $userPermissionValue;
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($selectPermissionsArray);
}

function checkPermission($con, $loggedUser, $loggedUserRole, $entity) { 
  //CRUD 1111 (15) all permissions;
  //CRUD 1110 (14) can not Delete;  
  //CRUD 0110 (6) Update and Read; 
  //CRUD 0100 (4) Read only; 
  //CRUD 0000 (0) Can not even read;
  if($entity === "All") {
    $entityWhereClause = "";
  } else {
    $entityWhereClause = "AND s1.entity = '".$entity."'";
  }
  $selectLoggedUserQuery="SELECT entity,SUM(entityPermission) AS entityPermission  FROM (SELECT entity, 
                                CASE
                                WHEN SUM(s.hasCreatePermission) = 1 AND SUM(s.hasDeletePermission) = 1 THEN 15
                                    WHEN SUM(s.hasCreatePermission) = 1 AND SUM(s.hasDeletePermission) = 0 THEN 14
                                    WHEN SUM(s.hasCreatePermission) = 0 AND SUM(s.hasUpdatepermission) = 1 AND SUM(s.hasDeletePermission) = 0 THEN 6
                                    WHEN SUM(s.hasCreatePermission) = 0 AND SUM(s.hasUpdatepermission) = 0 AND SUM(s.hasReadpermission) = 1 THEN 4
                                    ELSE 0 
                                    END AS entityPermission
                            FROM 
                            (SELECT p.entity, u.id, 
                                   IF(permission='C',1,0) AS hasCreatePermission,
                                   IF(permission='R',1,0) AS hasReadpermission,
                                   IF(permission='U',1,0) AS hasUpdatepermission,
                                   IF(permission='D',1,0) AS hasDeletePermission   
                              FROM user AS u
                              LEFT JOIN rolepermission AS rp ON u.role_id = rp.role_id
                              LEFT JOIN permission AS p ON p.id = rp.permission_id
                              WHERE u.userID = '".$loggedUser."' AND p.permission IN('C','R','U','D')
                              UNION ALL
                              SELECT DISTINCT entity, 0 AS uid, 0 AS hasCreatePermission, 0 AS hasReadpermission, 0 AS hasUpdatepermission, 0 AS hasDeletePermission  
                              FROM permission) 
                            AS s
                            GROUP BY s.id, s.entity) AS s1 WHERE 1=1 ".$entityWhereClause." GROUP BY s1.entity;";
  $selectPermission=$con->prepare($selectLoggedUserQuery);

  $selectPermission->execute();
  $myPermissions = $selectPermission->fetchAll(PDO::FETCH_KEY_PAIR);
  return $myPermissions;
} //END of checkPermission

function permissionTblsUpdate($con, $entities, $permissions, $role_id) {
    $sql="INSERT INTO hootus.permission(entity, permission) 
          VALUES (:entity, :permission)
          ON DUPLICATE KEY UPDATE entity=:entity, updated=CURRENT_TIMESTAMP;";
    $sql=$con->prepare($sql);

    foreach($entities AS $entity) {
      $sql->bindParam(':entity', $entity, PDO::PARAM_STR);
      foreach($permissions AS $permission) {
        $sql->bindParam(':permission', $permission, PDO::PARAM_STR);
        $con->beginTransaction(); 
        $sql->execute();
        $con->commit();
      }
    }

    $entities_string = implode(',', $entities); // WITHOUT WHITESPACES BEFORE AND AFTER THE COMMA
    $permissions_string = implode(',', $permissions); // WITHOUT WHITESPACES BEFORE AND AFTER THE COMMA
    $stmt = "SELECT id FROM permission WHERE FIND_IN_SET(entity, :entities) AND FIND_IN_SET(permission, :permissions);";
    $stmt=$con->prepare($stmt);
    $stmt->bindParam(':entities', $entities_string, PDO::PARAM_STR);
    $stmt->bindParam(':permissions', $permissions_string, PDO::PARAM_STR);
    $stmt->execute();
    /* Fetch all of the values of the first column */
    $permission_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $sql="INSERT INTO hootus.rolepermission(role_id, permission_id) 
          VALUES (:role_id,:permission_id)
          ON DUPLICATE KEY UPDATE role_id=:role_id";
    $sql=$con->prepare($sql);

    foreach($permission_id AS $value) {
      $sql->bindParam(':role_id', $role_id, PDO::PARAM_INT);
      $sql->bindParam(':permission_id',$value, PDO::PARAM_INT);

      $con->beginTransaction(); 
      $sql->execute();
      $con->commit();
    }
    
  } //END of permissionTblsUpdate

  function rolepermissionTblDelete($con, $entities, $permissions, $role_id) {
    $entities_string = implode(',', $entities); // WITHOUT WHITESPACES BEFORE AND AFTER THE COMMA
    $permissions_string = implode(',', $permissions); // WITHOUT WHITESPACES BEFORE AND AFTER THE COMMA

    $stmt = "SELECT id FROM permission WHERE FIND_IN_SET(entity, :entities) AND FIND_IN_SET(permission, :permissions);";
    $stmt=$con->prepare($stmt);
    $stmt->bindParam(':entities', $entities_string, PDO::PARAM_STR);
    $stmt->bindParam(':permissions', $permissions_string, PDO::PARAM_STR);
    $stmt->execute();
    $permission_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
   
    $sql="DELETE FROM hootus.rolepermission WHERE role_id=:role_id AND permission_id=:permission_id;";
    $sql=$con->prepare($sql);

    foreach($permission_id AS $value) {
      $sql->bindParam(':role_id', $role_id, PDO::PARAM_INT);
      $sql->bindParam(':permission_id', $value, PDO::PARAM_INT);

      $con->beginTransaction(); 
      $sql->execute();
      $con->commit();
    }
  } //END of rolepermissionTblDelete

?>

