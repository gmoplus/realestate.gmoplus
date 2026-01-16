<!-- home page search box tpl -->

{assign var='pill_width' value=0}
{if is_array($search_forms)}
    {math assign='pill_width' equation='round(100/(count), 3)' count=$search_forms|@count}
{/if}

<div class="search-block-content">
    {foreach from=$search_forms item='search_form' key='sf_key' name='sformsF'}
        {assign var='spage_key' value=$listing_types[$search_form.listing_type].Page_key}
        {assign var='listing_type' value=$listing_types[$search_form.listing_type]}
        {assign var='post_form_key' value=$sf_key}

        <div id="area_{$sf_key}" class="search_tab_area{if !$smarty.foreach.sformsF.first} hide{/if}">
            <form name="map-search-form" class="row" accesskey="{$search_form.listing_type}" method="{$listing_type.Submit_method}" action="{$rlBase}{if $config.mod_rewrite}{$pages.$spage_key}/{$search_results_url}.html{else}?page={$pages.$spage_key}&amp;{$search_results_url}{/if}">{strip}

                <input type="hidden" name="form" value="{$post_form_key}" />

                {if $search_form.arrange_field}
                    <input type="hidden" name="f[{$search_form.arrange_field}]" value="{$search_form.arrange_value}" />
                {/if}

                <!-- tabs -->
                {if $search_forms|@count > 1}
                <div class="search-form-cell form-switcher">
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
                                    <label data-key="{$pill_key}" title="{$search_pill.name}" style="width: {$pill_width}%;">
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
                <!-- tabs end -->

                {foreach from=$search_form.data item='item'}
                    {include file='blocks'|cat:$smarty.const.RL_DS|cat:'fields_search_horizontal.tpl' fields=$item.Fields}
                {/foreach}

                <div class="search-form-cell submit">
                    <div>
                        <span></span>
                        <div>
                            <input type="submit" value="{$lang.search}" />
                        </div>
                    </div>
                </div>
            {/strip}</form>
        </div>
    {/foreach}

    {if is_array($search_forms) && $search_forms|@count > 1}
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
</div>

<!-- home page search box tpl end -->
