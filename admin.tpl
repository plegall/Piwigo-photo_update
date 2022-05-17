<fieldset>
  <legend>{'Photo Update'|@translate}</legend>
  <table>
    <tr>
      <td id="albumThumbnail" style="vertical-align:top">
        <img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">
      </td>
      <td style="vertical-align:top">
        <form id="photo_update" method="post" action="" enctype="multipart/form-data">
{if isset($show_file_to_update)}
          <p style="text-align:left; margin-top:0;">
            <strong>{'File to update'|@translate}</strong><br>
            <label><input type="radio" name="file_to_update" value="main"> {'main file'|@translate} ({$original_filename})</label>
            <label><input type="radio" name="file_to_update" value="representative" checked="checked"> {'representative picture'|@translate}</label>
          </p>
{/if}
          <p style="text-align:left; margin-top:0;">
            <strong>{'Select a file'|@translate}</strong><br>
            <input type="file" size="60" name="photo_update">
          </p>
          <p style="text-align:left"><input class="submit" type="submit" value="{'Update'|@translate}" name="photo_update"></p>
        </form>
      </td>
    </tr>
  </table>
</fieldset>
