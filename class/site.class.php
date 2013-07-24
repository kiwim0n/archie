<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Site extends database_object { 

	public $uid; 
  public $name;
  public $description;

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

    //FIXME: UID is the site name until we migrate
    // Hack it in until we have a database
    $table = array('1'=>array('uid'=>'10IH73','name'=>'10IH73','description'=>'Coopers Ferry')); 

		//$row = $this->get_info($uid,'site'); 
    $row = $table['1'];

		foreach ($row as $key=>$value) { 
			$this->$key = $value; 
		} 

		return true; 

	} // constructor

  /**
   * build_cache
   */
  public static function build_cache($objects) { 

    if (!is_array($objects) || !count($objects)) { return false; }

    $idlist = '(' . implode(',',$objects) . ')';

    // passing array(false causes this
    if ($idlist == '()') { return false; }

    $sql = 'SELECT * FROM `site` WHERE `site`.`uid` IN ' . $idlist; 
    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('site',$row['uid'],$row); 
    }

    return true; 

  } //build_cache

	/**
	 * refresh
	 */
	public function refresh() { 

		// Remove cache
		User::remove_from_cache('site',$this->uid); 
		// Rebuild	
		$this->__construct($this->uid); 

	} // refresh

  /**
   * create
   */
  public static function create($input) { 



  } // create

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input) { 

    if (Error::occurred()) { return false; }

    return true; 

  } // validate

  /**
   * user_level
   * returns the access level for the specified user
   */
  public static function user_level($site_uid,$user_uid) { 

    //FIXME: We need a database to do anything meaninful here
    return true; 

  } // user_level

} // end class level
?>