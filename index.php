<?php
	//Cron job to add new news RSS feeds every 5 minutes.
	//To install put the following line in after typing 
	//		sudo crontab -e
	//		*/5 * * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 5
	//      0 * * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 60
	//		0 0 * * *	/usr/bin/php /your_server_path/plugins/rss_feed/index.php 1440


	if(!isset($rss_feed_config)) {
        //Get global plugin config - but only once
		$data = file_get_contents (dirname(__FILE__) . "/config/config.json");
        if($data) {
            $rss_feed_config = json_decode($data, true);
            if(!isset($rss_feed_config)) {
                echo "Error: rss_feed config/config.json is not valid JSON.";
                exit(0);
            }
     
        } else {
            echo "Error: Missing config/config.json in rss_feed plugin.";
            exit(0);
     
        }
  
  
    }



    $agent = $rss_feed_config['agent'];
	ini_set("user_agent",$agent);
	$_SERVER['HTTP_USER_AGENT'] = $agent;
	$start_path = $rss_feed_config['serverPath'];

	
	
	$notify = false;
	include_once($start_path . 'config/db_connect.php');	
	
	$define_classes_path = $start_path;     //This flag ensures we have access to the typical classes, before the cls.pluginapi.php is included
	require($start_path . "classes/cls.pluginapi.php");
	
	$api = new cls_plugin_api();
	
	
	if($argc >= 1) {
		$freq = intval($argv[1]);
	} else {
		$freq = 5;
	}
	




	
	//Read the feed file
	$feeds = $rss_feed_config['rssFeeds'];  
	
	echo "Frequency: $freq\n";
	
	foreach($feeds as $feed) {
		
		echo "Checking " . $feed['feed'] . ".. ";
		
		if($freq >= $feed['freq']) {		//Only call them if the freq in minutes of this request
			$feed_xml = simplexml_load_file($feed['feed']);
		
			foreach ($feed_xml->channel->item as $item) {
				$feed_array[] = $item;
		
			}

			$feed_array_out = array_reverse($feed_array);
		
			foreach ($feed_array_out as $item) {
			  $title       = (string) $item->title;
			  $description = (string) $item->description;
			  $link = (string) clean_data($item->link);
			  $guid = (string) clean_data($item->guid);
			  $pubDate = (string) clean_data($item->pubDate);
			  
			  if(isset($item->image->url)) {
			     $image = (string) clean_data($item->image->url);
	          }
	
	
			  	  
	          //Filter the description fort the first image
	          if(isset($feed['images'])&&($feed['images'] == true)) {
	          	  $image = null;
		               preg_match("/img(.*?)src=\"(.*?)\"/i", $description, $first_image);
		               if($first_image[2]) {
		                 	$image = $first_image[2];
		          
	          	  }
		          
	          }
	    	  
	
	
	          if($guid == "") {
	            	$guid = $link;	  
	          }
	          
	          if(substr($guid,0,4) == "http") {
	          	
	          
	          } else {
	          	if(substr($link,0,4) == "http") {
	          	 	$guid = $link;		//Don't include non linked articles
	          	 } else {
	          	 	$guid = "";
	          	 
	          	 }
	          
	          }
			  
			  
			  
			  
			  
			  echo $title . "\n";
			  
			  if($guid != "") {
			  
				  //Check if this item has already been shouted
				  $sql = "SELECT * FROM tbl_feed WHERE var_unique_id = '" . trim($guid) . "'";
				  $result = $api->db_select($sql);
					if($row = mysql_fetch_array($result))
					{
			
						//Already exists - fast skip
					} else {
						//We want to shout this
						$summary_description = summary(strip_tags($description),140);
						if($summary_description != "") {
							$summary_description = " - " . $summary_description;
						
						}
						

						
						if(isset($feed['images'])&&($feed['images'] == true)) {
							if(isset($image)) {
							 if((stristr($image, 'jpg') != false)||
								(stristr($image, 'jpeg') != false)) {
								$summary_description .= ' ' . $image;
							 }
						
							}
						}
						

						$shouted = $title . $summary_description . " " . $guid;		//guid may not be url for some feeds, may need to have link
						$your_name = $feed['name'];
						$whisper_to = "";
						$email = $feed['email'];
						$ip = "92.27.10.17"; //must be something anything
						$forum_name = $feed['aj'];
				
				
				        //Get the forum id
						$forum_info = $api->get_forum_id($forum_name);
						
						 //Send the message
					     $api->new_message($your_name, $shouted, $whisper_to, $email, $ip, $forum_info['forum_id'], false);
												
				
						//Now keep a record of this feed item for easy non duplication	
				        $api->db_insert("tbl_feed", "(var_unique_id, date_when_shouted)",
						                    "('" . trim($guid) ."', 
										'" . date("Y-m-d H:i:s", strtotime($pubDate)) . "')");
				
						sleep(1);
			
					}
				}
			
			  
			  
			}
		}
	}

	session_destroy();  //remove session
	
	
	
	
?>

