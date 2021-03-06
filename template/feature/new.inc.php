<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>New Feature - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_feature" method="post" action="<?php echo Config::get('web_path'); ?>/feature/create">
<div class="control-group span4<?php Error::display_class('description'); ?>">
  <label class="control-label" for="inputDescription">How is the feature differentiated from the surrounding sediments? What are its defining characteristics?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="description" id="inputDescription" tabindex="1"><?php echo scrub_out($_POST['description']); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('initial_rn'); ?>">
  <label class="control-label" for="inputInitialRN">Initial RN</label>
  <div class="controls">
    <input id="inputInitialRN" name="initial_rn" type="text" tabindex="3" value="<?php echo scrub_out($_POST['initial_rn']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input tabindex="4" id="inputNorthing" name="northing" type="text" value="<?php echo scrub_out($_POST['northing']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('easting'); ?>"> 
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" tabindex="5" name="easting" type="text" value="<?php echo scrub_out($_POST['easting']); ?>" />
  </div>
</div>

<div class="control-group span4<?php Error::display_class('keywords'); ?>">
  <label class="control-label" for="inputKeywords">Additional Notes?</label>
  <div class="controls">
    <textarea placeholder="..." rows="4" cols="80" name="keywords" id="inputKeywords" tabindex="2"><?php echo scrub_out($_POST['keywords']); ?></textarea>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elevation'); ?>">
  <label class="control-label" for="inputElevation">Elevation</label>
  <div class="controls">
    <input tabindex="5" id="inputElevation" name="elevation" type="text" value="<?php echo scrub_out($_POST['elevation']); ?>" />
  </div>
</div>
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" tabindex="6" class="btn btn-primary" value="Create" />
  </div>
</div>
</form>
