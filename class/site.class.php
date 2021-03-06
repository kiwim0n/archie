<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Site extends database_object { 

  public $uid; 
  public $name;
  public $description;
  public $northing; 
  public $easting;
  public $elevation;
  public $principal_investigator; // site.principal_investigator
  public $partners; // text field
  public $excavation_start; // timestamp
  public $excavation_end; // timestamp
  public $enabled; 

	// Constructor takes a uid
	public function __construct($uid='') { 

		if (!is_numeric($uid)) { return false; } 

		$row = $this->get_info($uid,'site'); 

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
   * get_from_name
   * Take a sitename and return the object
   */
  public static function get_from_name($name) { 

    $name = Dba::escape($name); 

    $sql = "SELECT `uid` FROM `site` WHERE `name`='$name'";
    $db_results = Dba::read($sql); 

    $row = Dba::fetch_assoc($db_results);
    return $row['uid'];

  } // get_from_name

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

    // Clear any previous Error state
    Error::clear(); 

    if (!Site::validate($input)) {
      Error::add('general','Invalid Field Values - please check input');
      return false; 
    }

    $name = Dba::escape($input['name']);
    $desc = Dba::escape($input['description']);
    $exc_start = Dba::escape($input['excavation_start']);
    $exc_end = Dba::escape($input['excavation_end']);
    $pi = Dba::escape($input['pi']);
    $elevation = Dba::escape($input['elevation']);
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $partners = Dba::escape($input['partners']);
    $sql = "INSERT INTO `site` (`name`,`description`,`principal_investigator`,`excavation_start`,`excavation_end`,`partners`,`northing`,`easting`,`elevation`,`enabled`) " . 
      "VALUES ('$name','$desc','$pi','$exc_start','$exc_end','$partners','$northing','$easting','$elevation','1')";
    $results = Dba::write($sql); 

    $insert_id = Dba::insert_id();

    if (!$insert_id OR !$results) { 
      Error::add('general','Unknown database error adding new site');
      return false;
    }

    return $insert_id;

  } // create

  /**
   * update
   * Updates a site
   */
  public function update($input) { 

    // Reset the error state
    Error::clear();

    if (!Site::validate($input,$this->uid)) { 
      Error::add('general','Invalid Field Values - Please check your input and try again');
      return false;
    }

    $uid = Dba::escape($this->uid);
    $name = Dba::escape($input['name']);
    $pi = Dba::escape($input['pi']);
    $description = Dba::escape($input['description']);
    $partners = Dba::escape($input['partners']);
    $exc_start = Dba::escape($input['excavation_start']);
    $exc_end = Dba::escape($input['excavation-end']);
    $elevation = Dba::escape($input['elevation']);
    $northing = Dba::escape($input['northing']);
    $easting = Dba::escape($input['easting']);
    $sql = "UPDATE `site` SET `name`='$name',`principal_investigator`='$pi',`description`='$description'," . 
      "`partners`='$partners',`excavation_start`='$exc_start',`excavation_end`='$exc_end',`elevation`='$elevation'," . 
      "`northing`='$northing',`easting`='$easting' WHERE `uid`='$uid'";
    $db_results = Dba::write($sql);

    if (!$db_results) { 
      Error::add('general','Unknown Database Error - Please try again');
      return false;
    }

    return true;

  } // update

  /**
   * validate
   * Validates the 'input' we get for update/create operations
   */
  public static function validate($input,$uid=0) { 

    // Make sure there's a name and it's unique
    if (!strlen($input['name'])) { 
      Error::add('name','Required Field');
    }

    $site_uid = Site::get_from_name($input['name']);

    if ($site_uid > 0 AND $site_uid != $uid) {
      Error::add('name','Name already exists');
    }

    // Require a start a PI
    if (!strlen($input['pi'])) {
      Error::add('pi','Required Field');
    } 

    // Make sure if start and end are set that end is after start
    $start = strtotime($input['excavation_start']);
    $end = strtotime($input['excavation_end']);

    if ($start > 0 AND $end > 0 AND $start > $end) { 
      Error::add('excavation_end','End must be after Start');
    }

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

  /**
   * get_all
   * Return all of the sites
   */
  public static function get_all() { 

    $results = array(); 

    $sql = 'SELECT * FROM `site`';
    $db_results = Dba::read($sql); 
    while ($row = Dba::fetch_assoc($db_results)) { 
      parent::add_to_cache('site',$row['uid'],$row);
      $results[] = new Site($row['uid']); 
    }

    return $results;

  } // get_all

} // end class level
?>
