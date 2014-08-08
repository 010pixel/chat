(function($) {
    function startTrigger(e,data) {
        var $elem = $(this);
        $elem.data('mouseheld_timeout', setTimeout(function() {
            $elem.trigger('mouseheld');
        }, e.data));
    }
    
    function stopTrigger() {
        var $elem = $(this);
        clearTimeout($elem.data('mouseheld_timeout'));
    }


    var mouseheld = $.event.special.mouseheld = {
        setup: function(data) {
            var $this = $(this);
            $this.bind('mousedown', +data || mouseheld.time, startTrigger);
            $this.bind('mouseleave mouseup', stopTrigger);
        },
        teardown: function() {
            var $this = $(this);
            $this.unbind('mousedown', startTrigger);
            $this.unbind('mouseleave mouseup', stopTrigger);
        },
        time: 1000 // default to 750ms
    };
})(jQuery);