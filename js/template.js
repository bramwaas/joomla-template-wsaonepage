/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 * bw 25-3-2016 aangevuld met script om mootools-more .. bootstrap 3 (jquery) conflict op te lossen voor dropdowns.
 */

(function($)
{
	$(document).ready(function()
	{
		$('*[rel=tooltip]').tooltip()

		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function()
		{
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		$(".btn-group input[checked=checked]").each(function()
		{
			if ($(this).val() == '') {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			} else if ($(this).val() == 0) {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
			}
		});
	})
})(jQuery);
/*
// conflict mootools-more met bootstrap 3 oplossen
// zie http://forum.joomla.org/viewtopic.php?f=713&t=841311
// en http://phproberto.com/es/37-fix-mootools-conflicts-in-bootstrap-joomla-templates
//
   if (MooTools != undefined) {
      var mHide = Element.prototype.hide;
      var mShow = Element.prototype.show;
      var mSlide = Element.prototype.slide;
      Element.implement({
         hide: function() {
            {
               return this;
            }
            mHide.apply(this, arguments);
         },
         show: function (v) {
            {
               return this;
            }
            mShow.apply(this, arguments);
         },
         slide: function (v) {
            {
               return this;
            }
            mSlide.apply(this, arguments);
         }

      });
   }

*/


