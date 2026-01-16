<!-- home page content tpl -->

{if $config.home_page_h1}
    <div class="h1 point1"><h1>{if $pageInfo.h1}{$pageInfo.h1}{else}{$pageInfo.name}{/if}</h1></div>
{/if}

{if $search_forms}
{php}
foreach ($this->_tpl_vars['search_forms'] as $key => $form) {
    foreach ($form['data'] as $index => $field) {
        if (is_numeric(strpos($field['Fields'][0]['Key'], 'address'))) {
            unset($this->_tpl_vars['search_forms'][$key]['data'][$index]);
            break;
        }
    }
}
{/php}
<section class="home-map map-search relative">
	<div id="map_container"></div>
	<div class="controls">
		<div class="point1">
			<div id="search_area" class="clearfix">
				{include file='blocks'|cat:$smarty.const.RL_DS|cat:'horizontal_search.tpl'}
			</div>
		</div>
	</div>
</section>

<svg class="hide" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    {include file='../img/svg/userLocation.svg'}
</svg>

{mapsAPI}

{addJS file=$rlTplBase|cat:'controllers/search_map/search_map.js'}

<script class="fl-js-dynamic">
var default_map_location = '{$default_map_location|escape:'quotes'}';
var default_map_coordinates = [{if $smarty.post.loc_lat && $smarty.post.loc_lng}{$smarty.post.loc_lat},{$smarty.post.loc_lng}{else}{$config.search_map_location}{/if}];
var default_map_zoom = {if $config.search_map_location_zoom}{$config.search_map_location_zoom}{else}14{/if};
var listings_limit_desktop = {if $config.map_search_listings_limit}{$config.map_search_listings_limit}{else}500{/if};
var listings_limit_mobile = {if $config.map_search_listings_limit_mobile}{$config.map_search_listings_limit_mobile}{else}75{/if};

lang['count_properties'] = '{$lang.count_properties}';
lang['number_property_found'] = '{$lang.number_property_found}';
lang['no_properties_found'] = '{$lang.no_properties_found}';
lang['map_listings_request_empty'] = '{$lang.map_listings_request_empty}';
lang['enter_a_location'] = '{$lang.enter_a_location}';
lang['short_price_k'] = '{$lang.short_price_k}';
lang['short_price_m'] = '{$lang.short_price_m}';
lang['short_price_b'] = '{$lang.short_price_b}';

{literal}

var mapTabBar = mapSearch.tabBar;
mapSearch.tabBar = function(){
    $('.leaflet-top.leaflet-right').addClass('point1');

    if (typeof mapTabBar == 'function') {
        mapTabBar.call(mapSearch);
    }
}

mapSearch.init({
    mapContainer: $('#map_container'),
    mapCenter: default_map_coordinates,
    mapZoom: default_map_zoom,
    mapAltLocation: default_map_location,
    searchForm: $('.home-map .search-block-content'),
    tabBar: $('.home-map .tabs'),
    desktopLimit: listings_limit_desktop,
    mobileLimit: listings_limit_mobile
});
{/literal}
</script>
{/if}

<!-- home page content tpl end -->
