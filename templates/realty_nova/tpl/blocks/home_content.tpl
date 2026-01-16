<!-- home page content tpl -->

<section class="horizontal-search{if $config.home_page_h1} h1-exists{/if}{if $config.home_map_search} map-search-mode{/if}">
    <div class="point1">
        {if $config.home_page_h1 && !$config.home_map_search}
        <div class="row">
            <div class="col-xl-6 col-md-5 h1-container">
                <h1>{if $pageInfo.h1}{$pageInfo.h1}{else}{$pageInfo.name}{/if}</h1>
                {if $lang.home_page_h3}<h3>{$lang.home_page_h3}</h3>{/if}
            </div>
        {/if}

        {if $search_forms}
            <div {if $config.home_page_h1 && !$config.home_map_search}class="col-xl-5 col-md-6 offset-md-1" {/if}id="search_area">
                {include file='blocks'|cat:$smarty.const.RL_DS|cat:'horizontal_search.tpl'}
            </div>
        {/if}

        {if $config.home_page_h1 && !$config.home_map_search}
        </div>
        {/if}
    </div>
</section>

<!-- home page content tpl end -->
