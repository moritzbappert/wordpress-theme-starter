(function($) {
  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Theme = {
    // All pages
    'common': {
      init: function() {
        // CSS media query breakpoints
        var screen_xs_min = 480;
        var screen_sm_min = 768;
        var screen_md_min = 992;
        var screen_lg_min = 1200;

        if (typeof $.fn.datepicker === 'function') {
          $.datepicker.setDefaults($.datepicker.regional.de);
        }

        if ($('body.single-post, body.page').length && !$('.page-overview').length && typeof $.fn.lightGallery === 'function') {
          // Prevent opening non-images in a lightbox.
          var imageSelector = [];
          var extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
          for (var i = 0; i < extensions.length; i++) {
            imageSelector.push('figure:not(".author__image") > a[href*=".' + extensions[i] + '"]');
          }
          $('.article').lightGallery({
            thumbnail: true,
            showThumbByDefault: false,
            selector: imageSelector.join(',')
          });
        }

        var adjustMainPadding = function () {
          // Set the height of header as padding-top from main.
          $('main').css('padding-top', $('.site-header').outerHeight());
        };
        adjustMainPadding();

        var formTopPosition = function () {
          // Set the height of header as top for webforms anchor.
          $('.gform_anchor').css('top', - $('.site-header').outerHeight());
        };
        formTopPosition();

        $(window).on('resize', function() {
          adjustMainPadding();
          formTopPosition();
          // Adjust site-header__bottom max height on resize.
          if ($('.site-header').hasClass('site-header__expanded')) {
            $('.site-header__bottom').css('max-height', $('.site-header').height() - $('.site-header__top').outerHeight() - $('.search-form').outerHeight(true) - $('.social-buttons').outerHeight(true));
          }

          if ($(window).width() < 1200 && !$('.site-header').hasClass('site-header__expanded')) {
            // Hide search form when switch to mobile layout and mobile
            // navigation is not open.
            $('.search-form').css('display', 'none');
          }
        });

        $(window).on('scroll', function () {
          var $siteHeader = $('.site-header');
          if (window.scrollY > 0) {
            if (!$siteHeader.hasClass('headroom') && $(window).width() > 1200) {
              // Added headroom.js to header.
              $siteHeader.headroom({
                offset: 45,
                tolerance: 0,
              });
            }
            // Fallback for page reload on scrolled position.
            if ($(window).width() > 1200) {
              $('main').css('padding-top', '221px');
            }
          }
        });

        // Anchor offset and smooth scrolling.
        $(document).on('click', 'a[href^="#"]', function (e) {
          e.preventDefault();
          if ($(this).closest('#cookieChoiceInfo').length === 1) { return; }
          var elHref = $.attr(this, 'href');
          var topPosition = (elHref === '#') ? 0 : $(elHref).offset().top - 68;
          $('html, body').animate({
            scrollTop: topPosition
          }, 500);
        });

        // Toggle mobile navigation.
        $(window).on('resize load', function () {
          if ($(window).width() < screen_lg_min) {
            $('.nav-button').on('click', function (e) {

              // Change burger button to close button.
              $(this).toggleClass('nav-button--x');

              // Prevent scrolling when mobile menu is shown.
              $('html').toggleClass('no-scroll');

              // Move logo out of viewport
              $('.site-logo').toggleClass('move-out');

              // Toggle class to site-header to adjust styling
              $('.site-header').toggleClass('site-header__expanded');

              // Move elements into viewport
              $('.nav-primary, .nav-secondary, .search-form, .social-buttons').toggleClass('move-in');

              // Set height for nav-primary to prevent it to move out the
              //   social media buttons from the viewport.
              $('.nav-primary').css('height', $('.site-header').height() - $('.nav-secondary').outerHeight() - $('.search-form').outerHeight() - $('.social-buttons').outerHeight(true));

              // Adjust main padding-top to fix wrong value when switch from
              //   mobile landscape view.
              $('main').css('padding-top', 60);
            });
          }
        });

        // Add functions to main-navigation to show submenu even after mouseleave
        $('.nav-primary .menu > li').on('mouseenter', function () {
          clearTimeout(window.navigationTimeout);
          $('.nav-primary .menu > li').each(function () {
            $(this).removeClass('hover');
          });
          $(this).addClass('hover');
        });

        $('.nav-primary .menu > li').on('mouseleave', function () {
          window.navigationTimeout = setTimeout(function () {
            $(this).removeClass('hover');
          }.bind(this), 500);
        });

        // Primary Navigation: copied from Twentysixteen.
        function initMainNavigation(container) {
          // Add dropdown toggle that displays child menu items.
          var dropdownToggle = $('<button />', {
            'class': 'nav-toggle',
            'aria-expanded': false
          }).append($( '<span />', {
            'class': 'sr-only',
            text: screenReaderText.expand
          }));

          container.find('.menu-item-has-children:has(ul) > a').after(dropdownToggle);

          // Toggle buttons and submenu items with active children menu items.
          // container.find('.current-menu-ancestor > button').addClass('expanded');
          // container.find('.current-menu-ancestor > .sub-menu').addClass('expanded');

          // Add menu items with submenus to aria-haspopup="true".
          container.find('.menu-item-has-children:has(ul)').attr('aria-haspopup', 'true');

          container.find('.nav-toggle').click(function(e) {
            var _this        = $(this),
            screenReaderSpan = _this.find('.sr-only'),
            expand = _this.attr('aria-expanded') === 'false';
            e.preventDefault();

            var browserWidth = ($(window).width() < 1200);
            if (_this.parent().parent().parent()[0].nodeName !== 'NAV' || browserWidth) {
              _this.toggleClass('expanded', expand);
              if (expand) {
                _this.next('.children, .sub-menu').stop(true, false).slideDown(200);
              }
              else {
                _this.next('.children, .sub-menu').stop(true, false).slideUp(200);
              }
              _this.parent().toggleClass('expanded', expand);
              _this.attr('aria-expanded', expand ? 'true' : 'false');

              // If not mobile, always close all expended navigation-elements,
              // because the current scroll position could jump when toggling an
              // item below the currently expanded one.
              if (_this.parent().parent().parent()[0].nodeName !== 'NAV' && !browserWidth) {
                // Fold all the expanded elements except all buttons that are
                // parents of the clicked item.
                var expandedElements = $('.nav-primary .nav-toggle.expanded').not(_this).not(_this.parents('.menu-item').children('.nav-toggle.expanded'));
                expandedElements.each(function (iterationCount) {
                  $(this).toggleClass('expanded', false);
                  $(this).next('.children, .sub-menu').stop(true, false).slideUp(200);
                  $(this).parent().toggleClass('expanded', false);
                  $(this).attr('aria-expanded', 'false');
                });
              }

              // jscs:disable
              // jscs:enable
              screenReaderSpan.text(screenReaderSpan.text() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand);
            }
          });
        }
        initMainNavigation($('.nav-primary'));

        // Toggle search on click.
        var searchElements = $('.search-form');
        $(document).on('click', function (e) {
          if ($(e.target).hasClass('search-button') && $('.search-form__field').val() !== '' && searchElements.is(':visible')) {
            $('.search-form').submit();
          }
          else if ($(e.target).hasClass('search-button')) {
            searchElements.slideToggle(200, function() {
              $('.search-form input').focus();
            });
          }
          else if ($(window).width() > 1200 && !$(e.target).hasClass('search-form__field') && $('.search-form__field').val() === '' && searchElements.is(':visible')) {
            e.preventDefault();
            searchElements.slideToggle(200);
          }
          else if ($(e.target).hasClass('icon-close')) {
            searchElements.slideToggle(200);
          }
        });

        // Calculate width of the secondary-navigation to get to know the maximum
        // scrolling position.
        var navSecondaryWidth = 0;
        $('.nav-secondary > ul > li').each(function (listIndex) {
          navSecondaryWidth += $(this).width();
        });

        // Add event to vertically scroll the secondary nav.
        $(document).on('click', '.site-header__top .scroll-button--right, .site-header__top .scroll-button--left', function (e) {
          e.preventDefault();
          var direction = $(this).attr('class').split('--')[1];
          $('.site-header__top .nav-secondary').animate({
            scrollLeft: direction === 'right' ? navSecondaryWidth - $('.nav-secondary').width() + 40 : 0
          });
        });

        // Add scroll-function to show/hide left- and right-buttons in the
        // secondary navigation and trigger the event initially once.
        var metaNaviWrapper = $('.site-header__top .container');
        $('.site-header__top .nav-secondary').on('scroll', function (e){
          if ($(e.target).scrollLeft() <= 20) {
            metaNaviWrapper.find('.scroll-button--left').fadeOut();
          }
          else {
            metaNaviWrapper.find('.scroll-button--left').fadeIn();
          }

          if ($(e.target).scrollLeft() >= navSecondaryWidth - $('.nav-secondary').width()) {
            metaNaviWrapper.find('.scroll-button--right').fadeOut();
          }
          else {
            metaNaviWrapper.find('.scroll-button--right').fadeIn();
          }
        });
        $('.site-header__top .nav-secondary').trigger('scroll');

        // Trigger function to show/hide left- and right-buttons on resize.
        $(window).on('resize', function (){
          $('.site-header__top .nav-secondary').trigger('scroll');
        });

        // Expand iframes in article content to consume the full available width
        // respecting aspect ratio, also upon change of viewport.
        var $allIframes = $('.article iframe').each(function () {
          $(this)
            .data('aspectRatio', this.height / this.width)
            .removeAttr('height')
            .removeAttr('width');
        });
        $(window).on('resize.iframe', function () {
          $allIframes.each(function () {
            var newWidth = $(this).parent().width();
            $(this).width(newWidth).height(newWidth * $(this).data('aspectRatio'));
          });
        }).triggerHandler('resize.iframe');

        // Event handler for opt-out on data privacy policy page.
        $('#ga-optout').on('change', function() {
          gaOptOut(this.checked);
        });
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Theme;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  $(document).ready(UTIL.loadEvents);

})(jQuery);
