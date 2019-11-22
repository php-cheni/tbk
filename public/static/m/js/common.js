$(function() {
    $(".header_button").click(function() {
        $("body").toggleClass("nav_bodyer");
        $(".nav_content").addClass("ccur");
    })
    $(".bodyer").click(function() {
        $(".nav_content").removeClass("ccur");
        $("body").removeClass("nav_bodyer");
    })
    $(".header_logo").click(function() {
        $(".header_logo").addClass("current");
        setTimeout(function() {
            window.location.href = "/touch/";
        }, 300)
    })

    $("a,.sca").on("click", function() {
        var _this = $(this);
        _this.addClass("scale1");
        setTimeout(function() {
            _this.removeClass("scale1");
        }, 150)
    })

})