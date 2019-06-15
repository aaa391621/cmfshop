// pages/address/list/list.js
var api = require('../../../utils/api.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    address: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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
    this.loadData()
  },

  /**
   * 默认加载所有地址
   */
  loadData: function () {
    api.get({
      url: 'user/public/getAddress',
      data: {},
      success: data => {
        if (data.code == 1) {
          this.setData({ address: data.data })
        }
      },
      complete: () => { }
    });
  },

  //添加收货地址
  addAddress: function () {
    wx.navigateTo({
      url: '../add/add?returnType=add',
    })
  },

  //删除地址
  deleteAddress: function (e) {
    var that = this
    wx.showModal({
      title: '提示',
      content: '确定删除该地址吗？',
      success: function (res) {
        if (res.confirm) {
          var id = e.currentTarget.dataset.id
          api.delete({
            url: 'user/public/deleteAddress',
            data: {
              id: id
            },
            success: data => {
              if (data.code == 1) {
                wx.showToast({
                  title: data.msg,
                  icon: 'success',
                  duration: 2000
                })
                that.loadData()
              }
            }
          });
        }
      }
    })
  },

  //设默认地址
  setDefault: function (e) {
    var id = e.currentTarget.dataset.id
    api.post({
      url: 'user/public/setDefault',
      data: { id: id },
      success: data => {
        if (data.code == 1) {
          this.loadData()
        }
      }
    });
  },

  //编辑收货地址
  editAddress: function (e) {
    var id = e.currentTarget.dataset.id
    wx.navigateTo({
      url: '../edit/edit?id=' + id
    })
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