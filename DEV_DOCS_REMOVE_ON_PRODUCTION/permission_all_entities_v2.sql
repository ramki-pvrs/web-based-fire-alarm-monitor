SELECT entity,entityPermission FROM (SELECT entity, 
                                CASE
                                WHEN SUM(s.hasCreatePermission) = 1 AND SUM(s.hasDeletePermission) = 1 THEN 15
                                    WHEN SUM(s.hasCreatePermission) = 1 AND SUM(s.hasDeletePermission) = 0 THEN 14
                                    WHEN SUM(s.hasCreatePermission) = 0 AND SUM(s.hasUpdatepermission) = 1 AND SUM(s.hasDeletePermission) = 0 THEN 6
                                    WHEN SUM(s.hasCreatePermission) = 0 AND SUM(s.hasUpdatepermission) = 0 AND SUM(s.hasReadpermission) = 1 THEN 4
                                    ELSE 0 
                                    END AS entityPermission
                            FROM 
                            (SELECT u.id, p.entity,
                                   IF(permission='C',1,0) AS hasCreatePermission,
                                   IF(permission='R',1,0) AS hasReadpermission,
                                   IF(permission='U',1,0) AS hasUpdatepermission,
                                   IF(permission='D',1,0) AS hasDeletePermission   
                              FROM user AS u
                              LEFT JOIN rolepermission AS rp ON u.role_id = rp.role_id
                              LEFT JOIN permission AS p ON p.id = rp.permission_id
                              WHERE u.userID = 'vshan' AND p.permission IN('C','R','U','D')) 
                            AS s
                            GROUP BY s.id, s.entity) AS s1 WHERE 1=1 AND s1.entity = 'Location';
                            
                            
SELECT p.entity, u.id, 
   IF(permission='C',1,0) AS hasCreatePermission,
   IF(permission='R',1,0) AS hasReadpermission,
   IF(permission='U',1,0) AS hasUpdatepermission,
   IF(permission='D',1,0) AS hasDeletePermission   
FROM user AS u
LEFT JOIN rolepermission AS rp ON u.role_id = rp.role_id
LEFT JOIN permission AS p ON p.id = rp.permission_id
WHERE u.userID = 'vshan' AND p.permission IN('C','R','U','D')
 UNION ALL
SELECT DISTINCT entity, 0 AS uid, 0 AS hasCreatePermission, 0 AS hasReadpermission, 0 AS hasUpdatepermission, 0 AS hasDeletePermission  
FROM permission;


SELECT entity,SUM(entityPermission) AS entityPermission  FROM (SELECT entity, 
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
                              WHERE u.userID = 'vshan' AND p.permission IN('C','R','U','D')
                              UNION ALL
SELECT DISTINCT entity, 0 AS uid, 0 AS hasCreatePermission, 0 AS hasReadpermission, 0 AS hasUpdatepermission, 0 AS hasDeletePermission  
FROM permission) 
                            AS s
                            GROUP BY s.id, s.entity) AS s1 WHERE 1=1 GROUP BY s1.entity;