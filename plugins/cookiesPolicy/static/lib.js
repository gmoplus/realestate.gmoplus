
/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: RLCOOKIESPOLICY.CLASS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

$(function () {
    if (cpConfigs.showCookieNotice === true) {
        $('div#cookies_policy_big_form').fadeIn('normal');
        $('div.cookies_policy_icon').before('<div class="cookies_absolute_div hide" id="modal_mask" ></div>');
        $('div.cookies_absolute_div').fadeIn('normal');
        $('html, body').addClass('CP_lock_screen');
    }

    $('.cookies_policy_icon').click(function () {
        $('div#cookies_policy_big_form').fadeIn('normal');
        $('div.cookies_policy_icon').before('<div class="cookies_absolute_div hide" id="modal_mask" ></div>');
        $('div.cookies_absolute_div').fadeIn('normal');
        $('html, body').addClass('CP_lock_screen');

        if (cpConfigs.showCookieNotice === false) {
            $('div.cookies_absolute_div').click(function () {
                $(this).fadeOut('normal');
                $(this).remove();
                $('div#cookies_policy_big_form').fadeOut('normal');
                $('html, body').removeClass('CP_lock_screen');
            })
        }
    });

    enquire.register("screen and (max-width: 767px)", {
        match: function () {
            $(window).resize(function () {
                var box_height    = $('div#cookies_policy_big_form').height() / 2;
                var move_from_top = $(window).height() / 2 - box_height;

                $('div#cookies_policy_big_form').css({'top': move_from_top, 'bottom': 'auto'});
            })
        }
    }).register("screen and (min-width: 768px)", {
        match: function () {
            $('div#cookies_policy_big_form').css({'top': 'auto', 'bottom': '15px'});
        }
    });
});

$('input.cookie_accept').click(function () {
    createCookie('cookies_policy', true, 365);

    $('div.cookies_absolute_div').fadeOut('normal', function () {
        $(this).remove();
    });

    if (cpConfigs.removeCookieBox) {
        $('div#cookies_policy_big_form').fadeOut('normal', function () {
            $(this).remove();
            $('div.cookies_policy_icon').remove();
        });
    } else {
        $('div#cookies_policy_big_form').fadeOut('normal', function () {
            $(this).find('input.cookie_accept').remove();
        });
    }

    cpConfigs.showCookieNotice = false;

    if (rlPageInfo.controller === 'add_listing' || cpConfigs.blockAllCookies) {
        document.location.reload(true);
    } else {
        $('html, body').removeClass('CP_lock_screen');
    }
});

$('input.cookie_decline').click(function(){
    document.cookie.split('; ').forEach(function(name) {
        eraseCookie(name.split('=')[0]);
    });

    window.location.href = cpConfigs.redirectUrl;
});

if (cpConfigs.showCookieNotice === false){
    $('div.cookies_absolute_div').click(function(){
        $(this).fadeOut('normal');
        $(this).remove();
        $('div#cookies_policy_big_form').fadeOut('normal');
    });
}
