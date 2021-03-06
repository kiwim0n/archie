<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div class="page-header">
<h4>New Level - <?php echo scrub_out(\UI\sess::$user->site->name); ?></h3>
</div>
<?php Event::display('errors'); ?>
<form class="form-horizontal" id="new_level" method="post" action="<?php echo Config::get('web_path'); ?>/level/create">
<div class="control-group span4<?php Error::display_class('unit'); ?>">
  <label class="control-label" for="inputUnit">Unit</label>
  <div class="controls">
	  <select id="inputUnit" name="unit">
    	<option value="-1">&nbsp;</option> 
	    <?php foreach (unit::$values as $value) {
	        $is_selected = '';
          if (isset($_POST['unit'])) { 
  	        if ($_POST['unit'] == $value) { $is_selected=" selected=\"selected\""; }
          } 
	    ?>
	    <option value="<?php echo scrub_out($value); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
      <?php } ?>
  	</select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('quad'); ?>">
  <label class="control-label" for="inputQuad">Quad</label>
  <div class="controls">
	  <select id="inputQuad" name="quad"> 
      <?php foreach (quad::$values as $key=>$value) { 
        $is_selected = '';
        if (isset($_POST['quad'])) { 
          if ($_POST['quad'] == $key) { $is_selected=" selected=\"selected\""; }
        }
      ?>
      <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($value); ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('level'); ?>">
  <label class="control-label" for="inputLevel">Level</label>
  <div class="controls">
	  <input id="inputLevel" name="catalog_id" type="text" value="<?php echo scrub_out($_POST['catalog_id']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('lsg_unit'); ?>">
  <label class="control-label" for="inputLsgUnit">L. U.</label>
  <div class="controls">
	  <select name="lsg_unit">
      <?php foreach (lsgunit::$values as $key=>$name) {
	      $is_selected = '';
        if (isset($_POST['lsg_unit'])) { 
          if ($_POST['lsg_unit'] == $key) { $is_selected=" selected=\"selected=\""; }
        } 
      ?>
      <option value="<?php echo scrub_out($key); ?>"<?php echo $is_selected; ?>><?php echo scrub_out($name); ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('northing'); ?>">
  <label class="control-label" for="inputNorthing">Northing</label>
  <div class="controls">
    <input id="inputNorthing" name="northing" type="text" value="<?php echo scrub_out($_POST['northing']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('easting'); ?>">
  <label class="control-label" for="inputEasting">Easting</label>
  <div class="controls">
    <input id="inputEasting" name="easting" type="text" value="<?php echo scrub_out($_POST['easting']); ?>" />
  </div>
</div>
<div class="control-group offset1 span10">
  <h5>Starting Elevations</h5>
  <hr />
</div>
<div class="control-group span4 <?php Error::display_class('elv_nw_start'); ?>">
  <label class="control-label" for="inputElvNWStart">NW</label>
  <div class="controls">
    <input id="inputElvNWStart" name="elv_nw_start" type="text" value="<?php echo scrub_out($_POST['elv_nw_start']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_ne_start'); ?>">
  <label class="control-label" for="inputElvNEStart">NE</label>
  <div class="controls">
    <input id="inputElvNEStart" name="elv_ne_start" type="text" value="<?php echo scrub_out($_POST['elv_ne_start']); ?>" />
  </div>
</div>
<div class="control-group span4 <?php Error::display_class('elv_sw_start'); ?>">
  <label class="control-label" for="inputElvSWStart">SW</label>
  <div class="controls">
    <input id="inputElvSWStart" name="elv_sw_start" type="text" value="<?php echo scrub_out($_POST['elv_sw_start']); ?>" />
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('elv_se_start'); ?>">
  <label class="control-label" for="inputElvSEStart">SE</label>
  <div class="controls">
    <input id="inputElvSEStart" name="elv_se_start" type="text" value="<?php echo scrub_out($_POST['elv_se_start']); ?>" />
  </div>
</div>
<div class="control-group span4<?php Error::display_class('elv_center_start'); ?>">
  <label class="control-label" for="inputElvCenterStart">Center</label>
  <div class="controls">
    <input id="inputElvCenterStart" name="elv_center_start" type="text" value="<?php echo scrub_out($_POST['elv_center_start']); ?>" />
  </div>
</div>
<div class="control-group offset1 span10">
  <h5>Excavators</h5>
  <hr />
</div>
<?php 
  // Current valid users
  $excavators = User::get('enabled'); 
?>
<div class="control-group span4<?php Error::display_class('excavator_one'); ?>">
  <label class="control-label" for="inputExcavatorone">First</label>
  <div class="controls">
    <?php if (!Access::is_admin()) { $onedisabled='disabled="disabled"'; } ?>
    <select id="inputExcavatorone" name="excavator_one" <?php echo $onedisabled; ?>>
      <option value="">&nbsp;</option>
      <?php
      foreach ($excavators as $user) { 
        $is_selected = '';
        if (\UI\sess::$user->uid == $user->uid) { $is_selected = ' selected="selected"'; }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('excavator_two'); ?>">
  <label class="control-label" for="inputExcavatortwo">Second</label>
  <div class="controls">
    <select id="inputExcavatortwo" name="excavator_two">
      <option value="">&nbsp;</option>
      <?php
      foreach ($excavators as $user) { 
        $is_selected = '';
        if (isset($_POST['excavator_tow'])) {
          if ($_POST['excavator_two'] == $user->uid) { $is_selected = ' selected="selected"'; }
        }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4<?php Error::display_class('excavator_three'); ?>">
  <label class="control-label" for="inputExcavatorthree">Third</label>
  <div class="controls">
    <select id="inputExcavatorthree" name="excavator_three">
      <option value="">&nbsp;</option>
      <?php
      foreach ($excavators as $user) { 
        $is_selected = '';
        if (isset($_POST['excavator_three'])) {
          if ($_POST['excavator_three'] == $user->uid) { $is_selected = ' selected="selected"'; }
        }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div>
<div class="control-group span4 offset1<?php Error::display_class('excavator_four'); ?>">
  <label class="control-label" for="inputExcavatorfour">Fourth</label>
  <div class="controls">
    <select id="inputExcavatorfour" name="excavator_four">
      <option value="">&nbsp;</option>
      <?php 
      foreach ($excavators as $user) { 
        $is_selected = '';
        if (isset($_POST['excavator_four'])) {
          if ($_POST['excavator_four'] == $user->uid) { $is_selected = ' selected="selected"'; }
        }
      ?>
      <option value="<?php echo scrub_out($user->uid); ?>"<?php echo $is_selected; ?>><?php echo $user->name; ?></option>
      <?php } ?>
    </select>
  </div>
</div> 
<div class="control-group span8">
  <div class="controls">
  	<input type="submit" class="btn btn-primary" value="Create" />
  </div>
</div>
</form>
