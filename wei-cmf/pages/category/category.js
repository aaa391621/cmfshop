// pages/category/category.js
var api = require('../../utils/api.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    categoryList: [], //分类
    subCategoryList: [], //分类下商品列表
    highlight: [],
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getCategory()
    this.getSubCategory()
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

  },

  /**
   * 获取分类
   */
  getCategory: function (id) {
    if (id == undefined) {
      id = 1
    }
    api.get({
      url: '/goods/categories',
      data: {},
      success: data => {
        //console.log(data);
        this.setData({
          categoryList: data.data.list
        })
        this.setHighlight(0)
      },
      complete: () => {}
    });
  },

  /**
   * 设置一级分类高亮
   */
  setHighlight: function (index) {
    var highlight = []
    for (var i = 0; i < this.data.categoryList; i++) {
      highlight[i] = ''
    }
    highlight[index] = 'highlight'
    this.setData({ highlight: highlight })
  },

  /**
   * 点击一级分类获取分类下商品
   */
  getSubCategory: function (e) {
    if (e == undefined) {
      var cid = 1
      var index = 1
    }else{
      var cid = e.currentTarget.dataset.id
      var index = e.currentTarget.dataset.index
    }
    this.setHighlight(index)
    api.get({
      url: '/goods/lists/getCategoryPostLists',
      data: {
        category_id: cid,
        field: 'post.post_title,post.more,post.id'
      },
      success: data => {
        //console.log(data);
        this.setData({
          subCategoryList: data.data.list
        })
      },
      complete: () => { }
    });
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

})