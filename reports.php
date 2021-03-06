<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
require_once 'class/init.php';
require_once 'template/header.inc.php'; 

// Switch on the action
switch (\UI\sess::location('action')) { 
  case 'download': 
    if (!Access::has('report','read')) { \UI\access_denied(); }
      $report = new Report(\UI\sess::location('2'),\UI\sess::location('3')); 
      $report->download(\UI\sess::location('4')); 
  break;
  case 'request':
    if (!Access::has('report','create')) { \UI\access_denied(); }
      $report = new Report(\UI\sess::location('2'),\UI\sess::location('3')); 
      if ($report->request(\UI\sess::location('4'))) {
        Event::add('success','Report scheduled. This may take a long time, you will be e-mailed once the report is complete'); 
      }
    header("Location:" . Config::get('web_path') . "/reports/view"); 
  break; 
  default:
    if (!Access::has('report')) { \UI\access_denied(); }
    require_once \UI\template('/reports/view'); 
  break;
}
require_once \UI\template('/footer');
?>
