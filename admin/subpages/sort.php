<?php
/***** Subpage - Sort *****/
/***** Subpage - Sort *****/
/***** Subpage - Sort *****/
/***** Subpage - Sort *****/
/***** Subpage - Sort *****/

/***** Needs Update : See Subpage Manage/Edit Page Sort for Photogallery *****/

	if($_POST['order'] == 'yes'){
		echo '<div class="Message"><img src="images/tick.gif"> Sort Successful.</div>';
	}
	
	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[p]'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/ /', '', $parent_vars[2]);
	$var_list = preg_replace('/\'/', '', $var_list);
	$var_list = 'Sub_'.$var_list;
	$vars = $$var_list;
	
	$all = $mysqli->query("SELECT * FROM $database WHERE type='$type' ORDER BY sort ASC");
	if(mysql_num_rows($all) > 0){
	echo '
		<div class="Title">'.$parent_vars[2].' - Sort</div>
	';
		$a=1;
		$listitems .= '<li id="listItem_0" style="display: none;"></li>';
		while($get = mysql_fetch_row($all)){
			$listitems .= '<li id="listItem_'.$get[0].'"><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" />'.$get[2].'</li>';
			$listorder .= '<div class="LabelInsert">'.$get[2].'</div>';
		}
		if($_POST['order'] == yes){
			echo'
				<div class="Label">
					'.$parent_vars[2].' - Sorted
				</div>
				<div class="LabelInsert">
					'.$listorder.'
				</div>
			';
		}
		else{
			echo'
				<script type="text/javascript" src="images/jquery-1.3.2.min.js"></script>
				<script type="text/javascript" src="images/jquery-ui-1.7.1.custom.min.js"></script>
				<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all" />
				<script type="text/javascript">
					// When the document is ready set up our sortable with it\'s inherant function(s)
					$(document).ready(function() {
						$("#sort-list").sortable({
							handle : \'.handle\',
							update : function () {
								var order = $(\'#sort-list\').sortable(\'serialize\');
								$("#info").load("calls/sort-generated-pages.php?"+order);
							}
						});
				});
				</script>
				<div class="Label">&nbsp;</div>
	
				<div class="LabelInsert">
					<ul id="sort-list">
						'.$listitems.'
					</ul>
				</div>
				<div class="LabelInsert">
					<div class="Submit" style="position: relative; text-align: left; left: 48px;">
						<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER['PHP_SELF'].'?p='.$_GET[p].'&x=sort">
							<input type="hidden" name="order" value="yes"/>
							<input type="submit" value="Save Order" style="padding: 2px 5px;"/>
						</form>
					</div>
				</div>
			';
		}
	}
	else{
		echo '<span class="title">There are no pages to list.';
	}

?>