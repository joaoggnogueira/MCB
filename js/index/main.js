$(document).ready(function () {
    $('a.linkpage').bind('click.smoothscroll', function (e) {
        e.preventDefault();
        var target = this.hash,
                $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': ($target.offset().top - 200)
        }, 400, 'swing', function () {
            window.location.hash = target;
            $('html, body').scrollTop($target.offset().top - 200);
            $target.addClass("hightlight");
            setTimeout(function () {
                $target.removeClass("hightlight");
            }, 2000);
        });
    });

    $(".card a.details").click(function (e) {
        e.preventDefault();
        var target = this.hash,
                $target = $(target);
        swal({html: $target.html()});
    });

});