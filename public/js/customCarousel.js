$('#carouselExampleControls').on('slide.bs.carousel', function (e) {

    var $e = $(e.relatedTarget);
    //index élément courant
    var idx = $e.index();
    //nombre d'item par slide 
    var itemsPerSlide = 6;
    //récupère le nombre d'items total
    var totalItems = $('.carousel-item').length;
    
    if (idx >= totalItems-(itemsPerSlide-1)) {
        var it = itemsPerSlide - (totalItems - idx);
        for (var i=0; i<it; i++) {
            // append slides to end
            if (e.direction=="left") {
                $('.carousel-item').eq(i).appendTo('.carousel-inner');
            }
            else {
                $('.carousel-item').eq(0).appendTo('.carousel-inner');
            }
        }
    }
});