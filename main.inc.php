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

add_event_handler('loc_begin_admin', 'photo_update_add_form');
function photo_update_add_form()
{
  global $template;

  $template->set_prefilter('picture_modify', 'photo_update_add_form_prefilter');
}

function photo_update_add_form_prefilter($content, &$smarty)
{
  $search = '#<form id="associations"#';
  $replacement = '
<form id="photo_update" method="post" action="{$F_ACTION}" enctype="multipart/form-data">
  <fieldset>
    <legend>{\'Photo Update\'|@translate}</legend>

    <p style="text-align:left" class="file"><input type="file" size="60" name="photo_update"></p>
    <p style="text-align:left"><input class="submit" type="submit" value="{\'Update\'|@translate}" name="photo_update"></p>
  </fieldset>
</form>

<form id="associations"
';

  return preg_replace($search, $replacement, $content);
}

add_event_handler('loc_begin_admin_page', 'photo_update_process_update');
function photo_update_process_update()
{
  load_language('plugin.lang', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');
  
  global $page, $template;
  
  if (isset($_FILES['photo_update']))
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');
    
    if ($_FILES['photo_update']['error'] !== UPLOAD_ERR_OK)
    {
      $error_message = file_upload_error_message($_FILES['photo_update']['error']);

      array_push(
        $page['errors'],
        $error_message
        );
    }
    else
    {
      check_status(ACCESS_ADMINISTRATOR);
      check_input_parameter('image_id', $_GET, false, PATTERN_ID);

      add_uploaded_file(
        $_FILES['photo_update']['tmp_name'],
        $_FILES['photo_update']['name'],
        null,
        null,
        $_GET['image_id']
        );

      $page['photo_update_refresh_thumbnail'] = true;

      $template->set_prefilter('picture_modify', 'photo_update_force_thumbnail_refresh_prefilter');

      $template->assign('REFRESH_KEY', time());
      
      array_push(
        $page['infos'],
        l10n('The photo was updated')
        );
    }
  }
}

function photo_update_force_thumbnail_refresh_prefilter($content, &$smarty)
{
  $search = '#<img src="{\$TN_SRC}"#';
  $replacement = '<img src="{$TN_SRC}?{$REFRESH_KEY}"';

  return preg_replace($search, $replacement, $content);
}
?>
