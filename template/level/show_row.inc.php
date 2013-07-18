<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
if (INIT_LOADED != '1') { exit; }
?>
<tr>
  <td>
      <a href="<?php echo Config::get('web_path'); ?>/level/view/<?php echo scrub_out($level->uid); ?>">
      <?php echo scrub_out($level->record); ?></a>
  </td>
	<td><?php echo scrub_out($level->unit); ?></td>
	<td><?php echo scrub_out($level->quad->name); ?></td>
  <td><?php echo \UI\boolean_word($level->closed); ?></td>
  <td>
    <div class="btn-group">
      <button class="btn" data-toggle="collapse" data-target="#more_<?php echo scrub_out($level->uid); ?>_info">More</button>
      <a href="#" class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
    <ul class="dropdown-menu">
      <?php if (Access::has('admin','admin',$level->uid) OR !$level->closed) { ?>
      <li><a href="<?php echo Config::get('web_path'); ?>/level/edit/<?php echo scrub_out($level->uid); ?>">Edit</a></li>
      <?php } ?>
      <?php if (Access::has('level','delete',$level->uid)) { ?>
      <li><a href="#confirmdel_<?php echo scrub_out($level->uid); ?>" role="button" data-toggle="modal">Delete</a></li>
      <?php } ?>
      <li><a target="_blank" href="<?php echo Config::get('web_path'); ?>/level/report/<?php echo scrub_out($level->uid); ?>">Generate Report</a></li>
    </ul>
    </div>
    <?php if (Access::has('level','delete',$level->uid)) { ?>
      <div id="confirmdel_<?php echo scrub_out($level->uid); ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
          <h3 id="myModalLabel">Confirm Delete Request</h3>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <?php echo $level->site . '-' . $level->record; ?> level</p>
        </div>
        <div class="modal-footer">
          <form method="post" action="<?php echo Config::get('web_path'); ?>/level/delete">
          <button type="submit" class="btn btn-danger">Delete</a>
          <input type="hidden" name="record_id" value="<?php echo scrub_out($level->uid); ?>" />
          <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
          </form>
        </div>
    </div>
  <?php } ?>
  </td>
</tr> 
<tr style="border:0px;">
  <td colspan="10">
  <div class="accordion-body collapse" style="height: 0px;" id="more_<?php echo scrub_out($level->uid); ?>_info">
    <div class="accordion-inner">
      <strong>Created by -</strong> <?php echo scrub_out($level->user->username); ?> on <?php echo scrub_out(date('d-M-Y',$level->created)); ?>
      <?php if ($level->updated) { ?>last updated on <?php echo scrub_out(date('d-M-Y',$level->updated)); ?><?php } ?>
      <blockquote><?php echo scrub_out($level->notes); ?></blockquote>
    </div>
  </div>
  </td>
</tr>