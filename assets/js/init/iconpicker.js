jQuery(document).ready(function($){ 
var iconList = wolf.icons;
var args = {
    theme              : 'fip-darkgrey',              // The CSS theme to use with this fontIconPicker. You can set different themes on multiple elements on the same page
    source             : iconList,                   // Icons source (array|false|object)
    searchSource       : false,                   // map source with text to search
    emptyIcon          : true,                    // Empty icon should be shown?
    emptyIconValue     : '',                      // The value of the empty icon, change if you select has something else, say "none"
    autoClose          : true,                    // Whether or not to close the FIP automatically when clicked outside
    iconsPerPage       : 20,                      // Number of icons per page
    hasSearch          : true,                    // Is search enabled?
    // jQuery objects
    useAttribute       : false,                   // Whether to use attribute selector for printing icons
    attributeName      : 'data-icon',             // HTML Attribute name
    convertToHex       : false,                    // Whether or not to convert to hexadecimal for attribute value. If true then    
    searchPlaceholder  : 'Search Icons'           // Placeholder for the search input
}
var $picker = $('.icon-picker').fontIconPicker(args);
function rebuildPicker(){
	var t = $(this);
	$picker.destroyPicker();
	setTimeout(function(){
		$('.icon-picker').fontIconPicker(args);
	},200)
}
// $('.wolfie-group').off().on('click', '.wolfie-add', rebuildPicker)


});