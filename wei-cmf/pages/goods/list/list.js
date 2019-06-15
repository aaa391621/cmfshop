// pages/goods/list/list.js
const util = require('../../../utils/util.js')

//获取应用实例
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    cid: 0, //分类ID
    page: 1,
    goods: [],
    empty: false,
    domain: app.globalData.domain,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var cid = options.cid
    this.setData({ cid: cid })
    this.getGoods(cid, 1) //默认取第1页
  },

  /**
   * 商品分类列表
   */
  getGoods: function (cid, page){
    var url = 'Goods/getGoods'
    var params = { cid: cid, page: page }
    util.wxRequest(url, params, data => {
      if (data.code == 200) {
        var goods = this.data.goods
        var newGoods = data.info.data
        if (newGoods.length > 0) {
          for (var i in newGoods) {
            goods.push(newGoods[i])
          }
          this.setData({ goods: goods, empty: false })
        } else if (goods.length == 0) {
          this.setData({ empty: true })
        } else {
          wx.showToast({
            title: '没有更新记录了',
            icon: 'none'
          })
        }
        wx.stopPullDownRefresh()
      }
    }, data => { }, data => { })
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
    wx.showToast({
      title: '刷新中',
      icon: 'loading'
    })
    this.setData({ goods: [], page: 1 })
    this.getGoods(this.data.cid, 1)
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    wx.showToast({
      title: '加载中',
      icon: 'loading'
    })
    this.getGoods(this.data.cid, ++this.data.page)
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },

  /**
 * 商品详情
 */
  showDetail: function (e) {
    // console.log(e)
    var goodsId = e.currentTarget.dataset.id
    wx.navigateTo({
      url: '../detail/detail?goodsId=' + goodsId
    })
  }

})