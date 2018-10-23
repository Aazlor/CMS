<?

function iterate_categories($fetchCats, &$mysqli){

	global $database;

	while($cat = $fetchCats->fetch_array()){
		$checkSubCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='$cat[0]' ORDER BY sort, name ASC");
		$count = $checkSubCats->num_rows;
		if($count > 0){
			echo '<li data-id="'.$cat['id'].'"> <span class="CatName" data-name="'.$cat['name'].'">'.$cat['name'].'</span> <span class="CatCog"><img src="/admin/images/options.png"></span><span class="AddCat">+</span><ul class="Cat">';
			iterate_categories($checkSubCats, $mysqli);
			echo '</ul></li>';
		}
		else{
			echo '<li data-id="'.$cat['id'].'"> <span class="CatName" data-name="'.$cat['name'].'">'.$cat['name'].'</span> <span class="CatCog"><img src="/admin/images/options.png"></span><span class="AddCat">+</span></li>';			
		}
	}
}
?>

<script>

$(document).ready(function(){

	$('.Categories').on('click', '.CatCog', function(){	
		if($(this).siblings('.CatName').hasClass('inEdit')){
			var parentCatName = $(this).parent('li').data('cat');
			$(this).siblings('.CatName').html(parentCatName);
			$(this).siblings('.CatName').removeClass('inEdit');
		}
		else{
			var line = '';
			$(this).parentsUntil('ul.Categories').each(function(){
				if($(this).is('li')){
					line = $(this).data('cat') + '|' + line;
				}
			});
			var catId = $(this).parent('li').data('id');
			var currCatName = $(this).siblings('.CatName').data('name');
			var editBlock = '<input type="text" name="changeCatName" value="'+currCatName+'" data-id="'+catId+'"><button class="changeName">save</button><span class="DeleteCat" data-id="'+catId+'"><img src="/admin/images/cross.gif"></span>';
			$(this).siblings('.CatName').html(editBlock);
			$(this).siblings('.CatName').addClass('inEdit');
		}
	});

	$('.Categories').on('click', '.AddCat', function(){	
		var parentID = $(this).parent('li').data('id');
		if($(this).parent('li').has("ul").length){
			var new_cat = '<li class="Temp"><input type="text" name="NewCatName" value=""><button class="addNewCat" data-parentid="'+parentID+'">add</button><span class="RemoveCat"><img src="/admin/images/cross.gif"></span></li>';
			$(this).parent('li').children('ul').append(new_cat);			
		}
		else{
			var new_cat = '<ul class="Cat Temp"><li class="Temp"><input type="text" name="NewCatName" value=""><button class="addNewCat" data-parentid="'+parentID+'">add</button><span class="RemoveCat"><img src="/admin/images/cross.gif"></span></li></ul>';
			$(this).parent('li').append(new_cat);
		}
	});

	$('.Categories').on('click', '.RemoveCat', function(){
		var parentUL = $(this).closest('ul.Temp');
		var countLI = $(parentUL).find('li').length;
		
		$(this).parent('li.Temp').remove();

		if(countLI <= 1){
			$(parentUL).remove();
		}
	});

	$('.Categories').on('click', '.DeleteCat', function(){
		var _this = $(this);
		var id = $(this).data('id');

		console.log(id+'!@#');

		$.ajax({
			type: "POST",
			url: "/admin/ecom/ajax.php",
			data: { deleteid: id }
		}).done(function( msg ) {
			console.log(msg);
			$(_this).closest('li').remove();
		});
	});

	$('.Categories').on('click', 'button.changeName', function(){
		var id = $(this).siblings('input[name="changeCatName"]').data('id');
		var NewCatName = $(this).siblings('input[name="changeCatName"]').val();
		var span = $(this).parent('.CatName');

		$.ajax({
			type: "POST",
			url: "/admin/ecom/ajax.php",
			data: { id: id, newName: NewCatName }
		}).done(function( msg ) {
			$(span).data('name', msg);
			$(span).text(msg);
		});
	});

	$('.Categories').on('click', 'button.addNewCat', function(){
		var _this = $(this);
		var id = $(this).data('parentid');
		var NewCatName = $(this).siblings('input[name="NewCatName"]').val();

		$.ajax({
			type: "POST",
			url: "/admin/ecom/ajax.php",
			data: { parentid: id, NewCatName: NewCatName }
		}).done(function( msg ) {
			$(_this).closest('ul').html(msg);
		});
	});

	$('.Submit').on('click', 'input[type="submit"]', function(){
		var catName = $('.Primary').val();

		$.ajax({
			type: "POST",
			url: "/admin/ecom/ajax.php",
			data: { newCat: catName }
		}).done(function( msg ) {
			if(msg == 'success'){
				location.reload();
			}
			else{
				var error = '<div class="Error" id="Error">Something went horribly wrong.  Please contact your system admin, or <a href="http://www.claytonpukitis.com/contact.html">Clayton Pukitis</a> with a detailed report of what you broke.</div>';
				$('.Title').after(error)
			}
		});
	});

});

</script>

<div class="Title">
	<?= $site_name; ?> Categories
</div>
<?
	$topCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='Primary' ORDER BY sort ASC");

	if($topCats){
		?>
		<div class="Label">
			Add New Primary Category
		</div>
		<div class="LabelInsert">
			<input type="text" name="category" value="" class="text Primary">

			<div class="Submit">
				<input type="submit" value="Add Category">
			</div>
		</div>

		<ul class="Categories">
		<?

		iterate_categories($topCats, $mysqli);
		
		?></ul><?
	}
	else{
		?>
		<div class="Label">
			Add Your First Category
		</div>
		<div class="LabelInsert">
			<input type="text" name="category" value="" class="text Primary">
		</div>

		<div class="Submit">
			<input type="submit" value="Add Category">
		</div>
		<?
	}
?>