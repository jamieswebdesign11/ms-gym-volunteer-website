(function ($) {

    //Check if IE Function
    function isIE() {
        ua = navigator.userAgent;
        /* MSIE used to detect old browsers and Trident used to newer ones*/
        var is_ie = ua.indexOf("MSIE ") > -1 || ua.indexOf("Trident/") > -1;
        return is_ie;
    }
    //Sanatize String Function
    function sanitize(str) {
        var sani = str.replace(/[^A-Z0-9]/ig, " "); // Removes Special Charaters with Spaces 
        sani = sani.replace(/^(\s*)([\W\w]*)(\b\s*$)/g, '$2');
        return sani.replace(/\b[a-z]/gi, function (char) { // Caps First leter From Words 
            return char.toUpperCase();
        });
    }
    //Add Alt/Title tags to links that don't have them
    $('a').each(function () {
        if ($(this).attr('title') === undefined && $(this).attr('alt') === undefined) {
            var linkTxt = $(this).text();
            if (linkTxt.length >= 2) {
                $(this).attr({
                    title: linkTxt,
                    alt: linkTxt
                });
            } else {
                var linkLocation = $(this).attr('href');
                if (linkLocation == undefined) {} else {
                    var sanitized = sanitize(linkLocation);
                    $(this).attr({
                        title: sanitized,
                        alt: sanitized
                    });
                }
            }
        } else {
            var title = $(this).attr('title');
            var alt = $(this).attr('alt');
            switch (title) {
                case undefined:
                    $(this).attr({
                        title: alt
                    });
                    break;
            }
            switch (alt) {
                case undefined:
                    $(this).attr({
                        alt: title
                    });
                    break;
            }
        }
    });

    // Scroll To Code 
    $(window).on("load", function () {
        if (location.hash) {
            scrollPageToAnchor(window.location.hash);
        }
        $('a').click(function (e) {
            if ($(this).attr("href") != window.location.href && $(this)[0].host + $(this)[0].pathname == window.location.host + window.location.pathname && $(this).attr('data-toggle') == undefined) {
                e.preventDefault();
                scrollPageToAnchor($(this)[0].hash);
                window.location.hash = $(this)[0].hash;
                var noHashURL = window.location.href.replace(/#.*$/, '');
                window.history.replaceState('', document.title, noHashURL);
            }
        });

        function scrollPageToAnchor(anchor) {
            anchor = anchor.replace('/', '');
            var anchorMarginPadding = $(anchor).outerHeight(true) - $(anchor).innerHeight();
            var scrollDuration = 1000;
            $('html, body').animate({
                scrollTop: $(anchor).offset().top - anchorMarginPadding
            }, scrollDuration);
        }
    });


    //Banner Scripts 
    $(document).ready(function () { 
        //On Load Code 
        if ($(window).outerWidth() > 991) { 
            $('[data-parallax]').attr('data-parallax', 'True'); 
            $('[data-height]').css('height', '700px'); 
            $('.slider-img').each(function () { 
                var imgSrc = $(this).attr('src'); 
                $(this).parent().parent().css({ 
                    'background': 'transparent url("' + imgSrc + '") fixed no-repeat center center/cover', 
                }); 
                $(this).css('display', 'none'); 
                $(this).parent().parent().addClass('parallax-scroll'); 
            }); 
        } 
        //Resize Banner Code 
        $(window).resize(function () { 
            var windowWidth = $(window).outerWidth(); 
            if (windowWidth < 992) { 
                $('[data-parallax]').attr('data-parallax', 'False'); 
                $('[data-height]').css('height', 'auto');
                $('.slider-img').each(function () { 
                    $(this).css('display', 'block'); 
                    $(this).parent().parent().removeClass('parallax-scroll'); 
                }); 
            } else { 
                $('[data-parallax]').attr('data-parallax', 'True'); 
                $('[data-height]').css('height', '700px');  
                $('.slider-img').each(function () { 
                    var imgSrc = $(this).attr('src'); 
                    $(this).parent().parent().css({ 
                        'background': 'transparent url("' + imgSrc + '") fixed no-repeat center center/cover', 
                    }); 
                    $(this).css('display', 'none'); 
                    $(this).parent().parent().addClass('parallax-scroll'); 
                }); 
            }
            
        }); 
        //Parallax Code 
        $(window).on('load scroll', function () { 
            var scrolled = $(this).scrollTop(); 
            $('.parallax-scroll').css('transform', 'translate3d(0, ' + -(scrolled * 0.1) + 'px, 0)'); 
        }); 
    });
    
    //mobile nav toggle
    $('.mobile-nav .fa-bars').click(function(){
        $('#menu-main-menu').toggle();
    });

    //carousel
    $(".testimonials-inner").slick({
        dots: true,
        infinite: true,
        arrows: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 6000,
    });

    //sticky header code
    $(document).ready(function () {
        headerHeight = $('#header').outerHeight();
        $('#banner').css('top', headerHeight);
        $('#interior-banner').css('top', headerHeight);
        $('.page-content').css('top', headerHeight);
        $('#footer').css('top', headerHeight);

        $(window).on("scroll touchmove", function () {
            $('#header').toggleClass('scrolled', $(document).scrollTop() > 0);
        });
    });

    //join form popup functions
    $('.CTA:first-of-type a').click(function(){
        $('.join-form-popup').addClass('active');
    });

    $('.join-form-popup--close').click(function(){
        $('.join-form-popup').removeClass('active');
    });

    $('.af-submit-button').addClass('btn');

    //iframe styling
    $('.blog-page iframe').parent('p').addClass('blog-iframe-container');
    $('.profile-blog-extras-content-box iframe').parent('p').addClass('profile-blog-extras-iframe-container');

    //light gallery
    $('.gallery-video').each(function(){
        var videoSrc = $(this).attr('data-src');

        function getYouTubeVideoId(url) {
            var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            var match = url.match(regExp);
            return (match && match[7].length == 11) ? match[7] : false;
        }

        var videoID = getYouTubeVideoId(videoSrc);

        //$(this).attr('data-poster', 'https://img.youtube.com/vi/'+videoID+'/0.jpg');
        $(this).children('img').attr('src', 'https://img.youtube.com/vi/'+videoID+'/mqdefault.jpg');
    });
    
    $(document).ready(function() {
        var elements = document.getElementsByClassName('light-gallery');
        for (let item of elements) {
            lightGallery(item, {
                plugins: [lgVideo],
                loadYouTubePoster: false,
                autoplayFirstVideo: false,
                youTubePlayerParams: {
                    mute: 0
                },
                share:false,
                rotate: false,
                download: false,
                subHtmlSelectorRelative: true
            })
        }
        
    });



})(jQuery);
