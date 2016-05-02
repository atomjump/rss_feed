<img src="https://atomjump.com/images/logo80.png">

# rss_feed
A plugin to reads an RSS feed live into an AtomJump Loop Server forum.



## Requirements

AtomJump Loop Server >= 0.5.0


## Installation

Find the server at https://github.com/atomjump/loop-server. Download and install.

Download the .zip file or git clone the repository into the directory loop-server/plugins/rss_feed

Put into your crontab file:

sudo crontab -e
	*/5 * * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 5  
    0 * * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 60  
	0 0 * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 1440  


Copy config/configORIGINAL.json to config/config.json

Edit the config file to match your own RSS feeds.


## Future improvements

Images to be included.

