/***** INIT TINYMCE *****/
tinymce.init({
	selector: "textarea",
	plugins : 'advlist autolink link image lists charmap print preview emoticons table wordcount',
	browser_spellcheck : true,
	setup: function (editor) {
		editor.on('change', function () {
			tinymce.triggerSave();
		});
	}
 });

/***** FUNCTION DECLARATION *****/
function toggleView(clickedElement){
	var group = $(clickedElement).data('group');
	var carrot = $(clickedElement).find('span').html();
	$(clickedElement).find('span').slideToggle();
	$('.toggle[data-group="'+group+'"]').slideToggle();
}

/// JQUERY donetyping ADD
(function($) {
$.fn.donetyping = function(callback){
    var _this = $(this);
    var x_timer;    
    _this.keyup(function (){
        clearTimeout(x_timer);
        x_timer = setTimeout(clear_timer, 300);
    }); 

    function clear_timer(){
        clearTimeout(x_timer);
        callback.call(_this);
    }
}
})(jQuery);

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

		$.ajax({
			type: "POST",
			url: "/admin/ajax.php",
			data: { deleteid: id }
		}).done(function( msg ) {
			$(_this).closest('li').remove();
		});
	});

	$('.Categories').on('click', 'button.changeName', function(){
		var id = $(this).siblings('input[name="changeCatName"]').data('id');
		var NewCatName = $(this).siblings('input[name="changeCatName"]').val();
		var span = $(this).parent('.CatName');

		$.ajax({
			type: "POST",
			url: "/admin/ajax.php",
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
			url: "/admin/ajax.php",
			data: { parentid: id, NewCatName: NewCatName }
		}).done(function( msg ) {
			$(_this).closest('ul').html(msg);
		});
	});

	/***** AJAX SUBMIT - NEW CATEGORY *****/
	$('.Submit').on('click', 'input[type="submit"]', function(){
		var catName = $('.Primary').val();

		$.ajax({
			type: "POST",
			url: "/admin/ajax.php",
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

	/***** PRODUCTS *****/
	$('input.NewProduct').click(function(){
		window.location.replace("?section=product");
	});

	$('#ProductList').on('mouseenter', '.Delete', function(e){
		$(this).closest('.Product').stop().fadeTo("fast", 0.3);
	});
	$('#ProductList').on('mouseleave', '.Delete', function(e){
		$(this).closest('.Product').stop().fadeTo("fast", 1);
	});

	$('#ProductList').on('click', '.Delete', function(e){
		e.stopPropagation();
		e.preventDefault();		
		var this_ = $(this).closest('li');
		var id = $(this).data('id');
		if(confirm("Are you sure you want to delete this product?  This cannot be undone.")){
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: { deleteid: id }
			}).done(function(msg){
				$(this_).remove();
			});
		}
	});

	$('.SearchProducts').donetyping(function() {
		var contentid = $(this).data('contentid');
		var search_val = $('.SearchProducts').val();
		var search_cat = $('select.Cats').val();
		var img_path = $('#'+contentid).data('img_path');
		getProducts(search_val, search_cat, img_path, contentid);
	});

	$('select.Cats').on('change', function() {
		var contentid = $(this).data('contentid');
		var search_val = $('.SearchProducts').val();
		var search_cat = $('select.Cats').val();
		var img_path = $('#'+contentid).data('img_path');
		getProducts(search_val, search_cat, img_path, contentid);
	});

	$('.SortProducts').on('change keyup', function() {
		var contentid = $(this).data('contentid');
		var search_cat = $('select.SortProducts').val();
		var img_path = $('#'+contentid).data('img_path');

		getProducts('', search_cat, img_path, contentid);

		$('#ProductList').data('fn', search_cat);

		$('#ProductList').sortable({
			placeholder: "ui-state-highlight",
			helper: 'clone',
			tolerance: 'touch',
			items: 'li',
			update: function () {
				var list = $(this);
				var sorted = $(this).sortable( "serialize" );
				var catID = $(this).data('fn')

				console.log(list) 
				console.log(sorted)
				console.log(catID)

				$.ajax({
					type: "POST",
					url: "/admin/ajax.php",
					data: {sortProducts: true, sort: sorted, categoryID: catID},
				}).done(function( msg ) {
					console.log(msg);					
				});
			}
		});
	});

	function getProducts(search, category, img_path, contentid){
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: { search: search, category: category, img_path: img_path }
		}).done(function( msg ) {
			$('#'+contentid).html(msg);
			// $('#'+contentid).fadeOut('fast', function(){
			// 	$('#'+contentid).fadeIn('fast');
			// });
		});		
	}

	/***** SORT GALLERY IMAGES DRAG AND DROP *****/
	var sortableVars = {
		placeholder: "ui-state-highlight",
		helper: 'clone',
		tolerance: 'touch',
		items: 'li',
		update: function () {
			var list = $(this);
			var sorted = $(this).sortable( "serialize" );
			var gallery = $(this).find('.sortable').data('fn')
			var id = $(this).find('.sortable').data('id')

			$.ajax({
				type: "POST",
				url: "/admin/ajax.php",
				data: {function: 'sort', sort: sorted, gallery: gallery, id: id},
			}).done(function( msg ) {
				var i = 0;
				$(list).find('li').each(function(){
					var newid = "itemID_" + i;
					$(this).attr("id", newid);
					$(this).data('id', i);
					i++;
				});
			});
		}
	}
	$('.FieldInput.LabelInsert').sortable(sortableVars);


	/***** GALLERY DIALOG *****/
	dialog = $( "#Dialog" ).dialog({
		autoOpen: false,
		height: 500,
		width: 700,
		modal: true,
		buttons: {
			"Save": galleryAddSubmit,
			Cancel: function() {
				dialog.dialog( "close" );
			}
		},
		close: function() {
			// form[ 0 ].reset();
			// allFields.removeClass( "ui-state-error" );
		}
	});

	$('#Content').on('click', 'button.dialogue', function(e){
		e.preventDefault();
		e.stopPropagation();

		var group = $(this).parent().data('group');
		var id = $(this).parent().data('id');

		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: { gallery: group, function: 'add', id: id },
			dataType: 'json',
		}).done(function(msg){

			$('#Dialog').html(msg.html);
			$('#Dialog').closest('.ui-dialog').find('.ui-dialog-title').html(msg.title);
			dialog.dialog( "open" );
			tinymce.init({
				selector: "textarea",
				setup: function (editor) {
					editor.on('change', function () {
						tinymce.triggerSave();
					});
				}
			});			
		});

		return false;
	});

	$('#Content').on('click', '.GalleryItem', function(){
		var data = $(this).closest('.sortable').data();
		var item = $(this).closest('li').data('id');

		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: { gallery: data.fn, function: 'item', id: data.id, item: item },
			dataType: 'json',
		}).done(function(msg){

			$('#Dialog').html(msg.html);
			$('#Dialog').closest('.ui-dialog').find('.ui-dialog-title').html(msg.title);
			dialog.dialog( "open" );
			tinymce.init({
				selector: "textarea",
				setup: function (editor) {
					editor.on('change', function () {
						tinymce.triggerSave();
					});
				}
			});
		});		
	});

	function galleryAddSubmit(){
		var field_name = $('#Dialog form input[name="field_name"]').val();

		var target = $('._toggle[data-group="'+field_name+'"').parent();

		var options = { 
			target:		target,   // target element(s) to be updated with server response 
			beforeSubmit:  showRequest,  // pre-submit callback 
			success:	   showResponse  // post-submit callback 
		};

		$('#Dialog form').ajaxSubmit(options);
	}

	function showRequest(data){
		$('#Content').find('.sortable').sortable(sortableVars);
	}

	function showResponse(msg){
		$('#Dialog').dialog('close');
	}


	$('#Dialog').on('click', 'input:checkbox#delete', function(){
		if($(this).is(':checked'))
			$(this).closest('.Delete').addClass('ui-state-error');
		else
			$(this).closest('.Delete').removeClass('ui-state-error');
	});

});
/***** END JS ON LOAD *****/
