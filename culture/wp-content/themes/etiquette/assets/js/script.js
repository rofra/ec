/* Author:

*/

var Etiquette = {};

jQuery(document).ready(function()
{
	// init special edition galleries
	if (jQuery('.gallery_main').length > 0)
	{
		jQuery('.gallery_main').each(function()
		{
			if (jQuery(this).find('li').length > 1)
			{
				jQuery(this).cycle({
					after: function() {  },
				    pager: '#' + jQuery(this).siblings('.gallery_nav').attr('id'),
				    timeout: 0,
				    speed: 300
				});
				jQuery(this).siblings('.gallery_nav').find('a').last().addClass('last');
			}
		});
	}

});