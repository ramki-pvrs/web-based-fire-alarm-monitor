SELECT * FROM hootusx.alarmhistory;

SELECT ah.id AS ahid, at.id AS atid, at.alarmType,
                              ac.id AS acid, ac.alarmCause, ah.comment 
                       FROM hootusx.alarmhistory AS ah
                       LEFT JOIN hootusX.alarmtype AS at ON at.id = ah.alarmtype_id
                       LEFT JOIN hootusX.alarmcause AS ac ON ac.id = ah.alarmcause_id
                       WHERE ah.id=1;

SELECT alarmType, SUM(Electrical) AS 'Electrical', SUM('Non-Electrical') AS 'Non-Electrical',
       SUM(Personnel) AS 'Personnel', SUM(Others) AS 'Others'  FROM (SELECT at.alarmType, 
       IF(ac.alarmCause='Electrical',COUNT(ac.id),0) AS 'Electrical',
       IF(ac.alarmCause='Non-Electrical',COUNT(ac.id),0) AS 'Non-Electrical',
       IF(ac.alarmCause='Personnel',COUNT(ac.id),0) AS 'Personnel',
       IF(ac.alarmCause='Others',COUNT(ac.id),0) AS 'Others'
FROM alarmtype AS at                      
LEFT JOIN hootusx.alarmhistory AS ah ON ah.alarmtype_id = at.id
LEFT JOIN hootusX.alarmcause AS ac ON ac.id = ah.alarmcause_id
-- WHERE ah.alarmONTime BETWEEN DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 3 MONTH) AND CURRENT_TIMESTAMP()
GROUP BY at.alarmType, ac.alarmCause) AS s
GROUP BY s.alarmType
ORDER BY FIELD(s.alarmType, 'False','Real','Transient', 'Demo'); 

SELECT at.alarmType, IF(ac.alarmCause, COUNT(ac.alarmCause),0) AS qty 
FROM alarmtype AS at                      
LEFT JOIN hootusx.alarmhistory AS ah ON ah.alarmtype_id = at.id
LEFT JOIN hootusX.alarmcause AS ac ON ac.id = ah.alarmcause_id
WHERE ah.alarmONTime BETWEEN DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 3 MONTH) AND CURRENT_TIMESTAMP()
GROUP BY at.alarmType, ac.alarmCause; 


SELECT IF(rc.rootCause='False',COUNT(rc.id),0) AS 'False',
       IF(rc.rootCause='Electrical',COUNT(rc.id),0) AS 'Electrical',
       IF(rc.rootCause='Non-Electrical',COUNT(rc.id),0) AS 'Non-Electrical',
       IF(rc.rootCause='Personnel',COUNT(rc.id),0) AS 'Personnel',
       IF(rc.rootCause='Others',COUNT(rc.id),0) AS 'Others'
FROM rootcause AS rc                      
LEFT JOIN hootusx.alarmhistory AS ah ON ah.rootcause_id = rc.id
-- WHERE ah.alarmONTime BETWEEN DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 3 MONTH) AND CURRENT_TIMESTAMP()
GROUP BY rc.rootCause;

SELECT rootCause, SUM(count) AS count FROM ((SELECT IFNULL(rc.rootCause,'None') AS rootCause, COUNT(h.id) AS count
FROM hootusx.downhistory AS h                      
LEFT JOIN hootusx.rootcause AS rc ON rc.id = h.rootcause_id
GROUP BY rc.rootCause)
UNION 
(SELECT rootCause, 0 AS count FROM rootcause)) AS s
GROUP BY s.rootCause
ORDER BY FIELD(s.rootCause,'None', 'False','Electrical','Non-Electrical','Personnel','Others');

SELECT rc.rootCause, COUNT(h.id) AS count
FROM hootusx.downhistory AS h                      
LEFT JOIN hootusx.rootcause AS rc ON rc.id = h.rootcause_id
GROUP BY rc.rootCause;


SELECT CASE 
           WHEN rc.rootCause='False' THEN 'False'
           WHEN rc.rootCause='Electrical' THEN 'Electrical'
            WHEN rc.rootCause='Non-Electrical' THEN 'Non-Electrical'
             WHEN rc.rootCause='Personnel' THEN 'Personnel'
              WHEN rc.rootCause='Others' THEN 'Others'
		ELSE 0 
	   END AS rootCause, 
        COUNT(rc.id) AS count
FROM rootcause AS rc                      
LEFT JOIN hootusx.alarmhistory AS ah ON ah.rootcause_id = rc.id
-- WHERE ah.alarmONTime BETWEEN DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 3 MONTH) AND CURRENT_TIMESTAMP()
GROUP BY rc.rootCause;


SELECT IF(rc.rootCause='False',COUNT(rc.id),0) AS 'False',
       IF(rc.rootCause='Electrical',COUNT(rc.id),0) AS 'Electrical',
       IF(rc.rootCause='Non-Electrical',COUNT(rc.id),0) AS 'Non-Electrical',
       IF(rc.rootCause='Personnel',COUNT(rc.id),0) AS 'Personnel',
       IF(rc.rootCause='Others',COUNT(rc.id),0) AS 'Others'
FROM alarmhistory AS ah                      
LEFT JOIN hootusx.rootcause  AS rc ON rc.id = ah.rootcause_id
-- WHERE ah.alarmONTime BETWEEN DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 3 MONTH) AND CURRENT_TIMESTAMP()
GROUP BY rc.rootCause;



SELECT rootCause, SUM(count) AS count FROM (
                                     (SELECT IFNULL(rc.rootCause,'None') AS rootCause, COUNT(h.id) AS count
                                      FROM hootusx.alarmhistory AS h                      
                                      LEFT JOIN hootusx.rootcause AS rc ON rc.id = h.rootcause_id
                                      GROUP BY rc.rootCause)
                                      UNION 
                                      (SELECT rootCause, 0 AS count FROM rootcause)
                                      UNION 
                                      (SELECT 'None' AS rootCause, 0 AS count)) AS s
                        GROUP BY s.rootCause
                        ORDER BY FIELD(s.rootCause,'None', 'False','Electrical','Non-Electrical','Personnel','Others');