<?php require_once("functions.php");

function createFeed(){
	
	$header = "<?xml version='1.0' encoding='UTF-8' ?>\n";
	$header .= "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>\n";
	$header .= "\n<channel>\n";
	$header .= "\t<title>".setting('sitename')."</title>\n";
	$header .= "\t<link>".setting('systemroot')."</link>\n";
	$header .= "\t<atom:link href='".setting('systemroot')."/feed.rss' rel='self' type='application/rss+xml' />";
	$header .= "\t<description>".setting('meta-description')."</description>\n";
	
	$footer = "</channel>\n\n</rss>";
	
	$postCount = 1;
	while(1){ //Count how many posts there are
		if (!postExists($postCount)) break;
		$postCount++;
	}
	$postCount--;

	$items = "";
	$i = 0;
	while ($i < 10){
		$event = null;
		$body = null;
		$author = null;
		$post = null;
		$pid = $postCount - $i;
		if ($pid <= 0) break;
		$post = openPost($pid);
		if (!isDeleted($post)){
			$author = getAuthor($post);
			$body = parseMarkdown(getPost($post));
			if (strpos($body,"{{{∵}}}")){
				$body = strtr($body,array("{{{∵}}}"=>""));
				$event = openEvent($pid);
				$eventHTML = "
					<h3 class='event-title'>".$event->name."</h3>
					<ul class='event-meta'>
					<li>Date: ".$event->date."</li>
					<li>Time: ".$event->time."</li>
					<li>Visit site to see location</li>
					</ul>";
				$eventHTML .= "<br><br>";
			}
			if (strlen(strip_tags($body)) <= 70) $titletext = ": ".$body;
			else $titletext = ": ".substr(strip_tags($body), 0, 70)."...";
			$titletext = strtr($titletext, array("\n" => " "));
			if ($event != null) {
				$titletext = " announced an event: ".$event->name;
				$body = $eventHTML.$body;
			}
	   
			$title = userSetting($author,"name").$titletext;
	   
			$items .= "\t<item>\n";
			$items .= "\t\t<title>".$title."</title>\n";
			$items .= "\t\t<link>".theRoot()."/post/".$pid."</link>\n";
			$items .= "\t\t<guid>".theRoot()."/post/".$pid."</guid>\n";
			$items .= "\t\t<description>".htmlentities($body)."</description>\n";
			$items .= "\t</item>\n";
			
			$i++;
		}  
	}
	
	$feed = $header.$items.$footer;
	
	return $feed;	
}

function updateFeed(){
	$feed = createFeed();
	postThing(HOME_ROOT()."feed.rss",$feed);
}

?>