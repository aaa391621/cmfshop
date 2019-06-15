var api = require('../../utils/api.js')
var app = getApp()
Page({
    data: {},

    onLoad() {
    },
    formSubmit: function (e) {
        //console.log(api.json2Form(e.detail.value));
        console.log(e.detail.value);
        e.detail.value.device_type = 'mobile';
        api.post({
            url: 'user/public/login',
            data: e.detail.value,
            success: data => {
                if (data.code == 0) {
                    wx.showModal({
                        content: data.msg,
                        showCancel: false,
                        success: function (res) {
                            if (res.confirm) {
                                console.log('用户点击确定')
                            }
                        }
                    })
                }

                if (data.code == 1) {
                    wx.showToast({
                        title: '登录成功!',
                        icon: 'success',
                        duration: 2000
                    });

                    try {
                        wx.setStorageSync('login', '1');
                        wx.setStorageSync('token', data.data.token);
                        console.log("success");
                        wx.navigateBack({
                            delta: 2
                        });
                    } catch (e) {
                        console.log(e);
                        // Do something when catch error
                    }

                }


                console.log(data);
            }
        });
    },
    handleGetUserInfo(){
        api.login();
    }
})
