<!-- footer data tpl -->

<div class="footer-data ml-md-3">
    <div class="icons justify-content-center justify-content-md-start d-flex">
        {include file='menus/footer_social_icons.tpl' marginClass='mr-4'}
    </div>

    <div class="logo">
        <a href="{$rlBase}" title="{$config.site_name}">
            <img alt="{$config.site_name}" src="{$rlTplBase}img/blank.gif" />
        </a>
    </div>

    &copy; {$smarty.now|date_format:'%Y'}, {$lang.powered_by}
    <a title="{$lang.powered_by} {$lang.copy_rights}" href="{$lang.flynax_url}">{$lang.copy_rights}</a>
</div>

<!-- footer data tpl end -->
