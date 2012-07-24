<h2>{$TITLE} &#8250; {'Edit photo'|@translate} {$TABSHEET_TITLE}</h2>

<fieldset>
  <legend>{'Photo Update'|@translate}</legend>
  <table>
    <tr>
      <td id="albumThumbnail">
        <img src="{$TN_SRC}" alt="{'Thumbnail'|@translate}" class="Thumbnail">
      </td>
      <td style="vertical-align:top">
        <form id="photo_update" method="post" action="" enctype="multipart/form-data">
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
