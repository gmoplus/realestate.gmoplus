<!-- home page search box tpl -->

{math assign='pill_width' equation='round(100/(count), 4)' count=$search_forms|@count}

<div class="search-block-content">
    {foreach from=$search_forms item='search_form' key='sf_key' name='sformsF'}
        {assign var='spage_key' value=$listing_types[$search_form.listing_type].Page_key}
        {assign var='listing_type' value=$listing_types[$search_form.listing_type]}
        {assign var='post_form_key' value=$sf_key}

        <div id="area_{$sf_key}" class="search_tab_area{if !$smarty.foreach.sformsF.first} hide{/if}">
            <form name="map-search-form" class="row" accesskey="{$search_form.listing_type}" method="{$listing_type.Submit_method}" action="{$rlBase}{if $config.mod_rewrite}{$pages.search_on_map}.html{else}?page={$pages.search_on_map}{/if}">{strip}
                <input type="hidden" value="" name="loc_lat" />
                <input type="hidden" value="" name="loc_lng" />

                {if $search_form.arrange_field}
                    <input type="hidden" name="f[{$search_form.arrange_field}]" value="{$search_form.arrange_value}" />
                {/if}

                <!-- Listing type switcher -->
                {if $search_forms|@count > 1}
                <div class="search-form-cell form-switcher tabs">
                    <div class="align-items-end">
                        <span>{if $search_form.arrange_field}{phrase key='listing_fields+name+'|cat:$search_form.arrange_field}{else}{$lang.listing_type}{/if}</span>
                        <div>
                            {if $search_forms|@count > 3}
                            <select name="pills_{$sf_key}">
                                {foreach from=$search_forms item='search_pill' key='pill_key'}
                                <option value="{$pill_key}"{if $sf_key == $pill_key} selected="selected"{/if}>{$search_pill.name}</option>
                                {/foreach}
                            </select>
                            {else}
                            <span class="pills" data-key="{$sf_key}">
                                {foreach from=$search_forms item='search_pill' key='pill_key'}
                                    <label data-key="{$pill_key}" data-target="{$pill_key}" title="{$search_pill.name}" style="width: {$pill_width}%;">
                                        <input type="radio" value="{$pill_key}" name="pills_{$sf_key}" {if $sf_key == $pill_key}checked="checked"{/if} />
                                        {$search_pill.name}
                                    </label>
                                {/foreach}
                            </span>
                            {/if}
                        </div>
                    </div>
                </div>
                {/if}
                <!-- Listing type switcher end -->

                {foreach from=$search_form.data item='item'}
                    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fields_search_horizontal.tpl' fields=$item.Fields}
                {/foreach}

                <div class="d-flex {if $config.home_page_h1}search-form-button{else}search-form-cell flex-column justify-content-end ml-auto{/if}">
                    <div class="align-items-end w-100">
                        <span></span>
                        <div>
                            <input type="submit" value="{$lang.search}" />
                        </div>
                    </div>
                </div>
            {/strip}</form>
        </div>
    {/foreach}

    {if $search_forms|@count > 1}
    <script class="fl-js-dynamic">
    {literal}

    (function(){
        $('.form-switcher label').click(function(e){
            e.stopPropagation();
            searchFormSwitcher($(this).data('key'));
            return false;
        });

        $('.form-switcher select').change(function(e){
            e.stopPropagation();
            searchFormSwitcher($(this).val());
            return false;
        });

        var searchFormSwitcher = function(key){
            $('.search-block-content > .search_tab_area').addClass('hide');
            $('#area_' + key).removeClass('hide');
        }
    })();

    {/literal}
    </script>
    {/if}

    {geoAutocompleteAPI assign='autocompleteAPI'}

    <script class="fl-js-dynamic">
    lang['enter_a_location'] = '{$lang.enter_a_location}';
    {literal}

    flUtil.loadStyle('{/literal}{$autocompleteAPI.css}{literal}');
    flUtil.loadScript('{/literal}{$autocompleteAPI.js}{literal}', function(){
        $target = $('.horizontal-search input[name*=address]');

        $target.attr('placeholder', lang['enter_a_location']);
        $target.geoAutocomplete({
            onSelect: function(address, lat, lng){
                if (typeof mapSearch == 'object') {
                    mapSearch.map.panTo(new L.LatLng(lat, lng));
                } else {
                    $('[name=loc_lat]').val(lat);
                    $('[name=loc_lng]').val(lng);
                }
            }
        });
    });

    {/literal}
    </script>
</div>

<!-- home page search box tpl end -->
