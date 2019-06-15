// pages/goods/detail/detail.js
const util = require('../../../utils/util.js')
var WxParse = require('../../../wxParse/wxParse.js');
var api = require('../../../utils/api.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    tab: 0,
    tabClass: ['text-select', 'text-normal'],
    goodsId: 0, //商品ID
    images: [],
    goodsInfo: [],
    swiperHeight: 0,
    goodsNum: 1, //商品数量
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({ goodsId: options.goodsId, swiperHeight: util.getWindowSize().screenWidth })
    this.getGoodsInfo()
  },

  getGoodsInfo: function () {
    api.get({
      url: '/goods/articles/' + this.data.goodsId,
      data: {},
      success: data => {
        //console.log(data)
        WxParse.wxParse('article', 'html', data.data.post_content, this, 5)
        this.setData({ images: data.data.more.photos, goodsInfo: data.data })
      },
      complete: () => { }
    });
  },

  /**
   * 购买数量减
   */
  bindMinus: function () {
    var num = this.data.goodsNum
    if (num > 1) {
      num--
    }
    this.setData({ goodsNum: num })
  },

  /**
   * 购买数量值
   */
  inputing: function (e) {
    var num = e.detail.value
    if (num < 1) {
      num = 1
    }
    this.setData({ goodsNum: num })
  },

  /**
   * 购买数量加
   */
  bindPlus: function () {
    var num = this.data.goodsNum
    num++
    this.setData({ goodsNum: num })
  },

  /**
   * 商品收藏或取消收藏
   */
  addCollect: function (e) {
    var that = this
    var gid = e.currentTarget.dataset.id
    //判断是否收藏
    api.get({
      url: 'user/favorites/hasFavorite',
      data: {
        object_id: gid,
        table_name: 'goods_post'
      },
      success: data => {
        //取消收藏
        if (data.code == 1) {
          var sid = data.data.id
          api.delete({
            url: 'user/favorites/' + sid,
            data: {},
            success: data => {
              if (data.code == 1) {
                wx.showToast({
                  title: data.msg,
                  icon: 'success',
                  duration: 2000
                })
              }
              if (data.code == 0) {
                wx.showToast({
                  title: data.msg,
                  icon: 'error',
                  duration: 2000
                })
              }

              console.log(data);
            }
          });
        }
        //添加收藏
        if (data.code == 0) {
          api.post({
            url: 'user/favorites',
            data: {
              object_id: gid,
              table_name: 'goods_post',
              url: JSON.stringify({ "action": "goods/Article/index", "param": { "id": gid } }),
              title: that.data.goodsInfo.post_title,
              description: that.data.goodsInfo.more.thumbnail,
              price: that.data.goodsInfo.post_price
            },
            success: data => {
              if (data.code == 1) {
                wx.showToast({
                  title: data.msg,
                  icon: 'success',
                  duration: 2000
                })
              }
              if (data.code == 0) {
                wx.showToast({
                  title: data.msg,
                  icon: 'error',
                  duration: 2000
                })
              }
            }
          });
        }
      }
    });
  },

  /**
   * 加入购物车
   */
  addCart: function () {
    var gid = this.data.goodsId
    var goodsNum = this.data.goodsNum
    api.post({
      url: 'goods/cart/add',
      data: {
        gid: gid,
        goodsNum: goodsNum
      },
      success: data => {
        if (data.code == 1) {
          wx.showToast({
            title: data.msg,
            icon: 'success',
            duration: 2000
          })
        }
        if (data.code == 0) {
          wx.showToast({
            title: data.msg,
            icon: 'none',
            duration: 2000
          })
        }
        //console.log(data);
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

  },

  tabClick: function (e) {
    var index = e.currentTarget.dataset.index
    var classs = ['text-normal', 'text-normal']
    classs[index] = 'text-select'
    this.setData({ tabClass: classs, tab: index })
  },

  /**
   * 立即购买：检查该商品是否已加入购物车，
   * 加入则跳转购物车页面，未加入则先加入购物车再跳转
   */
  buy: function () {
    api.post({
      url: 'goods/cart/checkCart',
      data: {
        gid: this.data.goodsInfo.id,
        goodsNum: this.data.goodsNum,
      },
      success: data => {
        if (data.code == 1) {
          wx.switchTab({
            url: '../../cart/cart',
          })
        }
        if (data.code == 0) {
          wx.showToast({
            title: data.msg,
            icon: 'none',
            duration: 2000
          })
        }
      }
    });
  }

})