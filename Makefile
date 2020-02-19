upload:
	rsync -r --exclude Makefile --exclude .git --exclude LICENSE --exclude README.md . root@51.15.247.125:/var/www/html/
