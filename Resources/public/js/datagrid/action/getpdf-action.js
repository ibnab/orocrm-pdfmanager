/*global define*/
define([
    './model-action'
], function (ModelAction) {
    'use strict';

    var GetpdfAction;

    /**
     * Ajax action, triggers REST AJAX request
     *
     * @export  oro/datagrid/action/getpdf-action
     * @class   oro.datagrid.action.GetpdfAction
     * @extends oro.datagrid.action.ModelAction
     */
    
    GetpdfAction = ModelAction.extend({

        entityName: null,
        
         /**
         * Initialize view
         *
         * @param {Object} options
         * @param {Backbone.Model} options.model Optional parameter
         * @throws {TypeError} If model is undefined
         */
        initialize: function (options) {
            
            var opts = options || {};

            
            if (_.has(opts, 'entityName')) {
                this.entityName = opts.entityName;
            }
            ModelAction.__super__.initialize.apply(this, arguments);
            
        },
        
        /**
         * Get action link
         *
         * @return {String}
         * @throws {TypeError} If route is undefined
         */
        /** @property {String} */
        
        getLink: function () {
            var result, backUrl;
            if (!this.link) {
                throw new TypeError("'link' is required");
            }
            if (!this.link) {
                throw new TypeError("'entity_name' is required");
            }
            if (this.model.has(this.link)) {
                result = this.model.get(this.link);
            } else {
                result = this.link;
            }

            if (this.backUrl) {
                backUrl = _.isBoolean(this.backUrl) ? location.href : this.backUrl;
                backUrl = encodeURIComponent(backUrl);
                result = this.addUrlParameter(result, this.backUrlParameter, backUrl);
            }
            if (this.model.has(this.entityName)) {
                result  = this.addUrlParameter(result,"entityClass",this.model.get(entityName));
            } else {
                result  = this.addUrlParameter(result,"entityClass",this.entityName);

            }
      /*
      if (this.model.has(this.entityId)) {
                result  = this.addUrlParameter(result,"entityId",this.model.get(id));
            } else {
                result  = this.addUrlParameter(result,"entityId",this.id);

            }
            return result;
        }
       */
            return result;
        }
    });
    

    return GetpdfAction;
});
