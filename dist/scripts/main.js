(function($) {
    var Theme = {
        common: {
            init: function() {
                var screen_xs_min = 480;
                var screen_sm_min = 768;
                var screen_md_min = 992;
                var screen_lg_min = 1200;
                if (typeof $.fn.datepicker === "function") {
                    $.datepicker.setDefaults($.datepicker.regional.de);
                }
                if ($("body.single-post, body.page").length && !$(".page-overview").length && typeof $.fn.lightGallery === "function") {
                    var imageSelector = [];
                    var extensions = [ "jpg", "jpeg", "png", "gif", "bmp", "svg" ];
                    for (var i = 0; i < extensions.length; i++) {
                        imageSelector.push('figure:not(".author__image") > a[href*=".' + extensions[i] + '"]');
                    }
                    $(".article").lightGallery({
                        thumbnail: true,
                        showThumbByDefault: false,
                        selector: imageSelector.join(",")
                    });
                }
                var adjustMainPadding = function() {
                    $("main").css("padding-top", $(".site-header").outerHeight());
                };
                adjustMainPadding();
                var formTopPosition = function() {
                    $(".gform_anchor").css("top", -$(".site-header").outerHeight());
                };
                formTopPosition();
                $(window).on("resize", function() {
                    adjustMainPadding();
                    formTopPosition();
                    if ($(".site-header").hasClass("site-header__expanded")) {
                        $(".site-header__bottom").css("max-height", $(".site-header").height() - $(".site-header__top").outerHeight() - $(".search-form").outerHeight(true) - $(".social-buttons").outerHeight(true));
                    }
                    if ($(window).width() < 1200 && !$(".site-header").hasClass("site-header__expanded")) {
                        $(".search-form").css("display", "none");
                    }
                });
                $(window).on("scroll", function() {
                    var $siteHeader = $(".site-header");
                    if (window.scrollY > 0) {
                        if (!$siteHeader.hasClass("headroom") && $(window).width() > 1200) {
                            $siteHeader.headroom({
                                offset: 45,
                                tolerance: 0
                            });
                        }
                        if ($(window).width() > 1200) {
                            $("main").css("padding-top", "221px");
                        }
                    }
                });
                $(document).on("click", 'a[href^="#"]', function(e) {
                    e.preventDefault();
                    if ($(this).closest("#cookieChoiceInfo").length === 1) {
                        return;
                    }
                    var elHref = $.attr(this, "href");
                    var topPosition = elHref === "#" ? 0 : $(elHref).offset().top - 68;
                    $("html, body").animate({
                        scrollTop: topPosition
                    }, 500);
                });
                $(window).on("resize load", function() {
                    if ($(window).width() < screen_lg_min) {
                        $(".nav-button").on("click", function(e) {
                            $(this).toggleClass("nav-button--x");
                            $("html").toggleClass("no-scroll");
                            $(".site-logo").toggleClass("move-out");
                            $(".site-header").toggleClass("site-header__expanded");
                            $(".nav-primary, .nav-secondary, .search-form, .social-buttons").toggleClass("move-in");
                            $(".nav-primary").css("height", $(".site-header").height() - $(".nav-secondary").outerHeight() - $(".search-form").outerHeight() - $(".social-buttons").outerHeight(true));
                            $("main").css("padding-top", 60);
                        });
                    }
                });
                $(".nav-primary .menu > li").on("mouseenter", function() {
                    clearTimeout(window.navigationTimeout);
                    $(".nav-primary .menu > li").each(function() {
                        $(this).removeClass("hover");
                    });
                    $(this).addClass("hover");
                });
                $(".nav-primary .menu > li").on("mouseleave", function() {
                    window.navigationTimeout = setTimeout(function() {
                        $(this).removeClass("hover");
                    }.bind(this), 500);
                });
                function initMainNavigation(container) {
                    var dropdownToggle = $("<button />", {
                        "class": "nav-toggle",
                        "aria-expanded": false
                    }).append($("<span />", {
                        "class": "sr-only",
                        text: screenReaderText.expand
                    }));
                    container.find(".menu-item-has-children:has(ul) > a").after(dropdownToggle);
                    container.find(".menu-item-has-children:has(ul)").attr("aria-haspopup", "true");
                    container.find(".nav-toggle").click(function(e) {
                        var _this = $(this), screenReaderSpan = _this.find(".sr-only"), expand = _this.attr("aria-expanded") === "false";
                        e.preventDefault();
                        var browserWidth = $(window).width() < 1200;
                        if (_this.parent().parent().parent()[0].nodeName !== "NAV" || browserWidth) {
                            _this.toggleClass("expanded", expand);
                            if (expand) {
                                _this.next(".children, .sub-menu").stop(true, false).slideDown(200);
                            } else {
                                _this.next(".children, .sub-menu").stop(true, false).slideUp(200);
                            }
                            _this.parent().toggleClass("expanded", expand);
                            _this.attr("aria-expanded", expand ? "true" : "false");
                            if (_this.parent().parent().parent()[0].nodeName !== "NAV" && !browserWidth) {
                                var expandedElements = $(".nav-primary .nav-toggle.expanded").not(_this).not(_this.parents(".menu-item").children(".nav-toggle.expanded"));
                                expandedElements.each(function(iterationCount) {
                                    $(this).toggleClass("expanded", false);
                                    $(this).next(".children, .sub-menu").stop(true, false).slideUp(200);
                                    $(this).parent().toggleClass("expanded", false);
                                    $(this).attr("aria-expanded", "false");
                                });
                            }
                            screenReaderSpan.text(screenReaderSpan.text() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand);
                        }
                    });
                }
                initMainNavigation($(".nav-primary"));
                var searchElements = $(".search-form");
                $(document).on("click", function(e) {
                    if ($(e.target).hasClass("search-button") && $(".search-form__field").val() !== "" && searchElements.is(":visible")) {
                        $(".search-form").submit();
                    } else if ($(e.target).hasClass("search-button")) {
                        searchElements.slideToggle(200, function() {
                            $(".search-form input").focus();
                        });
                    } else if ($(window).width() > 1200 && !$(e.target).hasClass("search-form__field") && $(".search-form__field").val() === "" && searchElements.is(":visible")) {
                        e.preventDefault();
                        searchElements.slideToggle(200);
                    } else if ($(e.target).hasClass("icon-close")) {
                        searchElements.slideToggle(200);
                    }
                });
                var navSecondaryWidth = 0;
                $(".nav-secondary > ul > li").each(function(listIndex) {
                    navSecondaryWidth += $(this).width();
                });
                $(document).on("click", ".site-header__top .scroll-button--right, .site-header__top .scroll-button--left", function(e) {
                    e.preventDefault();
                    var direction = $(this).attr("class").split("--")[1];
                    $(".site-header__top .nav-secondary").animate({
                        scrollLeft: direction === "right" ? navSecondaryWidth - $(".nav-secondary").width() + 40 : 0
                    });
                });
                var metaNaviWrapper = $(".site-header__top .container");
                $(".site-header__top .nav-secondary").on("scroll", function(e) {
                    if ($(e.target).scrollLeft() <= 20) {
                        metaNaviWrapper.find(".scroll-button--left").fadeOut();
                    } else {
                        metaNaviWrapper.find(".scroll-button--left").fadeIn();
                    }
                    if ($(e.target).scrollLeft() >= navSecondaryWidth - $(".nav-secondary").width()) {
                        metaNaviWrapper.find(".scroll-button--right").fadeOut();
                    } else {
                        metaNaviWrapper.find(".scroll-button--right").fadeIn();
                    }
                });
                $(".site-header__top .nav-secondary").trigger("scroll");
                $(window).on("resize", function() {
                    $(".site-header__top .nav-secondary").trigger("scroll");
                });
                var $allIframes = $(".article iframe").each(function() {
                    $(this).data("aspectRatio", this.height / this.width).removeAttr("height").removeAttr("width");
                });
                $(window).on("resize.iframe", function() {
                    $allIframes.each(function() {
                        var newWidth = $(this).parent().width();
                        $(this).width(newWidth).height(newWidth * $(this).data("aspectRatio"));
                    });
                }).triggerHandler("resize.iframe");
                $("#ga-optout").on("change", function() {
                    gaOptOut(this.checked);
                });
            },
            finalize: function() {}
        }
    };
    var UTIL = {
        fire: function(func, funcname, args) {
            var fire;
            var namespace = Theme;
            funcname = funcname === undefined ? "init" : funcname;
            fire = func !== "";
            fire = fire && namespace[func];
            fire = fire && typeof namespace[func][funcname] === "function";
            if (fire) {
                namespace[func][funcname](args);
            }
        },
        loadEvents: function() {
            UTIL.fire("common");
            $.each(document.body.className.replace(/-/g, "_").split(/\s+/), function(i, classnm) {
                UTIL.fire(classnm);
                UTIL.fire(classnm, "finalize");
            });
            UTIL.fire("common", "finalize");
        }
    };
    $(document).ready(UTIL.loadEvents);
})(jQuery);
//# sourceMappingURL=main.js.map
