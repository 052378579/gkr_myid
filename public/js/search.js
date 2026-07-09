$(document).ready(function() {
    $("[data-fancybox]").fancybox({
        buttons: ["zoom", "slideShow", "fullScreen", "thumbs", "close"]
    });
});

$(window).on("load", function() {
    if ($('.masonry-grid').length) {
        $('.masonry-grid').masonry({
            itemSelector: '.grid-item',
            columnWidth: 200,
            gutter: 15
        });
    }
});

function updateLinkCount(id) {
    fetch(window.AppConfig.apiUpdateLinkCount, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    });
}

function updateImageCount(id) {
    fetch(window.AppConfig.apiUpdateImageCount, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    });
}

function setBroken(imgElement, src) {
    imgElement.style.display = 'none';
    fetch(window.AppConfig.apiSetBroken, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'src=' + encodeURIComponent(src)
    }).then(() => {
        if ($('.masonry-grid').length) {
            $('.masonry-grid').masonry('layout');
        }
    });
}

$(window).scroll(function() {
    if ($(window).scrollTop() > 10) {
        $('#header-container').addClass('scrolled');
    } else {
        $('#header-container').removeClass('scrolled');
    }
});
