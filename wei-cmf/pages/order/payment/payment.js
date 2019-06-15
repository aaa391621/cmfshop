// pages/order/payment/payment.js
//获取应用实例
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    order:[],
    wxdata:[],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      order: app.globalData.order,
      wxdata: app.globalData.wxdata
    })
    // console.log(this.data.wxdata)
  },

  // 发起微信支付
  pay: function() {
    var that = this
    var wxdata = this.data.wxdata
    
    var timeStamp = wxdata.timeStamp + ''
    var nonceStr = wxdata.nonceStr + ''
    var wxpackage = wxdata.package + ''
    var paySign = wxdata.sign + ''

    wx.requestPayment({
      'timeStamp': timeStamp,
      'nonceStr': nonceStr,
      'package': wxpackage,
      'signType': 'MD5',
      'paySign': paySign,
      'success': function (res) {
        wx.navigateTo({
          url: '../result/result?status=1&orderId=' + that.data.order.id
        })
      },
      'fail': function (res) {
        wx.navigateTo({
          url: '../result/result?status=0&orderId=' + that.data.order.id
        })
      }
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})