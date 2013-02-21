<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

# This handles the content file org crap
class content {

	public $uid; // UID of object
	public $filename; // "UID" for this class, the real filename
	public $mime; // Mime type of this image
	public $parentuid; // Parent UID this is assoicated with
	public $type; // Type of object, most likely an image for now
	public $source; // Raw data of the object
	private $valid_types = array('record','thumb','qrcode','ticket'); 

	public function __construct($uid='',$type) { 

		if (!in_array($type,$valid_types)) { 
			Event::error('general','Invalid Content Type Specified');
			return false; 
		} 

		$this->uid = intval($uid); 
		$this->type = $type; 

		$this->{"load_".$type."_data"}($uid); 		

	} // construct

	/** 
	 * load_record_data
	 * This loads a record image from its UID
	 */
	private function load_record_data($uid) { 

		$uid = Dba::escape($uid); 
		$sql = "SELECT * FROM `image` WHERE `uid`='$uid'"; 
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		$this->filename = $row['data']; 
		$this->mime = $row['type']; 
		$this->parentuid = $row['record']; 

		return $db_results; 


	} // load_record_data

	/** 
	 * load_thumb_data
	 * Loads the thumbnail data for the specified image
	 */
	public function load_thumb_data($uid) { 

		$this->load_record_data($uid); 
		$this->filename = $this->filename . '.thumb'; 

		return true; 

	} // load_thumb_data 

	/** 
	 * load_qrcode_data
	 * This loads the qrcode image from the record info
	 * UID is the record uid
	 */
	private function load_qrcode_data($uid) { 

		$uid = Dba::escape($uid); 
		$sql = "SELECT * FROM `media` WHERE `record`='$uid' AND `type`='qrcode'";
		$db_results = Dba::read($sql); 

		$row = Dba::fetch_assoc($db_results); 

		$this->filename = $row['filename'];
		$this->uid	= $row['uid'];
		$this->parentuid = $row['record']; 
		
		return true; 

	} // load_qrcode_data

	// Reads in and returns the source of this file
	public function source() { 

		$data = file_get_contents($this->filename); 
		$this->source = $data; 
		return $data; 	

	} // source

	// This writes out the specified data 
	public static function write($uid,$type,$data='',$mime_type='') { 

		$extension = self::get_extension($mime_type); 
		
		$record = new Record($uid); 

		// First we need to generate a filename /SITE/YYYY/RECORD-MMDDHHMMSS
		switch ($type) { 
			case 'thumb': 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension . '.thumb'); 
				$results = self::write_thumb($data,$filename); 
			break; 
			case 'qrcode':
				// If we passed a filename in
				if (strlen($data)) { 
					$filename = $data; 
				}
				else {
					$filename = self::generate_filename($record->site . '-qrcode-' . $record->catalog_id,'png'); 
				}
				$results = self::write_qrcode($uid,$filename); 
			break; 
			default: 
			case 'record': 
				$filename = self::generate_filename($record->site . '-' . $record->catalog_id,$extension); 
				$results = self::write_record($uid,$data,$filename,$mime_type); 
			break; 
		} 

		return $results; 

	} // write

	/**
	 * write a record image
	 */
	private static function write_record($uid,$data,$filename,$mime_type) {

		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 
		$results = fwrite($handle,$data); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		$filename = Dba::escape($filename); 
		$uid = Dba::escape($uid); 
		$mime_type = Dba::escape($mime_type); 
		$sql = "INSERT INTO `image` (`data`,`record`,`type`) VALUES ('$filename','$uid','$mime_type')"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Event:error('general','Error inserting record of Image into Database'); 
			return false; 
		} 

		return $db_results; 

	} // write_record

	/**
	 * write_thumb
	 * Write out the thumbnail image of the "image" attached to a record
	 */
	private static function write_thumb($data,$filename) { 
		// Put it on the filesystem
		$handle = fopen($filename,'w'); 
		if (!$handle) { 
			Event::error('Content','Unable to write file - Permission denied'); 
			return false; 
		} 
		$results = fwrite($handle,$data); 

		if (!$results) { 
			Event::error('Content','Unable to write Image to disk'); 
			return false; 
		} 

		return $results; 

	} // write_thumb

	/**
	 * write_qrcode
	 * Generate and save a qrcode
	 */
	private static function write_qrcode($uid,$filename) { 

		$qrcode_data = Config::get('web_path') . '/records/view/' . $uid;
		$results = QRcode::png($qrcode_data,$filename,'L','4',2); 

		// Insert a record of this into the media table (why do we have an images table??!@)
		$filename = Dba::escape($filename); 
		$uid = Dba::escape($uid); 
		$type = 'qrcode';

		$sql = "INSERT INTO `media` (`filename`,`type`,`record`) VALUES ('$filename','$type','$uid')"; 
		$db_results = Dba::write($sql); 

		if (!$db_results) { 
			Event::error('Database','Unknown Datbase Error inserting QRCode'); 
			return false; 
		} 

		return $db_results;

		return $results; 
	
	} // write_qrcode

	/**
	 * delete
	 * Deletes some content from the FS
	 */
	public function delete() { 

		$results = $this->{'delete_'.$type}(); 	
		return $results; 

	} // delete

	/**
	 * delete_record
	 * Delete a record
	 */
	private function delete_record() {

		$results = unlink($this->filename); 
		if (!$results) { 
			Event::error('general','Error unable to remove Media file'); 
			return false; 
		} 

		$record_id = Dba::escape($this->uid); 
		$sql = "DELETE FROM `image` WHERE `record`='$record_id'"; 
		$db_results = Dba::write($sql); 	

		return true; 

	} //delete_record

	/**
	 * delete_thumb
	 * Delete the thumbnail of this record
	 */
	public function delete_thumb() { 

		$results = unlink($this->filename . '.thumb'); 
		if (!$results) { 
			Event::error('general','Error unable to remove Media Thumbnail'); 
			return false; 
		} 

		return true; 

	} // delete_thumb

	/**
	 * delete_qrcode
	 * Delete the qrcode for this record
	 */
	public function delete_qrcode() { 

		$results = unlink($this->filename); 
		if (!$results) { 
			Event::error('general','Error unable to remove QRCode'); 
			return false; 
		} 
	
		return true; 

	} // delete_qrcode

	/**
	 * generate_filename
	 * Generates a filename based on the name and extension using the data_root defined
	 * in the config file
	 */
	public static function generate_filename($name,$extension='') { 

		// If we pass an extension prepend a dot
		$extension = $extension ? '.' . $extension : '';

		$filename = self::generate_directory() . '/' . escapeshellcmd($name) . '-' . date("dmHis",time()) . $extension; 

		return $filename; 

	} // genereate_filename

	// Make sure we've got the directories we need
	private static function generate_directory() { 

		$directory =  Config::get('data_root') . '/' . escapeshellcmd(Config::get('site')) . '/' . date("Y",time()); 
	
		if (!is_dir($directory)) { 
			$dir = mkdir($directory,0775,true); 
		} 
			
		if (!$dir AND !is_dir($directory)) { 
			// Throw an error? 
			Event::error('Content','Unable to build directory structure out of ' . Config::get('data_root')); 
			return false; 
		} 	

		return $directory; 

	} // generate_directory 

	// Figure out what file extension this should have based on its mime type
	private static function get_extension($mime_type) { 

		$data = explode("/", $mime_type);
		$extension = $data['1'];

		if ($extension == 'jpeg') { $extension = 'jpg'; }

		return $extension;

	} // get_extension 

} // end content class
?>
