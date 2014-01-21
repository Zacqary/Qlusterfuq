<?php
require('data.php');

$base = new PDO('sqlite:db.sqlite');

$query =  "CREATE TABLE Posts(
      ID INTEGER NOT NULL PRIMARY KEY,
      Time INTEGER,
      Author text,
      Body text,
      HasEvent INTEGER DEFAULT 0,
      EventName text,
      EventDate text,
      EventTime text,
      EventLocation text,
      Followers text,
      Deleted INTEGER DEFAULT 0
      )";
$results = $base->exec($query);

$query =  "CREATE TABLE Comments(
      Post INTEGER NOT NULL,
      ID INTEGER NOT NULL,
      Time INTEGER,
      Author text,
      Body text,
      Deleted INTEGER DEFAULT 0
      )";
$results = $base->exec($query);

$postCount = 1;
while(1){ //Count how many posts there are
  if (!postExists($postCount)) break;
  $postCount++;
}

for($i = 0; $i < $postCount; $i++){
  if (postExists($i)) {
    echo ("Opening post ".$i."<br>");
    $post = openPost($i);
    if (!isDeleted($post)) {
      $author = getAuthor($post);
      $body = getPost($post);
      $time = getRawTimestamp($post);
      $hasEvent = 0;
      if (strpos($body,"{{{∵}}}")){
        $body = strtr($body,array("{{{∵}}}"=>""));
        $event = openEvent($i);
        $hasEvent = 1;
      }
      else $event = null;
      $followersData = getFollowers($i);
      $followers = "";
      if (is_array($followersData)) {
        foreach ($followersData as $key => $val){
          $followers .= $val."\n";
        }
      }
      $query = "INSERT INTO Posts(ID, Time, Author, Body, HasEvent, EventName, EventDate, EventTime, EventLocation, Followers)
      VALUES(:id, :time, :author, :body, :hasEvent, :eventName, :eventDate, :eventTime, :eventLocation, :followers)";
      $query = $base->prepare($query);
      $array = array(
        'id' => $i,
        'time' => $time,
        'author' => $author,
        'body' => $body,
        'hasEvent' => $hasEvent,
        'eventName' => $event->name,
        'eventDate' => $event->date,
        'eventTime' => $event->time,
        'eventLocation' => $event->location,
        'followers' => $followers
      );
      $query->execute($array);


      $commentTotal = 1;
      while(1){ //Count comments
        if (!commentExists($i,$commentTotal)) break; //If we've counted all the comments or if there are none, stop
        $commentTotal++;
      }
      for ($j = 0; $j < $commentTotal; $j++){
        if (commentExists($i, $j)){
          $comment = openComment($i,$j);
          if (!isDeleted($comment)) {
            $author = getAuthor($comment);
            $body = getComment($comment);
            $time = getRawTimestamp($comment);
            $query = "INSERT INTO Comments(Post, ID, Time, Author, Body)
            VALUES(:post, :id, :time, :author, :body)";
            $query = $base->prepare($query);
            $array = array(
              'post' => $i,
              'id' => $j,
              'time' => $time,
              'author' => $author,
              'body' => $body,
            );
            $query->execute($array);
          }
      else {
        $query = "INSERT INTO Comments(Post, ID, Deleted)
            VALUES(".$i.",".$j.",1)";
            $query = $base->query($query);
      }
        }
      }
    }
  else {
    $query = "INSERT INTO Posts(ID, Deleted)
        VALUES(".$i.",1)";
        $query = $base->query($query);
  }
  }
}
  
  $query =  "CREATE TABLE Users(
      ID text,
      Hash text,
      URL text,
      Admin INTEGER DEFAULT 0,
      AsteriskTip INTEGER DEFAULT 1,
      EmailMe INTEGER DEFAULT 15,
      NewEmail text,
      ConfirmToken text,
      ResetToken text,
      SessionToken text
  )";
$results = $base->exec($query);
$users = theUsers();
foreach ($users as $key => $val){
  $array = array(
    "id" => $val,
    "hash" => userSetting($val,'hash'),
    "url" => userSetting($val,'url'),
    "emailme" => userSetting($val,'emailme'),
    "newemail" => userSetting($val,'newemail'),
    "confirmtoken" => userSetting($val,'confirmtoken'),
    "resettoken" => userSetting($val,'resettoken'),
    "sessiontoken" => userSetting($val,'sessiontoken'),
  );
  if (userSetting($val,'admin') == "yes") $array['admin'] = 1;
  if (userSetting($val,'asterisktip') == "yes") $array['asterisktip'] = 1;
  $query = "INSERT INTO Users(ID, Hash, URL, Admin, AsteriskTip, EmailMe, NewEmail, ConfirmToken, ResetToken, SessionToken)
  VALUES(:id, :hash, :url, :admin, :asterisktip, :emailme, :newemail, :confirmtoken, :resettoken, :sessiontoken)";
  $query = $base->prepare($query);
  $query->execute($array);
  
  $query =  "CREATE TABLE profile_".$val."(
        Attr text PRIMARY KEY,
        Data text,
        Private INTEGER DEFAULT 0
        )";
  $results = $base->exec($query);
  
  
  $profiletable = "profile_".$val;


  $query = "INSERT INTO ".$profiletable."(Attr, Data, Private) VALUES(:attr, :data, :private)";
  $query = $base->prepare($query);
  $query->execute(injectSetting('Name', userSetting($val,'name')));
  $query->execute(injectSetting('AltNames', userSetting($val,'altnames')));
  $query->execute(injectSetting('Bio', userSetting($val,'bio')));
  $query->execute(injectSetting('Email', userSetting($val,'email')));
  $query->execute(injectSetting('Birthday', userSetting($val,'birthday')));
  $query->execute(injectSetting('Phone', userSetting($val,'phone')));
  $query->execute(injectSetting('Location', userSetting($val,'location')));
  $query->execute(injectSetting('Website', userSetting($val,'website')));

  $basicInfo = userSetting($val, 'basicinfo', true);
  foreach($basicInfo as $k => $v){
    $query->execute(injectSetting($k, $v));    
  }

  $externalSvcs = userSetting($val, 'externalsvcs', true);
  foreach($externalSvcs as $k => $v){
    $query->execute(injectSetting($k, $v));
  }
  
  $query =  "CREATE TABLE notifications_".$val."(
        Time INTEGER PRIMARY KEY,
        Author text,
        Text text,
        Link text,
        Read INTEGER DEFAULT 0
        )";
  $results = $base->exec($query);
  
  $notetable = "notifications_".$val;
  $check = $base->query("SELECT * FROM ".$notetable);
  $result = $check->fetch(PDO::FETCH_ASSOC);
  echo($notetable."<br>");
  print_r($result);
  echo(sizeof($result)."<br>");
  if(sizeof($result) < 2) {
    echo("adding");
    $nquery = "INSERT INTO ".$notetable."(Time, Author, Text, Link, Read) VALUES(:time, :author, :text, :link, :read)";
    $nquery = $base->prepare($nquery);
    $notes = openNotifications($val);
    foreach($notes as $key => $val){
      $array = injectNotification($key,$val);
      $nquery->execute($array);
  }
}
}

function injectSetting($attr, $data){
  $private = 0;
  if (strpos($data,"{{{∵}}}")) {
    $data = deprivate($data);
    $private = 1;
  }
  $array = array("attr" => $attr, "data" => $data, "private" => $private);
  return $array;
}
  
function injectNotification($time, $note){
  $array = array("time" => $time, "author" => getAuthor($note), "text" => getNotificationText($note), "link" => getNotificationLink($note), "read" => 0);
  if (getNotificationStatus($note) == "read") $array["read"] = 1;
  return $array;
}

?>