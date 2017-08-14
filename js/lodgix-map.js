var LodgixMap = (function ($) {
    'use strict';

    return function (options) {

        this.options = options;

        this.init = function () {
            var pos = new google.maps.LatLng(this.options.lat, this.options.lon);
            var options = {
                zoom: this.options.zoom,
                center: pos,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.maps = [];
            this.markers = [];
            var that = this;
            var els = document.getElementsByClassName(this.options.className);
            var len = els.length;
            var map;
            for (var i = 0; i < len; i++) {
                map = new google.maps.Map(els[i], options);
                this.maps.push(map);
                this.markers.push(new google.maps.Marker({
                    position: pos,
                    map: map
                }));
                google.maps.event.addListener(map, 'bounds_changed', function() {
                    if (that.isResized) {
                        that.panToMarker();
                        that.isResized = false;
                    }
                });
            }
        };

        this.initOnDocumentReady = function () {
            var that = this;
            $(document).ready(function () {
                that.init();
            });
        };

        this.isResized = false;

        this.resize = function () {
            var maps = this.maps;
            if (maps) {
                var len = maps.length;
                for (var i = 0; i < len; i++) {
                    google.maps.event.trigger(maps[i], 'resize');
                }
            }
            this.isResized = true;
        };

        this.panToMarker = function () {
            var maps = this.maps;
            if (maps) {
                var len = maps.length;
                for (var i = 0; i < len; i++) {
                    maps[i].panTo(this.markers[i].getPosition());
                }
            }
        };

        this.setZoom = function (value) {
            value = parseInt(value);
            if (!isNaN(value) && value > 0) {
                var maps = this.maps;
                if (maps) {
                    var len = maps.length;
                    for (var i = 0; i < len; i++) {
                        maps[i].setZoom(value);
                    }
                }
            }
        };

    };

})(window.jQLodgix);
