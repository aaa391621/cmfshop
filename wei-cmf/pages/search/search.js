// pages/search/search.js
var api = require('../../utils/api.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    keywords: '',
    page: 1,
    goods: [],
    empty: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var keywords = options.keywords
    if (keywords != '') {
      this.setData({ keywords: keywords })
      this.getGoods(keywords, 1) //默认取第1页
    } else {
      this.setData({ empty: true })
    }
  },

  /**
   * 商品搜索
   */
  getGoods: function (keywords, page) {
    api.get({
      url: '/goods/search',
      data: {
        keyword: keywords,
        page: page + ',8'
      },
      success: data => {
        console.log(data); 
        var goods = this.data.goods
        var newGoods = data.data.list
        if (newGoods.length > 0) {
          for (var i in newGoods) {
            goods.push(newGoods[i])
          }
          this.setData({ goods: goods, empty: false })
        } else if (goods.length == 0) {
          this.setData({ empty: true })
        } else {
          wx.showToast({
            title: '没有更多记录了',
            icon: 'none'
          })
        }
        wx.stopPullDownRefresh()
      },
      complete: () => { }
    });
  },

  /**
   * 获取搜索框的值
   */
  inputing: function (e) {
    this.setData({ keywords: e.detail.value })
  },

  /**
   * 搜索商品
   */
  searchGoods: function () {
    var keywords = this.data.keywords
    this.setData({ goods: [], page: 1 })
    if (keywords != '') {
      this.getGoods(keywords, 1)
    }
  },

  /**
   * 商品详情
   */
  showDetail: function (e) {
    // console.log(e)
    var goodsId = e.currentTarget.dataset.id
    wx.navigateTo({
      url: '../goods/detail/detail?goodsId=' + goodsId
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
   * 页面相关事件处理函数--监听用户下拉动作（下拉刷新）
   */
  onPullDownRefresh: function () {
    wx.showToast({
      title: '刷新中',
      icon: 'loading'
    })
    this.setData({ goods: [], page: 1 })
    this.getGoods(this.data.keywords, 1)
  },

  /**
   * 页面上拉触底事件的处理函数(上拉加载)
   */
  onReachBottom: function () {
    wx.showToast({
      title: '加载中',
      icon: 'loading'
    })
    this.getGoods(this.data.keywords, ++this.data.page)
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})