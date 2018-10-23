<?php

if($_POST[Submit] == 'Upload Images'){
  $uploads_dir = '../ximages';
  foreach ($_FILES["images"]["error"] as $key => $error) {
      if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["images"]["tmp_name"][$key];
          $name = $_FILES["images"]["name"][$key];
          move_uploaded_file($tmp_name, "$uploads_dir/$name");
      }
  }
}

if($_POST[delete_image] == 'yes'){
  unlink('../ximages/'.$_POST[imageid]);
}


include("header.php");
include("db.php");


echo  '<table width=95% style="margin-left: 20px;"><tr><td><span class=bigfont>Manage Images</span></td></tr></table>';

if ($handle = opendir('../ximages')) {
  echo '<div style="display: block; margin-left: 20px;">

    <form action="" method="POST" enctype="multipart/form-data">
        Select Images to Upload: 
        <input type="file" name="images[]" multiple/>
        <br>
        <input type="Submit" name="Submit" value="Upload Images">
    </form>

    <div style="margin-top: 10px; margin-bottom: 25px; border-bottom: 1px solid #000;"></div>
  ';



  while (false !== ($entry = readdir($handle))) {
    if($entry != '.' && $entry != '..'){
      echo '
      <div style="width: 200; height: 200; overflow: hidden; padding: 10px; display: inline-block; position: relative; border: 1px dashed #000; margin-left: -1px; margin-bottom: -1px; background: #f1f1f1;">
        <div style="width: 200; height: 170px; overflow: hidden; margin-bottom: 5px; border: 1px dotted #666;">
          <img src="/ximages/'.$entry.'" width="200">
        </div>
        Path:<input type="text" value="http://'.$_SERVER["SERVER_NAME"].'/ximages/'.$entry.'" width="150">
        <form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
          <input type="hidden" name="imageid" value="'.$entry.'"/>
          <input type="hidden" name="delete_image" value="yes"/>
          <input type="image" src="/images/cross.gif" style="position: absolute; bottom: 0; right: 0;" alt="Delete" title="Delete" onCLick="return confirm(\'Confirm Deletion of Image\')" />
        </form>
      </div>
      ';
      $a++;
    }
  }

  echo '</div><div style="clear:both;"></div>';

  closedir($handle);
}


include("footer.php");
?>
