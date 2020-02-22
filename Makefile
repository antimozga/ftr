upload:
	rsync -r --exclude Makefile --exclude .git --exclude LICENSE --exclude README.md . root@vtomske.net:/var/www/html/
