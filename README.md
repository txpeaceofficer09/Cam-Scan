# Cam-Scan

camscan.php opens the log.txt file and reads all the lines in csv format.  Each line represents a camera
and the information in the file is {IP,NAME,STATUS}.  Status is either a 1 or 0 depending on if the
camera is up (1) or down (0).

I use this script to check port 80 because all of our cameras have a built-in web server.  Then if the
camera changes from up-to-down or down-to-up I get an e-mail about all the cameras that changed status.
This is run in a cron script every 5 minutes.

	EXAMPLE:

		192.168.1.100,Front Door Camera,0
