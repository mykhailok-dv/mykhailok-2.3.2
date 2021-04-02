define([
    'Magento_Ui/js/grid/listing'
], function (Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Mykhailok_SupportChat/ui/grid/chat'
        },
        getRowClass: function (row) {
            if (!parseInt(row.is_active)) {
                return 'inactive';
            } else if(parseInt(row.priority) === 0) {
                return 'regular';
            } else if(parseInt(row.priority) === 1) {
                return 'waiting';
            } else {
                return 'immediate';
            }
        }
    });
});
