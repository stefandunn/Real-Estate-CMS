/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$current_url = window.location.href.split('#')[0].split('?')[0],
$body = $('body'),
$menu_toggle = $('#menu_toggle'),
$sidebat_menu = $('#sidebar-menu'),
$sidebar_footer = $('.sidebar-footer'),
$left_col = $('.left_col'),
$right_col = $('.right_col'),
$nav_menu = $('.nav_menu'),
$footer = $('footer');

$(document).ready(function() {
    // Sidebar
    var setContentHeight = function () {
        // reset height
        $right_col.css('min-height', $(window).height());

        var bodyHeight = $body.outerHeight(),
            footerHeight = $body.hasClass('footer_fixed') ? -10 : $footer.height(),
            leftColHeight = $left_col.eq(1).height() + $sidebar_footer.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $nav_menu.height() + footerHeight;

        $right_col.css('min-height', contentHeight);
    };

    $sidebat_menu.find('a').on('click', function(ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $sidebat_menu.find('li').removeClass('active active-sm');
                $sidebat_menu.find('li ul').slideUp();
            }
            
            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $menu_toggle.on('click', function() {
        if ($body.hasClass('nav-md')) {
            $sidebat_menu.find('li.active ul').hide();
            $sidebat_menu.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $sidebat_menu.find('li.active-sm ul').show();
            $sidebat_menu.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $body.toggleClass('nav-md nav-sm');

        setContentHeight();
    });

    // check active menu
    $sidebat_menu.find('a[href="' + $current_url + '"]').parent('li').addClass('current-page');

    $sidebat_menu.find('a').filter(function () {
        return this.href == $current_url;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function(){  
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel:{ preventDefault: true }
        });
    }
    // /Sidebar

    // Progressbar
    if ($(".progress .progress-bar")[0])
        $('.progress .progress-bar').progressbar();
    // /Progressbar


    // Table
    var checkState = '';
    $('table input').on('ifChecked', function () {
        checkState = '';
        $(this).parent().parent().parent().addClass('selected');
        countChecked();
    });
    $('table input').on('ifUnchecked', function () {
        checkState = '';
        $(this).parent().parent().parent().removeClass('selected');
        countChecked();
    });
    // /Table


    // Accordion
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
    // /Accordion
});