// pages/address/add/add.js
var api = require('../../../utils/api.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    consignee: '',
    address: '',
    mobile: '',
    //说明从购物车跳转过来的，添加成功后跳转到order/submit/submit
    returnType: '',
  },

  nameChange: function (e) {
    this.setData({ consignee: e.detail.value })
  },

  addressChange: function (e) {
    this.setData({ address: e.detail.value })
  },

  mobileChange: function (e) {
    this.setData({ mobile: e.detail.value })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({ returnType: options.returnType })
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
  //保存
  submit: function () {
    var consignee = this.data.consignee
    var address = this.data.address
    var mobile = this.data.mobile
    if (!consignee) {
      wx.showToast({
        title: '收货人不能为空',
        icon: 'none'
      })
      return
    }
    if (!address) {
      wx.showToast({
        title: '地址不能为空',
        icon: 'none'
      })
      return
    }
    if (!mobile) {
      wx.showToast({
        title: '手机号不能为空',
        icon: 'none'
      })
      return
    }
    api.post({
      url: 'user/public/addAddress',
      data: {
        consignee: consignee,
        address: address,
        mobile: mobile
      },
      success: data => {
        if (data.code == 1) {
          wx.showToast({
            title: '新增成功',
            icon: 'success',
            duration: 2000
          })
          //判断是否为购物车跳转过来
          if (this.data.returnType == 'submit') {
            wx.navigateTo({
              url: '../../order/submit/submit',
            })
          } else {
            //返回收货地址列表页
            wx.navigateBack()
          }
        }
      }
    });
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