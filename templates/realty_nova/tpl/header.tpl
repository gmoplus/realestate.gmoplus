{include file='head.tpl'}

    <div class="main-wrapper d-flex flex-column">
        <header class="page-header{if $pageInfo.Key == 'search_on_map'} fixed-menu{/if}{if $config.home_map_search && $pageInfo.Key == 'home'} page-header-map{/if}">
            <div class="page-header-mask">
                {if $config.home_map_search && $pageInfo.Key == 'home'}
                    <div id="map_container"></div>
                    <span class="loading map-loading"><span class="loading-spinner"></span></span>

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
                        searchForm: $('.map-search-mode .search-block-content'),
                        tabBar: $('.map-search-mode .form-switcher'),
                        desktopLimit: listings_limit_desktop,
                        mobileLimit: listings_limit_mobile,
                        geocoder: false
                    });
                    {/literal}
                    </script>
                {/if}
            </div>

            <div class="point1">
                <div class="top-navigation">
                    <div class="point1 h-100 d-flex align-items-center">
                        {include file='blocks'|cat:$smarty.const.RL_DS|cat:'lang_selector.tpl'}

                        {rlHook name='tplHeaderUserNav'}

                        <span class="header-contacts d-none d-lg-block font-size-xs font-weight-semibold">
                            {if $lang.header_contact_email}
                                <a class="color-light contacts__email ml-3 mr-3" href="mailto: {$lang.header_contact_email}">
                                    <svg viewBox="0 0 12 10" class="mr-1">
                                        <use xlink:href="#envelope-small"></use>
                                    </svg>
                                    {$lang.header_contact_email}
                                </a>
                            {/if}
                            {if $lang.header_contact_phone_number}
                                <a class="color-light contacts__handset ml-3 mr-3" href="tel: {$lang.header_contact_phone_number}">
                                    <svg viewBox="0 0 12 12" class="mr-1">
                                        <use xlink:href="#handset"></use>
                                    </svg>
                                    {$lang.header_contact_phone_number}
                                </a>
                            {/if}
                        </span>

                        <nav class="d-flex flex-fill shrink-fix h-100 justify-content-end user-navbar">
                            {rlHook name='tplHeaderUserArea'}

                            {include file='blocks'|cat:$smarty.const.RL_DS|cat:'user_navbar.tpl'}

                            <span class="menu-button d-flex d-lg-none align-items-center" title="{$lang.menu}">
                                <svg viewBox="0 0 20 14" class="mr-2">
                                    <use xlink:href="#mobile-menu"></use>
                                </svg>
                                {$lang.menu}
                            </span>
                        </nav>
                    </div>
                </div>
                <section class="header-nav d-flex">
                    <div class="point1 d-flex align-items-center">
                        <div>
                            <div class="mr-0 mr-md-3" id="logo">
                                <a class="d-inline-block" href="{$rlBase}" title="{$config.site_name}">
                                    <img alt="{$config.site_name}"
                                        src="{$rlTplBase}img/logo.png?rev={$config.static_files_revision}"
                                        srcset="{$rlTplBase}img/@2x/logo.png?rev={$config.static_files_revision} 2x" />
                                </a>
                            </div>
                        </div>
                        <div class="main-menu flex-fill">
                            {include file='menus'|cat:$smarty.const.RL_DS|cat:'main_menu.tpl'}
                        </div>
                    </div>
                </section>

                {if $pageInfo.Key == 'home'}
                    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'home_content.tpl'}
                {/if}
            </div>
        </header>
