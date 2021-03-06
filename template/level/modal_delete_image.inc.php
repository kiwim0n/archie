<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<div id="confirm_delete_image_<?php echo scrub_out($image->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Confirm Delete Level Image</h3>
  </div>
  <div class="modal-body">
    <p class="text-center"><img class="text-center" src="<?php echo Config::get('web_path'); ?>/media/image/<?php echo scrub_out($image->uid);?>/thumb" /></p>
    <p class="text-center"><?php echo scrub_out($image->notes); ?></p>
    <p>Are you sure you want to delete this image from <?php echo scrub_out($level->site->name . '-' . $level->record); ?>? This operation can not be reversed.</p>
  </div>
  <div class="modal-footer">
  <form method="post" action="<?php echo Config::get('web_path'); ?>/level/image_delete">
    <button type="submit" class="btn btn-danger">Delete Image</a>
    <input type="hidden" name="uid" value="<?php echo scrub_out($image->uid); ?>">
    <input type="hidden" name="return" value="<?php echo scrub_out(\UI\sess::location('absolute')); ?>">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
  </form>
  </div>
</div>
