/*globals $*/
/*jslint eqeq:true plusplus:true*/

var oApp = window.oApp || {};

(function () {

    'use strict';

    $('#optionFilter').blur(function () {
        var el = $(this),
            oldVal = el.data('original'),
            newVal = el.val(),
            currentUrl = window.location.href,
            newUrl = window.location.href.split('filter=' + oldVal).join('filter=' + newVal);

        if (newVal !== oldVal) {
            if (currentUrl === newUrl) {
                newUrl = window.location.href + '&filter=' + newVal;
            }
            window.location.href = newUrl;
        }
    });

    $('#optionShowHash').change(function () {
        var el = $(this),
            isChecked = el.prop('checked'),
            newUrl = window.location.href.split('&showhash').join('');

        if (isChecked) {
            newUrl += '&showhash';
        }

        window.location.href = newUrl;

    });

    $('#optionShowDate').change(function () {
        var el = $(this),
            isChecked = el.prop('checked'),
            newUrl = window.location.href.split('&showdate').join('');

        if (isChecked) {
            newUrl += '&showdate';
        }

        window.location.href = newUrl;

    });

}());