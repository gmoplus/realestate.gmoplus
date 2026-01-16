{include file='../img/social.svg'}

<footer>
    <div class="point1 mx-auto">
        <div class="d-flex flex-column flex-md-row">
            <nav class="footer-menu flex-fill">
                {include file='menus'|cat:$smarty.const.RL_DS|cat:'footer_menu.tpl'}
            </nav>

            {include file='footer_data.tpl'}
        </div>
    </div>
</footer>

{if !$isLogin}
    <div id="login_modal_source" class="hide">
        <div class="tmp-dom">
            {include file='blocks/login_modal.tpl'}
        </div>
    </div>
{/if}

{rlHook name='tplFooter'}

{include file='footerScript.tpl'}
