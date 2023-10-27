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
    'uiComponent'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            listens: {
                '${ $.provider }:data.is_downloadable': 'handleProductType'
            },
            links: {
                isDownloadable: '${ $.provider }:data.is_downloadable'
            },
            modules: {
                createConfigurableButton: '${$.createConfigurableButton}'
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            this.handleProductType(this.isDownloadable);

            return this;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['content']);

            return this;
        },

        /**
         * Change content for container and visibility for button
         *
         * @param {String} isDownloadable
         */
        handleProductType: function (isDownloadable) {
            if (isDownloadable === '1') {
                this.content(this.content2);

                if (this.createConfigurableButton()) {
                    this.createConfigurableButton().visible(false);
                }
            } else {
                this.content(this.content1);

                if (this.createConfigurableButton()) {
                    this.createConfigurableButton().visible(true);
                }
            }
        }
    });
});
