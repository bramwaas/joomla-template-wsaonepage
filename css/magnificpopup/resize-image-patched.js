		resizeImage: function() {
			var item = mfp.currItem;
			if(!item || !item.img) return;

			if(mfp.st.image.verticalFit) {
				var decr = 0;
				// fix box-sizing in ie7/8
				if(mfp.isLowIE) {
					decr = parseInt(item.img.css('padding-top'), 10) + parseInt(item.img.css('padding-bottom'),10);
				}
// added ivm margins outside image. BW 20160402 
// v1.1.0
//				item.img.css('max-height', mfp.wH-decr);
// v1.1.0 end
// patched
				item.img.css('max-height', mfp.wH-decr - parseInt(item.img.css('margin-top'), 10) - parseInt(item.img.css('margin-bottom'),10));
//patched end.				
			}
		},
