<?
	$field_name = $kp;
	$field_name_display = preg_replace('/_/', ' ', $kp);
	
	$gallery_vars = 'Gallery__'.$field_name;
	$gallery_vars = $$gallery_vars;
	
	echo '<div class="Label">'.$field_name_display.'</div>';

	/***** BEGIN Display Gallery Form Fields BEFORE Currently Added Items *****/
	echo '
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?y='.$_GET['y'].'">
		<fieldset>
			<legend>Add New '.$field_name_display.'</legend>
			';
		
			foreach($gallery_vars as $key => $value){
				$display_key = preg_replace('/_/', ' ', $key);
				$display_key = preg_replace('/{.*?}/', '', $display_key);
				if(preg_match('/{image}/', $key)){
					$key = preg_replace('/{image}/', '', $key);
					echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
				}
				elseif(preg_match('/{file}/', $value)){
					echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
				}
				elseif(preg_match('/{text}/', $value)){
					echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="" class="text"></div>';
				}
				elseif(preg_match('/{textarea}/', $value)){
					$text_to_display = (isset($page_vars[$key]) && $page_vars[$key] != '') ? $page_vars[$key] : '';
					echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$text_to_display.'</textarea></div>';
				}
				elseif(preg_match('/{checkbox}/', $value)){
					echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="checkbox" name="'.$key.'" value="yes" style="width: 14px;"></div>';
				}
				elseif(preg_match('/{select}/i', $key)){
					$key = preg_replace('/{select}/i', '', $key);
					unset($options);
					foreach($value as $kk => $vv){
						if($page_vars[$key] == $vv){
							$options .= '<option selected="selected">'.$vv.'</option>';					
						}
						else{
							$options .= '<option>'.$vv.'</option>';
						}
					}
					echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'>'.$options.'</select></div><div class="Clear"></div>';
				}					
				elseif(preg_match('/{details}/i', $key)){
					echo '<div class="Details">'.$value.'</div>';
				}
			}
			
			echo'		
				<div class="Submit">
					<input type="hidden" name="field_name" value="'.$field_name.'"/>
					<input type="hidden" name="submit_add_gallery" value="yes"/>
					<input type="submit" value="Add '.$field_name_display.'" />
				</div>
		</fieldset>
	</form>
	';
	/***** END Display Gallery Form Fields BEFORE Currently Added Items *****/


	/***** Displays for Gallery Manage and Sort Sections *****/
	unset($gallery_array);
	unset($edit_buttons);
	unset($edit_block);
	unset($listitems);
		$listitems = '';
	unset($listorder);
	unset($split);
	require($_SERVER['DOCUMENT_ROOT'].'/gallery/'.$field_name.'/images.php');
	if($gallery_array != ''){
		foreach($gallery_array as $key => $value){
			$split = explode('||', $value);
			foreach($split as $k => $v){
				if($v != ''){
					$edit_buttons[] = '
						<a href="?yy='.$_GET['y'].'&field_name='.$field_name.'&array_key='.$key.'&x=gallery_edit" class="Edit">
							<img style="border: none;" src="images/options.png">
						</a>

 						<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?y='.$_GET['y'].'" onsubmit="return confirm(\'Confirm Deletion?\n This cannot be undone.\')">
							<input type="hidden" name="array_key" value="'.$key.'"/>
							<input type="hidden" name="field_name" value="'.$field_name.'"/>
							<input type="hidden" name="type" value="'.$gallery_vars['Type'].'"/>
							<input type="hidden" name="submit_remove_gallery" value="yes"/>
							<input type="image" src="images/cross.gif" class="Delete" />
						</form>
					';

					if(preg_match('/\.png/i', $v) || preg_match('/\.jpg/i', $v) || preg_match('/\.gif/i', $v) || preg_match('/\.bmp/i', $v) || preg_match('/\.jpeg/i', $v) || preg_match('/\.pns/i', $v) || preg_match('/\.tiff/i', $v)){
						$edit_block[] = '<img src="../gallery/'.$field_name.'/'.$v.'">';
						$image = '1';
					}
					elseif(preg_match('/\.[a-z]/i', $v)){
						$edit_block[] = '<p><a href="../gallery/'.$field_name.'/'.$v.'" target="_blank">../gallery/'.$field_name.'/'.$v.'</a></p>';
					}
					else{
						$edit_block[] = '<p>'.$v.'</p>';
					}

					break 1;
				}
			}
		}
	}
	/***** END Displays for Gallery Manage and Sort Sections *****/

	?>
	<div class="Clear"></div>
		
	<fieldset>
		<legend>Manage <?= $field_name_display ?></legend>

		<div class="Label"></div>
			<?
			if(!empty($edit_block)){
				foreach($edit_block as $key => $value){
					$listitems .= '<li id="itemID_'.$key.'" class="SortItem">
						<div class="GalleryItem">
							'.$edit_buttons[$key].'
							<div class="Contents">
								'.$value.'
							</div>
						</div>
					</li>';
				}
			}
			?>

			<div class="sortable" id="sortlist<?= $gallery_vars['Type'] ?>">
				<?= $listitems ?>
				<div class="Clear"></div>
			</div>

			<? /***** SORT JS *****/ ?>
			<script type="text/javascript">
				$(function() {
					$( "#sortlist<?= $gallery_vars['Type'] ?>" ).sortable({
						placeholder: "ui-state-highlight",
						opacity: 0.5,
					});
					$( "#sortlist<?= $gallery_vars['Type'] ?>" ).disableSelection();
				});

				$( "#sortlist<?= $gallery_vars['Type'] ?>" ).on( "sortupdate", function( event, ui ) {

					var sorted = $( "#sortlist<?= $gallery_vars['Type'] ?>" ).sortable( "serialize" );

					$.ajax({
						type: "POST",
						url: "calls/sort-gallery.php?fn=<?= $field_name ?>&id=<?= $_GET[y] ?>",
						datatype: "html",
						data: sorted,
					}).done(function( msg ) {
						var i = 0;
						$("#sortlist<?= $gallery_vars['Type'] ?> li").each(function(){
							var newid = "itemID_" + i;
							$(this).attr("id", newid);
							i++;
						});
						console.log(msg);
					});

				});

			</script>
			<? /***** END SORT JS *****/ ?>
	</fieldset>