$(document).ready(function(){
	$('fieldset').on('click', '.GallerySubmit', function(){
		event.preventDefault();
		tinyMCE.triggerSave();
		var all_vars = $(this).closest('fieldset');
		$(all_vars).find('textarea, input, file').each(function(){
			var name = $(this).attr('name');
			if(name == 'gallery_submit'){
				
			}
		});
	});
});
