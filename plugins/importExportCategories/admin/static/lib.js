
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

var importCategoriesClass = function() {
    var self = this;
    var window = false;

    this.window = function() {
        if (!window) {
            window = Ext.MessageBox.show({
                msg: 'Saving your data, please wait...',
                progressText: 'Saving...',
                width:300,
                wait:true,
                waitConfig: {interval: 200}
            });
        }
        return window;
    };

    this.import = function(stack, callback) {
        if (stack === 0) {
            self.window().show();
        }

        $.ajax({
            url: rlConfig['ajax_url'],
            data: {
                'item': 'importExportCategories_importStack',
                'stack': stack
            },
            success: function(response) {
                var success = false;

                if (response) {
                    if (response.next === true && response.stack > stack) {
                        return self.import(response.stack);
                    } else {
                        success = true;
                    }
                }

                if (callback instanceof Function) {
                    callback(success)
                }
            }
        });
    };
};

var importCategories = new importCategoriesClass();
