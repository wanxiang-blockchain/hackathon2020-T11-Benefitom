var _common = {
  leftTree: function(leftTree) {
    leftTree.find("a").on("click", function() {
      $(this).addClass("active").siblings().removeClass("active");
      console.log(this)
    });
  },
  tableColor: function(elementsColor) {
    elementsColor.each(function(i) {
      if (i % 4 == 0) {
        $(this).css({
          "background": "#fbeeeb"
        });
      } else if (i % 4 == 1) {
        $(this).css({
          "background": "#faf8f0"
        });
      } else if (i % 4 == 2) {
        $(this).css({
          "background": "#e5f1ed"
        });
      } else if (i % 4 == 3) {
        $(this).css({
          "background": "#dfe9f4"
        });
      }
    })
  },
  formatDate: function(datevalue, flag) {
    var date = new Date(datevalue),
      year = date.getFullYear(),
      month = date.getMonth() + 1,
      day = date.getDate(),
      hour = date.getHours(),
      minutes = date.getMinutes(),
      seconds = date.getSeconds();
    var str = year + '-' + (month < 10 ? '0' + month : month) + '-' + (day <
      10 ? '0' + day : day);
    if (flag) {
      str += ' ' + (hour < 10 ? '0' + hour : hour) + ':' + (minutes < 10 ?
        '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds :
        seconds)
    }
    return str;
  },
  verificationCode: function() {
    //console.log('2345678ertyui..........');
    /* $('body').delegate('#getVerificationCode', 'click', function() {
      var obj = $(this);
      obj.attr('disabled', true);
      obj.removeClass('primary').addClass('grey');
      obj.parent().parent().parent().find($(".verificationCodeReminder"))
        .css({
          "display": "block"
        });
      var max = 60,
        ts = setInterval(function() {
          obj.val('已发送(' + (max--) + ')s');
          if (max < 0) {
            obj.attr('disabled', false);
            obj.val('重新获取');
            obj.removeClass('grey').addClass(
              'primary');
            clearInterval(ts);
            obj.removeAttr('disabled');
          }
        }, 1000)
    })*/
  },
  init: function() {
    var leftTree = $(".item>.menu");
    var noticeList = $(".noticeRoll ul li");
    if (leftTree.length) {
      this.leftTree(leftTree);
    }
    console.log(leftTree, "-------")
    if (noticeList.length) {
      setInterval(function() {
        var $self = $(".noticeRoll").find("ul:first");
        var lineHeight = $self.find("li:first").height();
        $self.animate({
          marginTop: -lineHeight + "px"
        }, 500, function() {
          $self.css({
            marginTop: "0px"
          }).find("li:first").appendTo($self);
        })
      }, 2000)
    }
    this.verificationCode();
  }
}
_common.init();

function msg_error(msg, id) {
    alert(msg)
  /*var new_msg = msg.split("\n");
  var new_msg_str = new_msg[0];*/
  // $('.msg_error').html('<b></b>' + msg);
  // $('.msg_error').show();
  // setTimeout(function() {
  //   $('.msg_error').fadeOut();
  // }, 2000);
  //
  // if (id) {
  //   $('input').removeAttr('style');
  //   $('input[id="' + id + '"]').css('border', '1px solid red');
  // } else {
  //   $('input').removeAttr('style');
  // }
  // return false;
}
