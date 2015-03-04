<?php echo $messages; ?>
<?php if($files): ?>
<form class="form-cache" action="<?php echo $config->url . '/' . $config->manager->slug; ?>/cache/kill" method="post">
  <input name="token" type="hidden" value="<?php echo $token; ?>">
  <p><button class="btn btn-destruct" type="submit"><i class="fa fa-times-circle"></i> <?php echo $speak->delete; ?></button></p>
  <table class="table-bordered table-full-width">
    <colgroup>
      <col style="width:2.6em;">
      <col style="width:11em;">
      <col>
      <col style="width:2.6em;">
      <col style="width:2.6em;">
    </colgroup>
    <thead>
      <tr>
        <th><input type="checkbox" data-connection="selected[]"></th>
        <th><?php echo Config::speak('last_', array($speak->updated)); ?></th>
        <th><?php echo $speak->file; ?></th>
        <th class="text-center" colspan="2"><?php echo $speak->action; ?></th>
      </tr>
    </thead>
    <tbody>
      <?php $editable = explode(',', SCRIPT_EXT); foreach($files as $file): ?>
      <tr>
        <td class="text-center"><input name="selected[]" type="checkbox" value="<?php echo str_replace(array(CACHE . DS, '\\'), array("", '/'), $file->path); ?>"></td>
        <td><time datetime="<?php echo Date::format($file->update, 'c'); ?>"><?php echo Date::format($file->update, 'Y/m/d H:i:s'); ?></time></td>
        <td><span title="<?php echo $file->size; ?>"><?php echo basename($file->path); ?></span></td>
        <?php if(in_array($file->extension, $editable)): ?>
        <td class="text-center"><a class="text-construct" href="<?php echo $config->url . '/' . $config->manager->slug . '/cache/repair/file:' . str_replace(array(CACHE . DS, '\\'), array("", '/'), $file->path); ?>" title="<?php echo $speak->edit; ?>"><i class="fa fa-pencil"></i></a></td>
        <?php else: ?>
        <td></td>
        <?php endif; ?>
        <td class="text-center"><a class="text-destruct" href="<?php echo $config->url . '/' . $config->manager->slug . '/cache/kill/file:' . str_replace(array(CACHE . DS, '\\'), array("", '/'), $file->path); ?>" title="<?php echo $speak->delete; ?>"><i class="fa fa-times"></i></a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p class="pager cf"><?php echo $pager->step->link; ?></p>
</form>
<?php else: ?>
<p class="empty"><?php echo Config::speak('notify_empty', array(strtolower($speak->caches))); ?></p>
<?php endif; ?>