<?php
/**
 * @name Image uploader
 * @version 0.7
 * @author Tareq Hasan (tareq1988@gmail.com)
 * @link http://tareq.wedevs.com
 * @example http://tareq.wedevs.com/2009/07/image-upload-validation/
 */
 
 #**********************************************************************
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# ( at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Online: http://www.gnu.org/licenses/gpl.txt

# *****************************************************************

class ImageUploader{
//	private $max_size;
	private $max_height;
	private $max_width;
	private $upload_dir;
	
	private $file_name;
	private $file_size;
	private $file_tmp;
	private $file_type;
	
	private $image_name; //the name we set as uploaded
	
	/**
	 * Constructor, sets the initial conditions
	 *
	 * @param integer $max_size
	 * @param integer $max_width
	 * @param integer $max_height
	 * @param string $upload_dir
	 */
	function __construct($max_size, $max_width, $max_height, $upload_dir)
	{
		$this->max_size = $max_size;
		$this->max_height = $max_height;
		$this->max_width = $max_width;
		$this->upload_dir = $upload_dir;
	}
	
	/**
	 * set the input image name what was used in input field
	 *
	 * @param string $name
	 */
	function setImage($name)
	{
		$this->file_name = $_FILES[$name]['name'];
		$this->file_size = $_FILES[$name]['size'];
		$this->file_tmp = $_FILES[$name]['tmp_name'];
		$this->file_type = $_FILES[$name]['type'];
	}
	
	/**
	 * set the output image name
	 *
	 * @param string/integer $name
	 */
	function setImageName($name)
	{
		$this->image_name = $name;
	}
	
	/**
	 * Delete existing image with same name set via $this->setImageName
	 *
	 */
	function deleteExisting()
	{
		$jpg =  $this->upload_dir.''.$this->image_name.'.jpg';
		if(file_exists($jpg)) unlink($jpg);
		
		$jpeg =  $this->upload_dir.''.$this->image_name.'.jpeg';
		if(file_exists($jpeg)) unlink($jpeg);		
			
		$gif =  $this->upload_dir.''.$this->image_name.'.gif';
		if(file_exists($gif)) unlink($gif); 		
			
		$png =  $this->upload_dir.''.$this->image_name.'.png';
		if(file_exists($png)) unlink($png);		
	}
	
	/**
	 * uploads the image
	 *
	 * @return boolean
	 */
	function upload()
	{
		$ext = strrchr($this->file_name, '.');
		if ($ext == ".jpeg") $ext = '.jpg';
		$name = $this->upload_dir.''.$this->image_name.''.$ext;
		if(!move_uploaded_file($this->file_tmp, $name)){
			echo $_FILES[$this->file_name]['error'];
			return false;
		}
		else
			return true;
	}
	
	/**
	 * Check the input image size with max image size
	 *
	 * @return boolean
	 */
	function checkSize()
	{
		if($this->file_size > ($this->max_size*1024))
			return false;
		else
			return true;
	}
	

	/**
	 * Check the input image height with max image height
	 *
	 * @return boolean
	 */
	function checkHeight()
	{
		$file = getimagesize($this->file_tmp);
		//$height = $file[1];
		
		if($file[1] > $this->max_height)
			return false;
		else
			return true;
	}
	
	/**
	 * Check the input image width with max image height
	 *
	 * @return boolean
	 */
	function checkWidth()
	{
		$file = getimagesize($this->file_tmp);
		//$width = $file[0];
		
		if($file[0] > $this->max_height)
			return false;
		else
			return true;
	}
	
	/**
	 * checks image extension
	 *
	 * @return boolean
	 */
	function checkExt()
	{		
		if (($this->file_type != 'image/jpg') && ($this->file_type != 'image/jpeg') && 
				($this->file_type != 'image/gif') && ($this->file_type != 'image/png'))
			return false;
		else
			return true;		
	}
	function getExt(){
		if ($this->file_type == 'image/jpeg') return '.jpg';
		else if ($this->file_type == 'image/jpg') return '.jpg';
		else if ($this->file_type == 'image/png') return '.png';
		else if ($this->file_type == 'image/gif') return '.gif';
	}
}
?>