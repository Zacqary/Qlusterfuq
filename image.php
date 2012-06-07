<?php
	include('functions.php');
	require 'imageupload.php';
	$user = $_POST['user'];
	if (editAuth($user)) {
		$image = new ImageUploader(4096000, 9999, 9999, 'upload/'); //Max size 4MB, unlimited height and width

		$image->setImage('image_file'); //Name of the input image field name

		if(!$image->checkSize()) //Check image size
			$errors[] = "File size over 4MB";

		if(!$image->checkExt()) //Check image extension
			$errors[] = "File ext is not supported";

		if(!isset($errors)){ //If everything's good
			
			//Set a random image ID. Make sure it doesn't exist.
			while(1){	
				$imgid = rand(123456789,999999999);
				if(!file_exists('upload/'.$imgid.$image->getExt())) break;
			}
			
			
			$image->setImageName($imgid); //Set image name
			$image->deleteExisting();
			$image->upload();
			
			$description = $_POST['description'];
			if ($description == "Image description") $description = ""; //If the user didn't set the description, make it blank
			$body = "<span class='image-share'>[![](".theRoot()."/upload/".$imgid.$image->getExt().")](".theRoot()."/upload/".$imgid.$image->getExt().")</span>".$description;
			$time = time();
			$pid = postPost(0,$user,$time,$body);
			showPost($pid);
			notifyNewPost($pid);
		}
		else{
			echo "<h2>Error</h2><br>";
			print_r($errors);
		}
	}
?>