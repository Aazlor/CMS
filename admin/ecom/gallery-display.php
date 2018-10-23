<?
		$field_name = $key;
		$field_name_display = preg_replace('/_/', ' ', $key);
		
		$gallery_vars = 'Gallery__'.$field_name;
		$gallery_vars = $$gallery_vars;

		echo '<div class="Label ToggleAble" data-group="'.$field_name.'">'.$field_name_display.'<span class="Right">&#9650;</span><span class="Right hide">&#9660;</span></div>';

		/***** BEGIN Display Gallery Form Fields BEFORE Currently Added Items *****/
		echo '
		<div class="toggle" data-group="'.$field_name.'">
		<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?section='.$_GET['section'].'&product_id='.$product_id.'">
			<fieldset>
				<legend>Add New '.$field_name_display.'</legend>
				';
			
				foreach($gallery_vars as $key => $value){
					$display_key = str_replace('_', ' ', $key);
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
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$page_vars[$key].'</textarea></div>';
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
						<input type="hidden" name="product_id" value="'.$product_id.'"/>
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

		require($_SERVER['DOCUMENT_ROOT'].'/products/'.$product_id.'/'.$field_name.'.php');
		if($gallery_array != ''){
			foreach($gallery_array as $key => $value){
				$split = explode('||', $value);
				foreach($split as $k => $v){
					if($v != ''){
						$edit_buttons[] = '
							<a href="?product_id='.$product_id.'&field_name='.$field_name.'&array_key='.$key.'&function=gallery_edit&section='.$_GET['section'].'" class="Edit">
								<img style="border: none;" src="images/options.png">
							</a>

	 						<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?section='.$_GET['section'].'&product_id='.$product_id.'" onsubmit="return confirm(\'Confirm Deletion?\n This cannot be undone.\')">
								<input type="hidden" name="array_key" value="'.$key.'"/>
								<input type="hidden" name="field_name" value="'.$field_name.'"/>
								<input type="hidden" name="product_id" value="'.$product_id.'"/>
								<input type="hidden" name="type" value="'.$gallery_vars['Type'].'"/>
								<input type="hidden" name="submit_remove_gallery" value="yes"/>
								<input type="image" src="images/cross.gif" class="Delete" />
							</form>
						';

						if(preg_match('/\.png/i', $v) || preg_match('/\.jpg/i', $v) || preg_match('/\.gif/i', $v) || preg_match('/\.bmp/i', $v) || preg_match('/\.jpeg/i', $v) || preg_match('/\.pns/i', $v) || preg_match('/\.tiff/i', $v)){
							$edit_block[] = '<img src="../products/'.$product_id.'/thumb-'.$v.'">';
							$image = '1';
						}
						elseif(preg_match('/\.[a-z]/i', $v)){
							$edit_block[] = '<p><a href="/products/'.$product_id.'/'.$v.'" target="_blank">/products/'.$product_id.'/'.$v.'</a></p>';
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
				<script>
				$(document).ready(function(){
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
							url: "ecom/sort-gallery.php?fn=<?= $field_name ?>&id=<?= $product_id ?>",
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

					$('.ToggleAble').unbind('click').click(function(e){
						e.stopPropagation();
						e.preventDefault();
						toggleView(this);
					});

					var group = $('.ToggleAble').first().data('group');
					$('.toggle[data-group="'+group+'"]').fadeIn('slow');
					$('.ToggleAble').first().find('.Right').fadeOut('slow');
					$('.ToggleAble').first().find('.Right:last-child').fadeIn('slow');
				});
				function toggleView(clickedElement){
					var group = $(clickedElement).data('group');
					var carrot = $(clickedElement).find('span').html();
					$(clickedElement).find('span').slideToggle();
					$('.toggle[data-group="'+group+'"]').slideToggle();
				}
				</script>
				<? /***** END SORT JS *****/ ?>
		</fieldset>
	</div>