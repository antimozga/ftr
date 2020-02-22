upload:
	rsync -r --exclude Makefile --exclude .git --exclude LICENSE --exclude README.md --exclude CREDITS . root@vtomske.net:/var/www/html/
