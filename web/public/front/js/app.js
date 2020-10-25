$(document)
  .ready(function() {

    // fix menu when passed
    // $('.masthead').visibility({
    //   once: false,
    //   onBottomPassed: function() {
    //     $('.fixed.menu').transition('fade in');
    //   },
    //   onBottomPassedReverse: function() {
    //     $('.fixed.menu').transition('fade out');
    //   }
    // });

    // create sidebar and attach to menu open
    $('.ui.sidebar').sidebar('attach events', '.launch .icon');
    $('.ui.menu  .ui.dropdown').dropdown({
      on: 'hover'
    });
    $('.right.menu  .ui.dropdown').dropdown({
      on: 'hover'
    });
    $('.right.menu .ui.dropdown.wechat').dropdown({
      on: 'hover'
    });
    $('.itemsAbout .menu .item')
      .tab();
    $('.ui.checkbox')
      .checkbox();
    $('.tabular.menu .item').tab();
    jQuery.validator.addMethod("isMobile", function(value, element) { //为插件添加验证手机方法
      var tel = /[1][34578]\d{9}/;
      return tel.test(value) || this.optional(element);
    }, "请输入正确的手机号码");
  });
