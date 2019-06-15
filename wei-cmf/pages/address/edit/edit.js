// pages/address/edit/edit.js
var api = require('../../../utils/api.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    id: 0,
    consignee: '',
    address: '',
    mobile: '',
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({ id: options.id })
    this.loadAddress(options.id)
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

  //加载地址
  loadAddress: function (id) {
    api.get({
      url: 'user/public/getAddressById',
      data: {
        id: id
      },
      success: data => {
        var address = data.data
        this.setData({
          id: address.id,
          consignee: address.consignee,
          address: address.address,
          mobile: address.mobile,
        })
      },
      complete: () => { }
    });
  },

  //编辑
  submit: function () {
    var id = this.data.id
    var consignee = this.data.consignee
    var address = this.data.address
    var mobile = this.data.mobile
    api.post({
      url: 'user/public/editAddress',
      data: {
        id: id,
        consignee: consignee,
        address: address,
        mobile: mobile
      },
      success: data => {
        if (data.code == 1) {
          wx.showToast({
            title: '编辑成功',
            icon: 'success'
          })
          //返回收货地址列表页
          wx.navigateBack()
        }
      }
    });
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