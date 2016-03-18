all:
	rsync --progress -r -t -avz --no-perms \
			app bootstrap.php cron.php findfeed.php libs tests \
			sourcelist.csv ubuntu@ec2-54-173-173-156.compute-1.amazonaws.com:scraper/

