<?
	include('functions.php');
	require 'imageupload.php';
	sessionRegen(true);
	//A bunch of stuff necessary for transparency in PNGs for some reason.
	//Fuck if I know what's going on here; I just copypasta'd it from someplace and it worked.
	function pngCanvas($width,$height){
		$canvas = imagecreatetruecolor($width, $height); //Create the canvas
		imagealphablending($canvas, false); //Enable alpha...blending...I guess...
		imagesavealpha($canvas,true); //What?
		$transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127); //So this is the color of transparency?
		imagefilledrectangle($canvas, 0, 0, $nWidth, $nHeight, $transparent); //Oh, who cares?
		return $canvas;
	}
	
	//Upload the image
	if (!$_POST["op"]){
		$dir = 'upload/temp/'.getLoggedInUser().'/'; //Create a temporary folder
		if(!is_dir($dir)) mkdir($dir,0755);
		$image = new ImageUploader(4096000, 9999, 9999, $dir); //Max size 4MB, unlimited height and width

		$image->setImage('image_file'); //Name of the input image field name

		if(!$image->checkSize()) //Check image size
			$errors[] = "File size over 4MB";

		if(!$image->checkExt()) //Check image extension
			$errors[] = "File ext is not supported";

		if(!isset($errors)){ //If everything's good
		
			//Set a random image ID. Make sure it doesn't exist.
			while(1){	
				$imgid = rand(123456789,999999999);
				if(!file_exists($dir.$imgid.$image->getExt())) break;
			}
			
			$image->setImageName($imgid); //Set image name
			$image->deleteExisting();
			$image->upload();
			list($width, $height, $type, $attr) = getimagesize(theRoot()."/".$dir.$imgid.$image->getExt());
			echo("<div class='row crop-modal'><div class='span2 avatar-crop'><img id='crop-me' src='".theRoot()."/".$dir.$imgid.$image->getExt()."'></div><div class='span3'><h3><i class='icon-arrow-left' style='top:5px'></i> Click and drag to crop</h3><div id='crop-preview' data-height='".$height."' data-width='".$width."'><img src='".theRoot()."/".$dir.$imgid.$image->getExt()."'></div><button class='submit-button btn btn-large btn-success' id='crop-button'><i class='icon-white icon-resize-small'></i> Crop</button></div></div>");
		}
		else{
			echo "<div class='row avatar-crop'><h2>Error</h2><br>";
			print_r($errors);
			echo "</div>";
		}
	}
	
	//Crop the image
	else if ($_POST["op"] == "crop"){
		// Original image
		$filename = $_POST['image'];
		$uid = $_POST['uid'];
		if (!editAuth($uid)) return false;
		$savepath = avatarPath($uid);
		$ext = strrchr($filename, '.');

		// Get dimensions of the original image
		list($current_width, $current_height) = getimagesize($filename);

		// Top left corner where crop begins
		$left = $_POST["x1"];
		$top = $_POST["y1"];
		
		// Final size of the image
		$crop_width = $_POST["x2"] - $left;
		$crop_height = $crop_width;
		
		//Make sure there's enough memory to handle big images
		ini_set('memory_limit', '96M');
		
		//Create a canvas for the original and one for each avatar size
		$canvas = pngCanvas($crop_width, $crop_height);
		$canvas250 = pngCanvas(250,250);
		$canvas72 = pngCanvas(72,72);
		$canvas48 = pngCanvas(48,48);
		$canvas32 = pngCanvas(32,32);
		
		//Check the file extension
		if (($ext == ".jpg") || ($ext == ".jpeg")) $current_image = imagecreatefromjpeg($filename);
		else if ($ext == ".png") $current_image = imagecreatefrompng($filename);
		else if ($ext == ".gif") $current_image = imagecreatefromgif($filename);
		
		//Crop the image to $canvas, then resample it three times
		imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
		imagecopyresampled($canvas250,$canvas,0,0,0,0,250,250,$crop_width,$crop_height);
		imagecopyresampled($canvas72,$canvas,0,0,0,0,72,72,$crop_width,$crop_height);
		imagecopyresampled($canvas48,$canvas,0,0,0,0,48,48,$crop_width,$crop_height);
		imagecopyresampled($canvas32,$canvas,0,0,0,0,32,32,$crop_width,$crop_height);
		
		//Save the three images as temporary, and set the canvasses on FIRE!!!
		imagepng($canvas250, $savepath."250temp.png", 9);
		imagepng($canvas72, $savepath."72temp.png", 9);
		imagepng($canvas48, $savepath."48temp.png", 9);
		imagepng($canvas32, $savepath."32temp.png", 9);
		imagedestroy($canvas);
		imagedestroy($canvas32);
		imagedestroy($canvas48);
		imagedestroy($canvas72);
		imagedestroy($canvas250);
		
		//Empty the temp folder
		destroy('upload/temp/'.getLoggedInUser().'/');
	}
	
	//Clear the avatar and replace it with the default
	else if ($_POST['op'] == "clear"){
		$uid = $_POST['uid'];
		if (!editAuth($uid)) return false;
		$savepath = avatarPath($uid);
		$default = avatarPath("default");
		//Set the default avatar as a temporary image
		copy($default."250.png",$savepath."250temp.png");
		copy($default."72.png",$savepath."72temp.png");
		copy($default."48.png",$savepath."48temp.png");
		copy($default."32.png",$savepath."32temp.png");
	}
	
	//Commit the temporary image
	else if ($_POST['op'] == "save"){
		$uid = $_POST['uid'];
		if (!editAuth($uid)) return false;
		$savepath = avatarPath($uid);
		//Replace the old avatar with the new one
		copy($savepath."250temp.png",$savepath."250.png");
		copy($savepath."72temp.png",$savepath."72.png");
		copy($savepath."48temp.png",$savepath."48.png");
		copy($savepath."32temp.png",$savepath."32.png");
		//Brutally murder the temporary avatar files
		unlink($savepath."250temp.png");
		unlink($savepath."72temp.png");
		unlink($savepath."48temp.png");
		unlink($savepath."32temp.png");
	}

?>