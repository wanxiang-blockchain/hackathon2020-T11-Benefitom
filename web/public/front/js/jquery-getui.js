//图片轮播
$(function() {
  var now = 0;
  var $slider = $(".slider");
  var $sliderContent = $slider.find(".slider-content");
  var $items = $sliderContent.find(".slider-item");
  var $leftControl = $slider.find(".slider-left-control");
  var $rightControl = $slider.find(".slider-right-control");
  var $indicators = $(".slider-indicator li");
  var timer;
  var interval = 5000;
  var len = $items.length;
  var slideWidth = $items.eq(0).width();

  for (var l = $indicators.length, i = 0; i < l; i++) {
    $indicators[i].index = i;
  }
  $indicators.eq(now).addClass("active");
  $items.eq(now).addClass("active");
  slide();
  $(".slider-indicator").on("click", "li", function(ev) {
    if (now % len == ev.target.index) return;
    goto(ev.target.index);
  });
  $slider.on("mouseover", function() {
    clearInterval(timer);
    timer = null;
  });
  $slider.on("mouseout", function() {
    slide();
  });
  $leftControl.on("click", function() {
    prev();
  });

  $rightControl.on("click", function() {
    next();
  });

  function goto(num) {
    var x = now % len,
      y = num % len;
    $indicators.eq(x).removeClass("active");
    $indicators.eq(y).addClass("active");
    if (now > num) {
      $items.eq(x).stop().css("left", 0).animate({
        left: slideWidth
      }, function() {
        $items.eq(x).removeClass("active");
      });
      $items.eq(y).stop().css("left", -slideWidth).addClass("active").animate({
        left: 0
      });
    } else {
      $items.eq(x).stop().css("left", 0).animate({
        left: -slideWidth
      }, function() {
        $items.eq(x).removeClass("active");
      });
      $items.eq(y).stop().css("left", slideWidth).addClass("active").animate({
        left: 0
      });

    }
    now = num;
  }

  function prev() {
    goto(now - 1);
  }

  function next() {
    goto(now + 1);
  }

  function slide() {
    clearInterval(timer);
    timer = setInterval(function() {
      next();
    }, interval);
  }

  var timer1 = setTimeout(function() {
    $(window).on("resize", function() {
      clearTimeout(timer1);
      slideWidth = $items.eq(0).width();
    })
  }, 2000);

});
