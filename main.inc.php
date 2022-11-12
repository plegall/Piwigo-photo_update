<?php
/*
Plugin Name: Photo Update
Version: auto
Description: Update a photo with a new file
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=
Author: plg
Author URI: http://le-gall.net/pierrick
Has Settings: false
*/

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

define('PHOTO_UPDATE_ID', basename(dirname(__FILE__)));
define('PHOTO_UPDATE_PATH', PHPWG_PLUGINS_PATH . PHOTO_UPDATE_ID . '/');

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+

add_event_handler('init', 'photo_update_init');
function photo_update_init()
{
  load_language('plugin.lang', PHOTO_UPDATE_PATH);
}

add_event_handler('tabsheet_before_select','photo_update_add_tab', 50, 2);
function photo_update_add_tab($sheets, $id)
{  
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
