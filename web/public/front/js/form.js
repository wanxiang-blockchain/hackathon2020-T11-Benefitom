var _form = {
  register: function(registerForm) {
    registerForm.validate({
      rules: {
        phone: {
          required: true,
          isMobile: true
        },
        captcha: "required",
        verificationCode: "required",
        password: {
          required: true,
          minlength: 6,
          maxlength: 20
        },
        agreement: {
          required: true
        }
      },
      messages: {
        agreement: {
          required: "请勾选用户协议"
        }
      }
    })
  },
  login: function(loginForm) {
    loginForm.validate({
      rules: {
        phone: {
          required: true,
          isMobile: true
        },
        password: {
          required: true
        }
      }
    })
  },
  resetPasswordOne: function(resetPasswordOneForm) {
    resetPasswordOneForm.validate({
      rules: {
        resetPwPhone: {
          required: true,
          isMobile: true
        },
        resetPwCaptcha: {
          required: true
        }
      }
    })
  },
  resetPasswordTwo: function(resetPasswordTwoForm) {
    resetPasswordTwoForm.validate({
      rules: {
        resetPwTwoCaptcha: {
          required: true,
          minlength: 6,
          maxlength: 6
        },
        newPassword: {
          required: true,
          minlength: 6,
          maxlength: 20
        },
        againPassword: {
          required: true,
          equalTo: "#newPassword"
        }
      }
    })
  },
  tradePassword: function(tradePasswordForm) {
    tradePasswordForm.validate({
      rules: {
        tradePasswordPhone: {
          required: true,
          isMobile: true
        },
        tradePasswordVerificationCode: {
          required: true
        },
        tradePasswordNewPassword: {
          required: true
        },
        tradePasswordAgainPassword: {
          required: true
        }
      }
    })
  },
  changePhoneOne: function(changePhoneOneForm) {
    changePhoneOneForm.validate({
      rules: {
        changePhoneOnePhone: {
          required: true,
          isMobile: true
        },
        changePhoneOneCaptcha: {
          required: true,
          minlength: 5,
          maxlength: 5
        },
        changePhoneOneCode: {
          required: true,
          minlength: 6,
          maxlength: 6
        }
      }
    })
  },
  changePhoneTwo: function(changePhoneTwoForm) {
    changePhoneTwoForm.validate({
      rules: {
        changePhoneTwoPhone: {
          required: true,
          isMobile: true
        },
        changePhoneTwoCaptcha: {
          required: true,
          minlength: 5,
          maxlength: 5
        },
        changePhoneTwoCode: {
          required: true,
          minlength: 6,
          maxlength: 6
        }
      }
    })
  },
  rechange: function(rechangeForm) {
    rechangeForm.validate({
      rules: {
        rechangeAmount: {
          required: true,
          number: true
        }
      }
    })
  },
  withdraw: function(withdrawForm) {
    withdrawForm.validate({
      rules: {
        withdrawAmount: {
          required: true,
          number: true
        },
        withdrawId: {
          required: true
        }
      }
    })
  },
  resetTradePw: function(resetTradePwForm) {
    resetTradePwForm.validate({
      rules: {
        resetTradePwPhone: {
          required: true,
          isMobile: true
        },
        resetTradePwCode: {
          required: true
        },
        resetTradePwNewPassword: {
          required: true,
          minlength: 6,
          maxlength: 20
        },
        resetTradePwAgainPassword: {
          required: true,
          equalTo: "#resetTradePwNewPassword"
        }
      }
    })
  },
  init: function() {
    var registerForm = $("#registerForm");
    var loginForm = $("#loginForm");
    var resetPasswordOneForm = $("#resetPasswordOne");
    var resetPasswordTwoForm = $("#resetPasswordTwo");
    var tradePasswordForm = $("#tradePasswordForm");
    var changePhoneOneForm = $("#changePhoneOneForm");
    var changePhoneTwoForm = $("#changePhoneTwoForm");
    var rechangeForm = $("#rechangeForm");
    var withdrawForm = $("#withdrawForm");
    var resetTradePwForm = $("#resetTradePwForm");
    if (registerForm.length) {
      this.register(registerForm);
    }
    if (loginForm.length) {
      this.login(loginForm);
    }
    if (resetPasswordOneForm.length) {
      this.resetPasswordOne(resetPasswordOneForm);
    }
    if (resetPasswordTwoForm.length) {
      this.resetPasswordTwo(resetPasswordTwoForm);
    }
    if (tradePasswordForm.length) {
      this.tradePassword(tradePasswordForm);
    }
    if (changePhoneOneForm.length) {
      this.changePhoneOne(changePhoneOneForm);
    }
    if (changePhoneTwoForm.length) {
      this.changePhoneTwo(changePhoneTwoForm);
    }
    if (rechangeForm.length) {
      this.rechange(rechangeForm);
    }
    if (withdrawForm.length) {
      this.withdraw(withdrawForm);
    }
    if (resetTradePwForm.length) {
      this.resetTradePw(resetTradePwForm);
    }
  }
}
_form.init();
