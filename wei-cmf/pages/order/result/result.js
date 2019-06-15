// pages/order/result/result.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    orderId: 0,
    payResult: 0,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      orderId: options.orderId,
      payResult: options.status
    })
  },

  //继续购物
  continueView: function() {
    wx.switchTab({
      url: '../../index/index'
    })
  },

  //查看订单详情
  showDetails: function() {
    wx.navigateTo({
      url: '../details/details?orderId=' + this.data.orderId
    })
  },

  //返回订单列表
  returnOrderList: function () {
    wx.navigateTo({
      url: '../list/list'
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