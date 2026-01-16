<!-- home page content tpl -->

<section class="home-search">
	<div class="point1">
		{if $config.home_page_h1}
            <h1 class="align-center">{if $pageInfo.h1}{$pageInfo.h1}{else}{$pageInfo.name}{/if}</h1>
        {/if}

		<div id="search_area">
			{include file='blocks'|cat:$smarty.const.RL_DS|cat:'horizontal_search.tpl'}
		</div>
	</div>
</section>

{if $tpl_settings.home_page_gallery && ($config.home_gallery_box || $config.home_special_box)}
    <section class="features-gallery">
        <div class="point1">
            <div class="row gallery-container">
                <div class="col-lg-8 col-sm-12 h-100">
                    <div class="featured_gallery h-100 overflow-hidden{if $demo_gallery} demo{/if}">
                        {insert name='eval' content=$gallary_content}
                        <div class="preview h-100">
                            <a {if $config.featured_new_window}target="_blank"{/if} title="{$lang.view_details}" href="#"><div></div></a>
                            <div class="fg-title hide"></div>
                            <div class="fg-price hide"></div>
                        </div>
                        {if $demo_gallery}{assign var='demo_gallery' value=false}{/if}
                    </div>
                </div>

                <!-- body style box -->
                <div class="col-lg-4 col-sm-12 h-100 special-block mt-4 mt-md-0">
                    {if $home_page_special_block}
                        {include file='blocks'|cat:$smarty.const.RL_DS|cat:'blocks_manager.tpl' block=$home_page_special_block side='sidebar'}
                    {/if}
                </div>
                <!-- body style box end -->
            </div>
        </div>
    </section>

    <script class="fl-js-dynamic">
    {literal}

    $(function(){
        flUtil.loadScript(rlConfig['tpl_base'] + 'components/featured-gallery/_featured-gallery.js', function(){
            featuredGallery();
        });

        $('.special-block .side_block > div').addClass('scrollbar').css('overflow', 'auto');
    });

    {/literal}
    </script>
{/if}

<!-- home page content tpl end -->
