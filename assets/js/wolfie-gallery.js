jQuery(document).ready(function($){ 
	function commaCorrection(val) {
		var newVal = val.replace(/,\s*$/, "");
		newVal = newVal.replace(/^,/, '');
		newVal = newVal.replace(/,,/, ',');
		return newVal;
	}
	function getIds(holder){
		var i = 0,
		ids = [],
		children = holder.children('.item');
		children.each(function(){
			t = $(this);
			ids[i] = $(this).data('id');
			i++;
		})
		return ids;
	}
	$('.gallery-wolfie').each(function(){
		var control = $(this).closest('.wolfie-form-control')
		var holder = control.find('.images-holder')
		var add = control.find('.add')
		var remove = control.find('.remove')
		var input = control.find('input')
		add.off().click(function(e){
			e.preventDefault();
			custom_uploader = wp.media({
				title: 'Insert images',
				library : {
					type : 'image'
				},
				button: {
				text: 'Use this images' // button label text
			},
			multiple: true // for multiple images selection set to true
		}).open().on('select', function() { // it also has "open" and "close" events 
		var currentIds = input.val().split(',').map(Number);
		var attachment = custom_uploader.state().get('selection').first().toJSON();
		/* if you sen multiple to true, here is some code for getting the images IDs */
		var attachments = custom_uploader.state().get('selection'),
		galleryImages,
		attachment_ids = new Array(),
		attachment_urls = new Array(),
		i = 0;
		attachments.each(function(attachment) {
			var thumbUrl = attachment.attributes.sizes.thumbnail;
			var url = attachment.attributes['url'];
			if(thumbUrl) {
				url = thumbUrl['url'];
			}
			attachment_ids[i] = attachment['id'];
			attachment_urls[i] = url;
			i++;
			control.find('.images-holder').append('<div class="item" data-id="'+attachment['id']+'"><a href="#" class="wolfie-close"></a><img style="width:100px;height:100px;object-fit:cover" src="'+url+'"></div>');
		});
		var idsArr = currentIds.concat(attachment_ids);
		input.val(idsArr);
	});
	});
		remove.click(function(e){
			e.preventDefault();
			holder.html('');
			input.val('');
		})
		holder.sortable({
			placeholder: 'wolfie-drop-placeholder'
		}).on( "sortstop", function( event, ui ) {
			var ids = getIds(holder);
			input.val(ids);
		});
	});
	$('body').off().on('click', '.wolfie-close', function(e){
		e.preventDefault();
		var t = $(this);
		var id = t.parent().data('id');
		var input = t.closest('.wolfie-form-control').find('input');
		val = input.val();
		var newVal = val.replace(id, '')
		newVal = commaCorrection(newVal);
		input.val(newVal);
		var id = t.parent().remove();
	})
});