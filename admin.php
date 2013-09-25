<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

define('COMMUNITY_BASE_URL', get_root_url().'admin.php?page=plugin-community');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Basic checks                                                          |
// +-----------------------------------------------------------------------+

$_GET['image_id'] = $_GET['tab'];

check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];

// +-----------------------------------------------------------------------+
// | Process form                                                          |
// +-----------------------------------------------------------------------+

load_language('plugin.lang', PHPWG_PLUGINS_PATH.basename(dirname(__FILE__)).'/');
  
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
    $file_to_update = 'main';
    if (isset($_POST['file_to_update']) and in_array($_POST['file_to_update'], array('main', 'representative')))
    {
      $file_to_update = $_POST['file_to_update'];
    }

    $image_id = $_GET['image_id'];
      
    $query = '
SELECT
    id, path, representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
    $result = pwg_query($query);
    $row = pwg_db_fetch_assoc($result);

    if ('main' == $file_to_update)
    {
      add_uploaded_file(
        $_FILES['photo_update']['tmp_name'],
        $_FILES['photo_update']['name'],
        null,
        null,
        $_GET['image_id']
        );
      
      array_push(
        $page['infos'],
        l10n('The photo was updated')
        );
    }
    
    if ('representative' == $file_to_update)
    {
      $file_path = $row['path'];

      // move the uploaded file to pwg_representative sub-directory
      $representative_file_path = dirname($file_path).'/pwg_representative/';      
      $representative_file_path.= get_filename_wo_extension(basename($file_path)).'.';

      $old_representative_file_path = $representative_file_path.$row['representative_ext'];

      $representative_ext = get_extension($_FILES['photo_update']['name']);

      // in case we replace a *.jpg by *.png we have to safely remove the
      // *.jpg becase move_uploaded_file won't remove it
      if ($representative_ext != $row['representative_ext'])
      {
        @unlink($representative_file_path.$row['representative_ext']);
      }
      
      $representative_file_path.= $representative_ext;

      prepare_directory(dirname($representative_file_path));

      move_uploaded_file($_FILES['photo_update']['tmp_name'], $representative_file_path);

      $file_infos = pwg_image_infos($representative_file_path);
      
      single_update(
        IMAGES_TABLE,
        array(
          'representative_ext' => $representative_ext,
          'width' => $file_infos['width'],
          'height' => $file_infos['height'],
          ),
        array('id' => $image_id)
        );
      
      array_push(
        $page['infos'],
        l10n('The representative picture was updated')
        );
    }

    // force refresh of multiple sizes
    delete_element_derivatives($row);
  }
}

// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$page['tab'] = 'update';

$tabsheet = new tabsheet();
$tabsheet->set_id('photo');
$tabsheet->select('update');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'plugin_admin_content' => dirname(__FILE__).'/admin.tpl'
    )
  );

// retrieving direct information about picture
$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
$row = pwg_db_fetch_assoc(pwg_query($query));

if (!in_array(get_extension($row['path']), $conf['picture_ext']) or !empty($row['representative_ext']))
{
  $template->assign('show_file_to_update', true);
}

$template->assign(
  array(
    'TN_SRC' => DerivativeImage::thumb_url($row),
    'original_filename' => $row['file'],
    'TITLE' => render_element_name($row),
    )
  );

// +-----------------------------------------------------------------------+
// | sending html code                                                     |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
?>