/***** INIT TINYMCE *****/
tinymce.init({
    selector: "textarea",
    plugins : 'advlist autolink link image lists charmap print preview emoticons table wordcount',
    browser_spellcheck : true
 });

/***** FUNCTION DECLARATION *****/
function toggleView(clickedElement){
	var group = $(clickedElement).data('group');
	var carrot = $(clickedElement).find('span').html();
	$(clickedElement).find('span').slideToggle();
	$('.toggle[data-group="'+group+'"]').slideToggle();
}

/***** ON PAGE LOAD *****/
$(document).ready(function() {
	/***** NUMBERS *****/
	$('.number').blur(function(){
		if($.isNumeric($(this).val()) == true){
			$(this).css('border', '1px solid #CDCED0');		
		}
		else{
			$(this).focus();
			$(this).css('border', '2px solid #cc0000');
		}
	});

	/***** TOGGLEBOX CHECKBOX AND LOAD CHECK *****/
	function execTogglebox(toggleCheckBox){
		// console.log(toggleCheckBox)
		var checked = $(toggleCheckBox).data('checked');
		var unchecked = $(toggleCheckBox).data('unchecked');
		if($(toggleCheckBox).prop('checked')){
			$('.Wrapper-'+checked).show();
			$('.Wrapper-'+unchecked).hide();
		}
		else{
			$('.Wrapper-'+checked).hide();
			$('.Wrapper-'+unchecked).show();
		}		
	}

	$('.togglebox').each(function(){
		execTogglebox(this);
	});

	$('.togglebox').on('change', function(){
		execTogglebox(this);
	});


	/***** SORT GALLERY IMAGES DRAG AND DROP *****/
	$(function() {
		$( ".sortable" ).sortable({
			placeholder: "ui-state-highlight",
			opacity: 0.5,
		});
		$( ".sortable" ).disableSelection();
	});

	$( ".sortable" ).on( "sortupdate", function( event, ui ) {

		console.log(this);

		var group = $(this).attr('id');
		var itemID = $(this).data('id');
		var field_name = $(this).data('fn');
		console.log(group, itemID, field_name);

		var sorted = $( '#sortlist'+group ).sortable( "serialize" );

		$.ajax({
			type: "POST",
			url: "ecom/sort-gallery.php?fn="+field_name+"&id="+itemID,
			datatype: "html",
			data: sorted,
		}).done(function( msg ) {
			var i = 0;
			$('#sortlist'+group+' li').each(function(){
				var newid = "itemID_" + i;
				$(this).attr("id", newid);
				i++;
			});
			console.log(msg);
		});
	});

	/***** SORT PRODUCTS DRAG AND DROP *****/
	// $(function() {
	// 	$( "#ProductList" ).sortable({
	// 		placeholder: "ui-state-highlight",
	// 		opacity: 0.5,
	// 		helper: 'clone',
	// 	});
	// 	$( "#ProductList" ).disableSelection();
	// });

	// $( "#ProductList" ).on( "sortupdate", function( event, ui ) {

	// 	var sorted = $( "#ProductList" ).sortable( "serialize" );
	// 	var category = $('select.Cats').val();
		
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "ecom/sort-products.php?id=<?= (isset($product_id)) ? $product_id : '' ?>",
	// 		datatype: "html",
	// 		data: {sorted: sorted, cat: category}
	// 	}).done(function( msg ) {
	// 		console.log(msg);
	// 	});

	// });

	/***** TOGGLE DISPLAY FOR GALLERIES *****/
	$('.ToggleAble').unbind('click').click(function(e){
		e.stopPropagation();
		e.preventDefault();
		toggleView(this);
	});

	var group = $('.ToggleAble').first().data('group');
	$('.toggle[data-group="'+group+'"]').fadeIn('slow');
	$('.ToggleAble').first().find('.Right').fadeOut('slow');
	$('.ToggleAble').first().find('.Right:last-child').fadeIn('slow');

	/***** CATEGORY ADD/EDIT/DELETE *****/
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
			url: "/admin/aa_ajax.php",
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
			url: "/admin/aa_ajax.php",
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
			url: "/admin/aa_ajax.php",
			data: { parentid: id, NewCatName: NewCatName }
		}).done(function( msg ) {
			// console.log(msg);
			$(_this).closest('ul').html(msg);
		});
	});

	/***** AJAX SUBMIT - NEW CATEGORY *****/
	$('.Submit').on('click', 'input[type="submit"]', function(){
		var catName = $('.Primary').val();

		$.ajax({
			type: "POST",
			url: "/admin/aa_ajax.php",
			data: { newCat: catName }
		}).done(function( msg ) {
			// console.log(msg, '!!!');
			if(msg == 'success'){
				location.reload();
			}
			else{
				var error = '<div class="Error" id="Error">Something went horribly wrong.  Please contact your system admin, or <a href="http://www.claytonpukitis.com/contact.html">Clayton Pukitis</a> with a detailed report of what you broke.</div>';
				$('.Title').after(error)
			}
		});
	});

	/***** PRODUCTS *****/
	$('input.NewProduct').click(function(){
		window.location.replace("?section=product");
	});

	$('#ProductList').on('mouseenter', '.Delete', function(e){
		$(this).siblings('.Product').stop().fadeTo("fast", 0.3);
	});
	$('#ProductList').on('mouseleave', '.Delete', function(e){
		$(this).siblings('.Product').stop().fadeTo("fast", 1);
	});

	$('#ProductList').on('click', '.Delete', function(){
		console.log(this);
		var this_ = $(this).closest('li');
		var id = $(this).data('id');
		if(confirm("Are you sure you want to delete this product?  This cannot be undone.")){
			$.ajax({
				type: "POST",
				url: "aa_ajax.php",
				data: { deleteid: id }
			}).done(function(msg){
				$(this_).remove();
			});
		}
	});

	$('#ProductList').on('click', '.Product', function(){
		var product_id = $(this).data('product');
		window.location.href = 'aa_manage.php?id='+product_id;
	});

	$('.SearchProducts, select.Cats').on('change keyup', function() {
		var contentid = $(this).data('contentid');
		console.log(contentid);
		var search_val = $('.SearchProducts').val();
		var search_cat = $('select.Cats').val();
		var img_path = $('#'+contentid).data('img_path');

		// console.log(search_val, search_cat, img_path);

		getProducts(search_val, search_cat, img_path, contentid);
	});

	function getProducts(search, category, img_path, contentid){
		$.ajax({
			type: "POST",
			url: "aa_ajax.php",
			data: { search: search, category: category, img_path: img_path }
		}).done(function( msg ) {
			$('#'+contentid).fadeOut('fast', function(){
				$('#'+contentid).html(msg);
				$('#'+contentid).fadeIn('fast');
			});
		});		
	}
});
/***** END JS ON LOAD *****/
