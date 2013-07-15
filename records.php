<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php'; 
require_once 'template/header.inc.php'; 

// Switch on the action
switch (\UI\sess::location('action')) { 
  case 'upload':
    // Figure out the extension and upload accordingly
    Content::upload($_POST['record_id'],$_POST,$_FILES); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'image_edit': 
    if (!Access::has('media','write',$_POST['uid'])) { break; }
    Content::update('image',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
  case '3dmodel_edit':
    if (!Access::has('media','write',$_POST['uid'])) { break; }
    Content::update('3dmodel',$_POST['uid'],$_POST); 
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'image_delete':
    if (!Access::has('media','delete',$_POST['uid'])) {  break; }
    $image = new Content($_POST['uid'],'image'); 
    if (!$image->delete()) { 
      Error::add('delete','Unable to perform image deletion request, please contact administrator'); 
    }
    else { 
      Event::add('success','Image Deleted','small'); 
    }
    // Return to whence we came,
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
  case '3dmodel_delete':
    if (!Access::has('media','delete',$_POST['uid'])) { break; }
    $media = new Content($_POST['uid'],'3dmodel'); 
    if (!$media->delete()) { 
      Event::error('DELETE','Unable to delete media item:' . $media->filename); 
      Error::add('delete','Unable to 3D Model perform deletion request, please contact administrator'); 
    }
    
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break;
  case 'media_delete':
    if (!Access::has('media','delete',$_POST['uid'])) { break; }
    $media = new Content($_POST['uid'],'media'); 
    if (!$media->delete()) { 
      Event::error('DELETE','Unable to delete media item:' . $media->filename); 
      Error::add('delete','Unable to Media perform deletion request, please contact administrator'); 
    }
    
    header('Location:' . Config::get('web_path') . \UI\return_url($_POST['return'])); 
  break; 
	case 'update': 
		$record = new Record($_POST['record_id']); 
    // Set to current user  
    $_POST['user'] = \UI\sess::$user->uid;
		// Attempt to update this!
		if (!$record->update($_POST)) { 
      require_once \UI\template('/records/edit'); 
		} 
		else { 
      Event::add('success','Record has been updated, thanks!','small'); 
			$record = new Record($record->uid); 
			require_once \UI\template('/records/view'); 
	  } 
	break; 
  case 'edit':
		$record = new Record(\UI\sess::location('objectid')); 
    require_once \UI\template('/records/edit'); 
	break; 
  case 'view':
    $record = new Record(\UI\sess::location('objectid')); 
    require_once \UI\template();
  break;
  case 'new':
    Error::clear(); 
    require_once \UI\template(); 
  break;
  case 'create':
    $_POST['user'] = \UI\sess::$user->uid;
    if ($record_id = Record::create($_POST)) {
      $record = new Record($record_id);
      require_once \UI\template('/records/view');
    }
    else {
      require_once \UI\template('/records/new'); 
    }
  break;
  case 'delete': 
    // Admin only
    if (!Access::has('record','delete',$_POST['record_id'])) {  break; }
    // We should do some form ID checking here
    Record::delete($_POST['record_id']);
    header("Location:" . Config::get('web_path') . "/records"); 
    exit; 
  break;
  case 'print': 
    // For now its just tickets
    $ticket = new Content(\UI\sess::location('objectid'),'ticket'); 
    $record = new Record(\UI\sess::location('objectid')); 
    if (!$ticket->filename OR filemtime($ticket->filename) < $record->updated) { 
      Content::write(\UI\sess::location('objectid'),'ticket',$ticket->filename); 
    } 
    header("Location:" . Config::get('web_path') . '/media/ticket/' . \UI\sess::location('objectid'));
  break; 
  case 'search':
    $view = new View(); 
    $view->reset(); 
    $view->set_type('record'); 
    $view->set_filter($_POST['field'],$_POST['value']); 
    $records = $view->run(); 
    require_once \UI\template('/show_records'); 
  break;
  case 'sort':
    $field = \UI\sess::location('objectid') ? \UI\sess::location('objectid') : 'station_index';
    $order = \UI\sess::location('3') ? strtoupper(\UI\sess::location('3')) : '';
    $view = new View(); 
    $view->set_type('record'); 
    $view->set_sort($field,$order); 
    $view->set_start(0); 
    $records = $view->run(); 
    require_once \UI\template('/show_records'); 
  break; 
  case 'offset': 
    $view = new View(); 
    $view->set_type('record'); 
    $view->set_start(\UI\sess::location('objectid')); 
    $records= $view->run(); 
    require_once \UI\template('/show_records'); 
  break;
  default:
    $view = new View(); 
    $view->reset(); 
    $view->set_type('record'); 
    $view->set_sort('station_index','ASC');
    $records = $view->run(); 
    require_once \UI\template('/show_records');
  break; 
} // end switch
?>
<?php require_once 'template/footer.inc.php'; ?>
