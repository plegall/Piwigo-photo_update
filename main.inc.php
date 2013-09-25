<?php
/*
Plugin Name: Photo Update
Version: auto
Description: Update a photo with a new file
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=
Author: plg
Author URI: http://piwigo.wordpress.com
*/

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

add_event_handler('tabsheet_before_select','photo_update_add_tab', 50, 2);
function photo_update_add_tab($sheets, $id)
{  
  load_language('plugin.lang', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');
  
  if ($id == 'photo')
  {
    $sheets['update'] = array(
      'caption' => l10n('Update'),
      'url' => get_root_url().'admin.php?page=plugin-photo_update-'.$_GET['image_id'],
      );
  }
  
  return $sheets;
}
?>
