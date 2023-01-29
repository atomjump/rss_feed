<img src="https://atomjump.com/images/logo80.png">

__WARNING: this project has now moved to https://src.atomjump.com/atomjump/rss_feed.git__

# rss_feed
A plugin to reads an RSS feed live into an AtomJump Messaging Server forum.



## Requirements

AtomJump Messaging Server >= 0.7.0


## Installation

Find the server at https://src.atomjump.com/atomjump/loop-server. Download and install.

Download the .zip file or git clone the repository into the directory messaging-server/plugins/rss_feed

Put into your crontab file:

sudo crontab -e  
	*/5 * * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 5  
    0 * * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 60  
	0 0 * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 1440  


Copy config/configORIGINAL.json to config/config.json

Edit the config file to match your own RSS feeds.


## Future improvements

Images to be included.

