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
	
	$posts = getAllPosts();
	$postCount = sizeof($posts);
	$items = "";
	$i = 0;
	for ($i = $postCount; $i > $postCount - setting("rsslimit"); $i--){
		$event = null;
		$body = null;
		$author = null;
		$post = null;
		$pid = $i;
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
			if (strlen(strip_tags($body)) <= 70) $titletext = ": ".strip_tags($body);
			else $titletext = ": ".substr(strip_tags($body), 0, 70)."...";
			$titletext = strtr($titletext, array("\n" => " "));
			if ($event != null) {
				$titletext = " announced an event: ".$event->name;
				$body = $eventHTML.$body;
			}
	   
			$title = userSetting($author,"name").htmlspecialchars($titletext);
	   		$title = str_replace(array('&', '<'), array('&#x26;', '&#x3C;'), $title);
			
			$body = htmlspecialchars($body);
			
			
			$items .= "\t<item>\n";
			$items .= "\t\t<title>".$title."</title>\n";
			$items .= "\t\t<link>".theRoot()."/post/".$pid."</link>\n";
			$items .= "\t\t<guid>".theRoot()."/post/".$pid."</guid>\n";
			$items .= "\t\t<description>".$body."</description>\n";
			$items .= "\t</item>\n";
			
		}  
	}
	
	$feed = $header.$items.$footer;
	
	return $feed;	
}

function updateFeed(){
	$feed = createFeed();
	postThing(HOME_ROOT()."feed.rss",$feed);
}

function createUserMap(){
	
	$header = "<?xml version='1.0' encoding='UTF-8' ?>\n";
	$header .= "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>\n";
	$header .= "\n<channel>\n";
	$header .= "\t<title>".setting('sitename')." Users</title>\n";
	$header .= "\t<link>".setting('systemroot')."/members</link>\n";
	$header .= "\t<atom:link href='".setting('systemroot')."/users.rss' rel='self' type='application/rss+xml' />";
	$header .= "\t<description>".setting('meta-description')."</description>\n";
	
	$footer = "</channel>\n\n</rss>";
	
	$users = theUsers();
	$urls = "";
	foreach ($users as $key=>$val){
		$myUrl = userSetting($val,"url");
		$urls .= "<item>\n";
		$urls .= "\t<title>".userSetting($val,"name")."</title>\n";
		$urls .= "\t<link>".theRoot()."/".$myUrl."/</link>\n";
		$urls .= "\t<guid>".theRoot()."/".$myUrl."/</guid>\n";
		$urls .= "\t<description>".userSetting($val,"name")."'s profile page</description>\n";
		$urls .= "</item>\n";
	}
	
	return $header.$urls.$footer;
}

function updateSiteMap(){
	$header = "<?xml version='1.0' encoding='UTF-8'?>\n<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd'>";
	$footer = "</urlset>";
	
	$urls = "<url>\n";
	$urls .= "\t<loc>".theRoot()."/</loc>\n";
	$urls .= "\t<changefreq>always</changefreq>\n";
	$urls .= "\t<priority>1.0</priority>\n";
	$urls .= "</url>";
	
	$urls .= "<url>\n";
	$urls .= "\t<loc>".theRoot()."/members</loc>\n";
	$urls .= "\t<changefreq>never</changefreq>\n";
	$urls .= "\t<priority>0.8</priority>\n";
	$urls .= "</url>\n";
	
	$urls .= "<url>\n";
	$urls .= "\t<loc>".theRoot()."/register</loc>\n";
	$urls .= "\t<changefreq>never</changefreq>\n";
	$urls .= "\t<priority>0.8</priority>\n";
	$urls .= "</url>";
	
	$urls .= "<url>\n";
	$urls .= "\t<loc>".theRoot()."/login</loc>\n";
	$urls .= "\t<changefreq>never</changefreq>\n";
	$urls .= "\t<priority>0.8</priority>\n";
	$urls .= "</url>\n";
	
	$sitemap = $header.$urls.$footer;
	
	postThing(HOME_ROOT()."sitemap.xml",$sitemap);
	
	postThing(HOME_ROOT()."users.rss",createUserMap());
}

?>