What is this project?
	- A web application based on php, mysql
	- Primary purpose is to display fire alarms status of multiple locations 
		- for example from different parts of a large building or a community

Critical Requirements
	- Fire/Smoke detection by LAN/wi-fi connected devices in their installed (device) location
	- Installed device can make a web-service POST http call to update the fire alarm status
		- like curl -d "did=6001&dname=DEVICE_BRAND&sts=1" -X POST http://localhost:80/fire.php
	- Each device from all part of the site is centrally monitored for its current status - whether it's alarm is active or not
		- 'active' meaning there is fire in that device location currently
	- Device will also be sending heartbeat signal and device down status in monitor
	- Monitoring at central location as well as anyone authorised to visit that web page
	- Refresh monitor each 'n' seconds of all devices alarm status
		- configurable based on application installation site
		- alarms_page.php file line : setInterval("my_function();", 5000);

Integration Testing
	- launched in WAMP stack in laptop and tested with
		- direct mysql table update with active alarm status for a specific device id and device location
		- curl command
		- using arduino
		- using firealarm device prototype

Contact
	ramki.pvrs@gmail.com

