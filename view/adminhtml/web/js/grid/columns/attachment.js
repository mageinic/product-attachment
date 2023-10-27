/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_ProductAttachment
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        gethtml: function (row) {
            return row[this.index + '_html'];
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getFormaction: function (row) {
            return row[this.index + '_formaction'];
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getCustomerid: function (row) {
            return row[this.index + '_customerid'];
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getLabel: function (row) {
            return row[this.index + '_html']
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getTitle: function (row) {
            return row[this.index + '_title']
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getSubmitlabel: function (row) {
            return row[this.index + '_submitlabel']
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getCancellabel: function (row) {
            return row[this.index + '_cancellabel']
        },

        /**
         *
         * @param row
         * @returns void
         */
        preview: function (row) {
            var modalHtml = mageTemplate(
                sendmailPreviewTemplate,
                {
                    html: this.gethtml(row),
                    title: this.getTitle(row),
                    label: this.getLabel(row),
                    formaction: this.getFormaction(row),
                    customerid: this.getCustomerid(row),
                    submitlabel: this.getSubmitlabel(row),
                    cancellabel: this.getCancellabel(row),
                    linkText: $.mage.__('Go to Details Page')
                }
            );

            /**
             *
             * @type {*|jQuery}
             */
            const previewPopup = $('<div></div>').html(modalHtml);
            previewPopup.modal({
                title: this.getTitle(row),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []}).trigger('openModal');
        },

        /**
         *
         * @param row
         * @returns {*}
         */
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});
