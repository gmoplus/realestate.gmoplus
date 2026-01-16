<!-- user navigation bar -->

<span class="circle" id="user-navbar">
    {if $new_messages}<span class="notify"></span>{/if}
    <span class="default"><span {if $isLogin}class="logged-in"{/if}></span></span>
    <span class="content {if $isLogin}a-menu{/if} hide">
        {if $isLogin}
            {include file='menus/user_navbar_menu.tpl'}
        {else}
            <span class="user-navbar-container">
                {include file='blocks/login_modal.tpl'}
            </span>
        {/if}
    </span>

    <span class="mobile-hidden">
        {if $isLogin}
            <span class="font1">{$lang.welcome},</span> <a href="{$rlBase}{if $config.mod_rewrite}{$pages.login}.html{else}?page={$pages.login}{/if}">{$account_info.Username}!</a>
        {else}
            <a title="{$lang.login}" href="{$rlBase}{if $config.mod_rewrite}{$pages.login}.html{else}?page={$pages.login}{/if}">{$lang.login}</a>
            {if $pages.registration}
                <a title="{$lang.create_account}" href="{$rlBase}{if $config.mod_rewrite}{$pages.registration}.html{else}?page={$pages.registration}{/if}">{$lang.registration}</a>
            {/if}
        {/if}
    </span>
</span>

<!-- user navigation bar end -->
