function slideshowheight(){
    var slideshow = jQuery('.flexslideshow');
    var heightunits = slideshow.data('height-units');
    var slideshowwidth = slideshow.width();

    slideshow.each(function() {
        var heightunits = jQuery(this).data('height-units');
        if (heightunits == '%'){
            var slideshowheight = jQuery(this).data('slideshow-height');
            jQuery(this).find('.slides .slideshow-item > div').css('height', jQuery(this).width() * slideshowheight / 100);
        }
                
    });
}

jQuery(document).ready(function() {
    slideshowheight();
    new ResizeSensor(jQuery('.flexslideshow .slides .slideshow-item'), function(){ 
        slideshowheight();
    });
});
