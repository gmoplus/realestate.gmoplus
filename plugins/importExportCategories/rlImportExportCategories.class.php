<?php


/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: RLIMPORTEXPORTCATEGORIES.CLASS.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

use Flynax\Abstracts\AbstractPlugin;
use Flynax\Interfaces\PluginInterface;
use Flynax\Plugins\ImportExportCategories\Import;

/**
 * Class rlImportExportCategories
 */
class rlImportExportCategories extends AbstractPlugin implements PluginInterface
{
    /**
     * @hook apTplHeader
     * @since 2.3.0
     */
    public function hookApTplHeader()
    {
        if ($_GET['controller'] !== 'importExportCategories') {
            return;
        }

        $href = RL_PLUGINS_URL . 'importExportCategories/admin/static/style.css';
        printf('<link href="%s" type="text/css" rel="stylesheet" />', $href);
    }

    /**
     * @hook apAjaxRequest
     * @since 3.0.0
     *
     * @param array  $out
     * @param string $item
     */
    public function hookApAjaxRequest(&$out, $item)
    {
        if ($item !== 'importCategory') {
            return;
        }

        require __DIR__ . '/vendor/autoload.php';

        $out = (new Import)->fromStack((int) $_REQUEST['stack']);
    }

    /**
     * @hook  apPhpIndexBottom
     * @since 2.3.0
     */
    public function hookApPhpIndexBottom()
    {
        global $_response, $lang;

        if ($_GET['controller'] === 'importExportCategories'
            && $_REQUEST['xjxfun'] === 'ajaxGetCatLevel'
            && ($categoryID = reset($_REQUEST['xjxargs']))
        ) {
            $_response->script("
                var imExChildInterval = setInterval(function(){
                    var \$childList = $('li#tree_cat_{$categoryID} ul');

                    if (\$childList.length) {
                        var \$spanCheckAll = $('\<span\>')
                            .addClass('green_10')
                            .text(\"{$lang['check_all']}\")
                            .click(function(){
                                \$childList.find('input').prop('checked', true);
                                levelDynamic('check', $(this));
                            });

                        var \$spanDivider = $('\<span\>').addClass('divider').text(' | ');

                        var \$spanUnCheckAll = $('\<span\>')
                            .addClass('green_10')
                            .text(\"{$lang['uncheck_all']}\")
                            .click(function(){
                                 levelDynamic('uncheck', $(this));
                                \$childList.find('input').prop('checked', false)
                            });

                        var \$divGrey = $('\<div\>').addClass('grey_area margin_block').append(
                            \$spanCheckAll,
                            \$spanDivider,
                            \$spanUnCheckAll
                         )

                        \$childList.after(\$divGrey);

                        clearInterval(imExChildInterval);

                        uncheckChildCheckboxes();
                    }
                }, 200);
            ");
        }
    }

    /**
     * @hook phpPreGetCategoryData
     * @since 3.0.0
     */
    public function hookPhpPreGetCategoryData($id = 0, $path = '', &$select = []): void
    {
        global $config, $languages, $rlLang;

        if ($config['multilingual_paths']
            && $_SESSION['imex_plugin']
            && $_SESSION['imex_plugin']['category_id']
            && ($_SERVER['SCRIPT_FILENAME'] === RL_PLUGINS . 'importExportCategories/admin/importExportCategories.inc.php'
                || $_REQUEST['item'] === 'importCategory'
            )
        ) {
            if (!$languages) {
                $languages = $rlLang->getLanguagesList();
            }

            foreach ($languages as $languageKey => $languageData) {
                if ($languageData['Code'] === $config['lang']) {
                    continue;
                }

                $select[] = 'Path_' . $languageData['Code'];
            }
        }
    }

    /**
     * @version 2.1.0
     */
    public function update210(): void
    {
        $GLOBALS['rlDb']->query("
            DELETE FROM `{db_prefix}lang_keys` 
            WHERE `Key` IN (
              'importExportCategories_selector_tr_level',
              'importExportCategories_selector_tr_parent',
              'importExportCategories_selector_tr_name',
              'importExportCategories_selector_tr_path',
              'importExportCategories_selector_tr_type',
              'importExportCategories_selector_tr_lock',
              'importExportCategories_selector_tr_key',
              'importExportCategories_no_parent'
            )
        ");
    }

    /**
     * @version 3.0.0
     */
    public function update300(): void
    {
        global $rlDb;

        $GLOBALS['reefless']->deleteDirectory(RL_PLUGINS . 'importExportCategories/phpExcel/');
        @unlink(RL_PLUGINS . 'importExportCategories/admin/import.php');
        @unlink(RL_PLUGINS . 'importExportCategories/admin/static/example.png');

        $rlDb->query(
            "DELETE FROM `{db_prefix}lang_keys` 
             WHERE `Plugin` = 'importExportCategories' AND `Key` IN (
                 'importExportCategories_import_reupload',
                 'importExportCategories_rowLevel'
             )"
        );

        if (array_key_exists('ru', $GLOBALS['languages'])) {
            $russianTranslation = json_decode(file_get_contents(RL_PLUGINS . 'importExportCategories/i18n/ru.json'), true);
            foreach ($russianTranslation as $phraseKey => $phrase) {
                if ($rlDb->getOne('ID', "`Key` = '{$phraseKey}' AND `Code` = 'ru'", 'lang_keys')) {
                    $rlDb->updateOne([
                        'fields' => ['Value' => $phrase],
                        'where'  => ['Key'  => $phraseKey, 'Code' => 'ru'],
                    ], 'lang_keys');
                } else {
                    $rlDb->insertOne([
                        'Code'   => 'ru',
                        'Module' => 'common',
                        'Key'    => $phraseKey,
                        'Value'  => $phrase,
                        'Plugin' => 'importExportCategories',
                    ], 'lang_keys');
                }
            }
        }
    }
}
