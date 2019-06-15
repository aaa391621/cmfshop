var api = require('../../utils/api.js');
Page({

  data: {
    _api: {},
  },

  onLoad() {
    this.loadIndex();
    this.loadGoods();
    try {
      var isLogin = wx.getStorageSync('login');
      if (!isLogin) {
        api.login();
        return;
      }
    } catch (e) {
      // Do something when catch error
    }

    this.setData({
      _api: api,
    });

  },
  onShow() { },

  /**
   * 加载幻灯片
   */
  loadIndex: function () {
    var banner = wx.getStorageSync('banner')
    if (banner) {
      this.setData({ banner: banner })
    } else {
      api.get({
        url: '/home/slides/1',
        data: {},
        success: data => {
          //console.log(data);
          this.setData({ banner: data.data.items })
          wx.setStorageSync('banner', data.data.items)
        },
        complete: () => { }
      });
    }
  },
  /**
 * 加载新品推荐 
 */
  loadGoods: function () {
      api.get({
        url: '/goods/lists/getCategoryPostLists',
        data: {
          category_id: 1,
          limit: 8
        },
        success: data => {
          //console.log(data);
          this.setData({ goods: data.data.list })
          wx.setStorageSync('goods', data.data.list)
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
   * 搜索功能
   */
  searchGoods: function () {
    var keywords = this.data.keywords
    if (keywords != '') {
      wx.navigateTo({
        url: '../search/search?keywords=' + keywords
      })
    }
  },

  /**
  * 商品详情
  */
  showDetail: function (e) {
    // console.log(e)
    var goodsId = e.currentTarget.dataset.goodsId
    wx.navigateTo({
      url: '../goods/detail/detail?goodsId=' + goodsId
    })
  },

  // 全部分类
  showCategory: function () {
    wx.switchTab({
      url: '../category/category',
    })
  },

  // 购物车
  showCarts: function () {
    wx.switchTab({
      url: '../cart/cart',
    })
  },
  // 个人中心
  showMine: function () {
    wx.switchTab({
      url: '../my/my',
    })
  }



});
