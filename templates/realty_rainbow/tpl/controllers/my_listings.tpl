<!-- my listings -->

{* GMO Plus Refresh Statistics Panel - Always show, load via JavaScript *}
<div id="refresh-stats-panel" class="refresh-stats-panel" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; margin-bottom: 25px; color: white; box-shadow: 0 8px 25px rgba(0,0,0,0.15);">
    <div style="display: flex; align-items: center; margin-bottom: 15px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" style="margin-right: 10px;">
            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1L13.5 2.5L16.17 5.17C14.24 4.42 12.17 4.17 10 4.58C5.58 5.47 2.47 9.24 2.47 13.8C2.47 18.4 6.06 22 10.66 22C14.15 22 17.09 19.37 17.82 15.91C17.96 15.27 17.55 14.64 16.91 14.5C16.27 14.36 15.64 14.77 15.5 15.41C14.95 18.05 12.58 20 9.66 20C7.15 20 5.16 17.91 5.16 15.4C5.16 12.91 7.15 10.82 9.66 10.82C10.5 10.82 11.3 11.04 12 11.45V14L17 9H21Z" fill="white"/>
        </svg>
        <h3 style="margin: 0; font-size: 18px; font-weight: 600;">İlan Yenileme İstatistikleri</h3>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px;">
        <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 15px; text-align: center; backdrop-filter: blur(10px);">
            <div id="stat-total" style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">⏳</div>
            <div style="font-size: 13px; opacity: 0.9;">Toplam Yenileme</div>
        </div>
        <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 15px; text-align: center; backdrop-filter: blur(10px);">
            <div id="stat-month" style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">⏳</div>
            <div style="font-size: 13px; opacity: 0.9;">Bu Ay</div>
        </div>
        <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 15px; text-align: center; backdrop-filter: blur(10px);">
            <div id="stat-week" style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">⏳</div>
            <div style="font-size: 13px; opacity: 0.9;">Bu Hafta</div>
        </div>
        <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 15px; text-align: center; backdrop-filter: blur(10px);">
            <div id="stat-today" style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">⏳</div>
            <div style="font-size: 13px; opacity: 0.9;">Bugün</div>
        </div>
    </div>
    <div style="margin-top: 15px; font-size: 12px; opacity: 0.8; text-align: center;">
        💡 İpucu: Yenileme işlemi ilan hakkınızdan düşülmez ve ilanınızı üst sıralara taşır.
    </div>
</div>

{if !empty($listings)}

    {if $sorting}

        {php}
            $types = array('asc' => 'ascending', 'desc' => 'descending'); $this -> assign('sort_types', $types);
            $sort = array('price', 'number', 'date'); $this -> assign('sf_types', $sort);
        {/php}

        <div class="grid_navbar">
            <div class="sorting">
                <div class="current{if $grid_mode == 'map'} disabled{/if}">
                    {$lang.sort_by}:
                    <span class="link">{if $sort_by}{$sorting[$sort_by].name}{else}{$lang.date}{/if}</span>
                    <span class="arrow"></span>
                </div>
                <ul class="fields">
                    {foreach from=$sorting item='field_item' key='sort_key' name='fSorting'}
                        {if $field_item.Type|in_array:$sf_types}
                            {foreach from=$sort_types key='st_key' item='st'}
                                <li><a rel="nofollow" {if $sort_by == $sort_key && $sort_type == $st_key}class="active"{/if} title="{$lang.sort_listings_by} {$field_item.name} ({$lang[$st]})" href="{if $config.mod_rewrite}?{else}index.php?{$pageInfo.query_string}&{/if}sort_by={$sort_key}&sort_type={$st_key}">{$field_item.name} ({$lang[$st]})</a></li>
                            {/foreach}
                        {else}
                            <li><a rel="nofollow" {if $sort_by == $sort_key}class="active"{/if} title="{$lang.sort_listings_by} {$field_item.name}" href="{if $config.mod_rewrite}?{else}index.php?{$pageInfo.query_string}&{/if}sort_by={$sort_key}&sort_type=asc">{$field_item.name}</a></li>
                        {/if}
                    {/foreach}
                    {rlHook name='myListingsAfterSorting'}
                </ul>
            </div>
        </div>
    {/if}

    {rlHook name='myListingsBeforeListings'}

    <section id="listings" class="my-listings list">
        {foreach from=$listings item='listing' key='key'}
            {if $listing.Subscription_ID}
                {assign var='hasSubscriptions' value=true}
            {/if}
            {include file='blocks'|cat:$smarty.const.RL_DS|cat:'my_listing.tpl'}
        {/foreach}
    </section>

    <!-- paging block -->
    {if $search_results_mode && $refine_search_form}
        {assign var='myads_paging_url' value=$search_results_url}
    {else}
        {assign var='myads_paging_url' value=false}
    {/if}
    {paging calc=$pInfo.calc total=$listings|@count current=$pInfo.current per_page=$config.listings_per_page url=$myads_paging_url method=$listing_type.Submit_method}
    <!-- paging block end -->

    <script class="fl-js-dynamic">{literal}
        $(document).ready(function(){
            // Load refresh statistics
            function loadRefreshStats() {
                $.get('simple_stats_test.php', function(data) {
                    if (data.success) {
                        $('#stat-total').text(data.stats.total);
                        $('#stat-month').text(data.stats.month);
                        $('#stat-week').text(data.stats.week);
                        $('#stat-today').text(data.stats.today);
                    } else {
                        $('#stat-total, #stat-month, #stat-week, #stat-today').text('⚠️');
                    }
                }).fail(function() {
                    $('#stat-total, #stat-month, #stat-week, #stat-today').text('❌');
                });
            }
            
            // Load stats on page load
            loadRefreshStats();
            
            // Also update stats after successful refresh
            window.updateRefreshStats = loadRefreshStats;
            $('.my-listings .delete').each(function(){
                $(this).flModal({
                    caption: '{/literal}{$lang.warning}{literal}',
                    content: '{/literal}{$lang.notice_delete_listing}{literal}',
                    prompt: 'xajax_deleteListing('+ $(this).attr('id').split('_')[2] +')',
                    width: 'auto',
                    height: 'auto'
                });
            });

            {/literal}{if $hasSubscriptions}{literal}
            $('.my-listings .unsubscription').each(function() {
                $(this).flModal({
                    caption: '',
                    content: '{/literal}{$lang.stripe_unsubscripbe_confirmation}{literal}',
                    prompt: 'flSubscription.cancelSubscription(\''+ $(this).attr('accesskey').split('-')[2] +'\', \''+ $(this).attr('accesskey').split('-')[0] +'\', '+ $(this).attr('accesskey').split('-')[1] +', false)',
                    width: 'auto',
                    height: 'auto'
                });
            });
            {/literal}{/if}{literal}

            $('label.switcher-status input[type="checkbox"]').change(function() {
                var element = $(this);
                var id = $(this).val();
                var status = $(this).is(':checked') ? 'active' : 'approval';

                $.getJSON(
                    rlConfig['ajax_url'],
                    {mode: 'changeListingStatus', item: id, value: status, lang: rlLang},
                    function(response) {
                        if (response) {
                            if (response.status == 'ok') {
                                printMessage('notice', response.message_text);
                            } else {
                                printMessage('error', response.message_text);
                                element.prop('checked', false);
                            }
                        }
                    }
                );
            });

            $('label.switcher-featured input[type="checkbox"]').change(function() {
                var element = $(this);
                var id = $(this).val();
                var status = $(this).is(':checked') ? 'featured': 'standard';

                $.getJSON(
                    rlConfig['ajax_url'],
                    {mode: 'changeListingFeaturedStatus', item: id, value: status, lang: rlLang},
                    function(response) {
                        if (response) {
                            if (response.status == 'ok') {
                                if (status == 'featured') {
                                    $('article#listing_' + id).addClass('featured');
                                    $('article#listing_'+ id +' div.nav div.info .picture').prepend('<div class="label"><div title="{/literal}{$lang.featured}{literal}">{/literal}{$lang.featured}{literal}</div></div></div>');
                                } else {
                                    $('article#listing_'+ id +' div.nav div.info .picture').find('div.label').remove();
                                    $('article#listing_' + id).removeClass('featured');
                                }
                                printMessage('notice', response.message_text);
                            } else {
                                printMessage('error', response.message_text);
                                if (element.is(':checked')) {
                                    element.prop('checked', false);
                                } else {
                                    element.prop('checked', 'checked');
                                }
                            }
                        }
                    }
                );
            });

            // Refresh System JavaScript
            $('.refresh-listing').click(function(e) {
                e.preventDefault();
                var button = $(this);
                var listingId = button.data('listing-id');
                var listingType = button.data('listing-type');
                var originalText = button.find('span').text();
                
                // Disable button and show loading state
                button.addClass('disabled').find('span').text('{/literal}{$lang.processing}{literal}...');
                
                $.post('index.php', {
                    mode: 'refreshListing',
                    listing_id: listingId,
                    listing_type: listingType
                }, function(response) {
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch(e) {}
                    }
                    
                    if (response.status == 'success') {
                        printMessage('notice', response.message);
                        // Hide refresh button after successful refresh
                        button.closest('li').fadeOut();
                        // Update date if provided
                        if (response.newDate) {
                            $('article#listing_' + listingId + ' .stat li:contains("{/literal}{$lang.added}{literal}")').html('<span class="name">{/literal}{$lang.added}{literal}</span> ' + response.newDate);
                        }
                        
                        // Update refresh statistics
                        setTimeout(function() {
                            if (window.updateRefreshStats) {
                                window.updateRefreshStats();
                            }
                        }, 500);
                    } else {
                        printMessage('error', response.message);
                        button.removeClass('disabled').find('span').text(originalText);
                    }
                }).fail(function() {
                    printMessage('error', 'Sistem hatası oluştu');
                    button.removeClass('disabled').find('span').text(originalText);
                });
            });
        });
        {/literal}
    </script>
{else}
    <div class="info">
        {if $pages.add_listing}
            {assign var='link' value='<a href="'|cat:$add_listing_href|cat:'">$1</a>'}
            {$lang.no_listings_here|regex_replace:'/\[(.+)\]/':$link}
        {else}
            {phrase key='no_listings_found_deny_posting' db_check='true'}
        {/if}
    </div>
{/if}

{rlHook name='myListingsBottom'}

{if $hasSubscriptions}
    {addJS file=$rlTplBase|cat:'js/subscription.js' id='subscription'}
{/if}

<!-- my listings end -->
