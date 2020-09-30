SELECT * FROM hootus.locationcontact;

SELECT * FROM locationcontact
WHERE id IN (SELECT lc.id FROM hootus.locationcontact AS lc
LEFT JOIN contact AS c ON c.id=lc.contact_id
WHERE c.primaryContact='9999999991');


UPDATE locationcontact AS lc
INNER JOIN contact AS c ON c.id = lc.contact_id
INNER JOIN location AS l ON l.id = lc.location_id
SET currentContact = 0
WHERE l.id = 1 AND c.primaryContact = '9999999991';



