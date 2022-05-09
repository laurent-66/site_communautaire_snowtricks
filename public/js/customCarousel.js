$('#carouselExample').on('slide.bs.carousel', function (e) {

    var $e = $(e.relatedTarget);
    var idx = $e.index();
    var itemsPerSlide = 6;
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


// const carouselExample = document.querySelector('#carouselExample');
// const carouselItem = dosument.querySelector('.carousel-item');

// carouselExample.addEventListener('slide.bs.carousel', (e) => {
//     let e = e.relatedTarget;
//     let idx = $e.index();
//     let itemsPerSlide = 6;
//     let totalItems = carouselItem.length;

//     if(idx >= totalItems-(itemsPerSlide-1)) {
//         let it = itemsPerSlide - (totalItems - idx);

//         for (let i=0; i<it; i++) {
//             // append slides to end
//             if (e.direction=="left") {

//                 carouselItem
//                 $('.carousel-item').eq(i).appendTo('.carousel-inner');
//             }
//             else {
//                 carouselItem
//                 $('.carousel-item').eq(0).appendTo('.carousel-inner');
//             }
//         }
//     }
// })