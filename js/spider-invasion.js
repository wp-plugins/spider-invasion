(function ($) {
    $.fn.shake = function (options) {
        // defaults
        var settings = {
            'shakes': 4,
            'distance': 3,
            'duration': 200
        };
        // merge options
        if (options) {
            $.extend(settings, options);
        }
        // make it so
        var pos;
        return this.each(function () {
            $this = $(this);
            // position if necessary
            pos = $this.css('position');
            var marginLeft = 0;
            if (!pos || pos === 'static') {
                $this.css('position', 'relative');
            }
            else if (pos === 'absolute') {
                marginLeft = $this.position().left;
            }
            // shake it
            for (var x = 1; x <= settings.shakes; x++) {
                $this.animate({ left: marginLeft + settings.distance * -1 }, (settings.duration / settings.shakes) / 4)
                    .animate({ left: marginLeft + settings.distance }, (settings.duration / settings.shakes) / 2)
                    .animate({ left: marginLeft + 0 }, (settings.duration / settings.shakes) / 4);
            }
        });
    };
}(jQuery));
function getRandomPosition(element) {
	var $j = jQuery.noConflict();
	var x = $j('body').height()-element.height();
	var y = $j('body').width()-element.width();
	var randomX = Math.floor(Math.random()*x);
	var randomY = Math.floor(Math.random()*y);
	return [randomX,randomY];
}
function runSpawnAnimation(element) {
    element.animate({
        width: '35px', 
        height: '21px',
        marginLeft: '-15',
        marginTop: '-7'
    }, 400, function() {
        element.animate({
            width: '50px', 
            height: '30px',
            marginLeft: '-21',
            marginTop: '-10'
        }, 50, function() {
			element.shake();
        });
    });
}
function spawnSpider() {
	var $j = jQuery.noConflict();
	var src = param.images_dir + '/cute-spider-' + (Math.floor(Math.random() * 3) + 1) + '.png';
	var img = $j('<img/>')
    	.css({
        	position: 'absolute',
            opacity: 0.5,
            width: '50px',
            height: '30px',
            'z-index': 100
        })
        .addClass('spider-invader')
    	.attr("src", src)
        .appendTo('body')
        .hide();
	var xy = getRandomPosition(img);
    xy[1] += 10; // fix gap due to effects
	img.css({
        width: '1px',
        height: '1px'
    });
	if(param.invade_content) {
		if(
	        $j("#content").length > 0 && 
			xy[1] > ($j("#content").offset().left - img.height()) && xy[1] < ($j("#content").offset().left + $j("#content").width()) && 
			xy[0] > $j("#content").offset().top && xy[0] < ($j("#content").offset().top + $j("#content").height())
		) return;
	}
    img.css({
            top: xy[0] + 'px',
            left: xy[1] + 'px'
        });
    img.show();
    runSpawnAnimation(img);
    var dangerZoneWidth = 305;
    var dangerZonePosRight = xy[1] - 100 + dangerZoneWidth;
    var dangerZonePosLeft = xy[1] - 100;
    var dangerZone = $j('<div/>')
    	.css({
        	position: 'absolute',
            top: xy[0] - 140 + 'px',
            left: xy[1] - 150 + 'px',
            opacity: 0.5,
            width: ( dangerZonePosRight > $j('body').width() ? ($j('body').width() - dangerZonePosLeft) : dangerZoneWidth ) + 'px',
            height: '275px',
            'z-index': 50,
            overflow: 'hidden'
        })
    	.addClass('spider-danger-zone')
        .appendTo('body')
        .data('spider', img);
}
function spawnSpidersAtRandomIntervals(nbSpiders) {
	var $j = jQuery.noConflict();
	spawnSpider();
    ++nbSpiders;
    if($j('body').height()*$j('body').width()/(500*500) < nbSpiders) return;
    setTimeout(function() {
    	spawnSpidersAtRandomIntervals(nbSpiders);
    }, Math.floor(Math.random()*60000) + 5000);
}
function zigzagDefense(el) {
    if(el.position().top < -50) return;
    el.animate({
        left: '+=13',
        top: '-=25'
    }, 30, 'linear').animate({
        left: '-=13',
        top: '-=25'
    }, 30, 'linear', function() { zigzagDefense(el); });
}
window.onload = function() {
	var $j = jQuery.noConflict();
	param.max_density = +param.max_density; // unary plus to convert string to int
	param.min_density = +param.min_density;
	param.nb_spiders = +param.nb_spiders;
	param.invade_content = Boolean(param.invade_content);
	var surface = $j('body').height()*$j('body').width();
	var densityMax = Math.floor(surface/param.max_density);
	var densityMin = Math.floor(surface/param.min_density);
	var nbSpiders = param.nb_spiders > densityMax ? (densityMax < 1 ? 1 : densityMax) : Math.floor(param.nb_spiders);
	nbSpiders = (nbSpiders != 0 && nbSpiders < densityMin) ? densityMin : nbSpiders;
	for(i=0;i<nbSpiders;++i) {
        setTimeout(function() {
            spawnSpider();
        }, Math.floor(Math.random()*400) + 10);
    }
    setTimeout(function() {
    	spawnSpidersAtRandomIntervals(nbSpiders);
    }, Math.floor(Math.random()*60000) + 5000);
    $j("body").on("mouseover", ".spider-danger-zone", function() {
        zigzagDefense($j(this).data('spider'));
    }).on("click", ".spider-invader", function() {
        zigzagDefense($j(this));
    });
}