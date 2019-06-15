// pages/cart/cart.js

var api = require('../../utils/api.js');
var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    carts: [],
    empty: true, //判断购物空是否为空
    selectAllStatus: true, //默认全选
    total: 0 //总金额
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
    this.getCarts()
  },

  /**
  * 加载购物车数据
  */
  getCarts: function () {
    api.get({
      url: 'goods/cart/cartList',
      data: {},
      success: data => {
        console.log(data)
        if (data.code == 1) {
          this.setData({ carts: data.data, empty: false })
          //总金额
          this.sum()
        }
        if (data.code == 0) {
          this.setData({ empty: true })
        }
      },
    });
  },

  /**
   * 数量设置
   */
  inputing: function (e) {
    //获取下标
    var index = e.currentTarget.dataset.index
    //获取数值
    var num = e.detail.value
    //购物车数据
    var carts = this.data.carts
    carts[index].num = num
    this.setData({ carts: carts })
    //更新数据库
    this.updateNum(carts[index].id, num)
    //更新总金额
    if (carts[index].selected == 1) {
      this.sum()
    }
  },

  /**
   * 数量减
   */
  bindMinus: function (e) {
    //获取下标
    var index = e.currentTarget.dataset.index
    //购物车数据
    var carts = this.data.carts
    //获取数值
    var num = carts[index].num
    if (num > 1) {
      num--
    }
    carts[index].num = num
    this.setData({ carts: carts })
    //更新数据库
    this.updateNum(carts[index].id, num)
    //更新总金额
    if (carts[index].selected == 1) {
      this.sum()
    }
  },

  /**
 * 数量加
 */
  bindPlus: function (e) {
    //获取下标
    var index = e.currentTarget.dataset.index
    //购物车数据
    var carts = this.data.carts
    //获取数值
    var num = carts[index].num
    num++
    carts[index].num = num
    this.setData({ carts: carts })
    //更新数据库
    this.updateNum(carts[index].id, num)
    //更新总金额
    if (carts[index].selected == 1) {
      this.sum()
    }
  },

  /**
   * 更新数据库购物车数量
   */
  updateNum: function (id, num) {
    api.get({
      url: 'goods/cart/updateNum',
      data: {
        id: id,
        num: num
      },
      success: data => {
        this.getCarts()
        //console.log(data);
      },
      complete: () => { }
    });
  },

  /**
   * 总金额
   */
  sum: function () {
    var carts = this.data.carts
    var total = 0
    for (var i = 0; i < carts.length; i++) {
      if (carts[i].selected) {
        total += carts[i].price * carts[i].num
      }
    }
    this.setData({ total: total })
  },

  /**
   * 删除购物车
   */
  deleteCart: function (e) {
    //获取下标
    var index = e.currentTarget.dataset.index
    var id = this.data.carts[index].id
    api.get({
      url: 'goods/cart/deleteCart',
      data: {
        id: id
      },
      success: data => {
        this.getCarts()
        //console.log(data);
      },
      complete: () => { }
    });
  },

  /**
   * 选中或非选中状态
   */
  selectBox: function (e) {
    //获取下标值
    var index = e.currentTarget.dataset.index
    var carts = this.data.carts
    //状态取反
    carts[index].selected = !carts[index].selected
    //重新赋值
    this.setData({ carts: carts })
    //重新计算总金额
    this.sum()
    //最后更新数据库
    this.updateSelect(carts[index].id, carts[index].selected)
  },

  updateSelect: function (id, selected) {
    //参数为商品ID和状态
    if (selected) {
      selected = 1
    } else {
      selected = 0
    }
    api.get({
      url: 'goods/cart/updateSelect',
      data: {
        id: id,
        selected: selected
      },
      success: data => {
        //console.log(data);
      },
      complete: () => { }
    });
  },

  /**
   * 全选与反选
   */
  selectAll: function () {
    var selectAllStatus = !this.data.selectAllStatus
    var carts = this.data.carts
    //遍历
    for (var i = 0; i < carts.length; i++) {
      carts[i].selected = selectAllStatus
    }
    //重新赋值
    this.setData({ carts: carts, selectAllStatus: selectAllStatus })
    //重新计算总金额
    this.sum()
    //最后更新数据库
    this.updateSelectAll(selectAllStatus)
  },

  updateSelectAll: function (selectAllStatus) {
    //参数为商品ID和状态
    if (selectAllStatus) {
      selectAllStatus = 1
    } else {
      selectAllStatus = 0
    }
    api.get({
      url: 'goods/cart/updateSelectAll',
      data: {
        selected: selectAllStatus
      },
      success: data => {
        //console.log(data);
      },
      complete: () => { }
    });
  },

  //立即结算
  userPay: function () {
    var carts = this.data.carts
    if (carts.length <= 0) {
      wx.showToast({
        title: '请先勾选商品',
        icon: 'none'
      })
      return
    } else {
      //遍历取出已勾选的cid
      var cartIds = []
      for (var i = 0; i < carts.length; i++) {
        if (carts[i].selected == 1) {
          cartIds.push(carts[i].id)
        }
      }
    }
    //将cartIds由数组转字符串，例如1,2
    cartIds = cartIds.join(',')
    //存于全局变量中
    app.globalData.cartIds = cartIds
    app.globalData.amount = this.data.total

    //判断是否有默认收货地址，没有跳转到添加地址页面，有则跳转订单提交页面
    api.get({
      url: 'user/public/haveAddress',
      data: {},
      success: data => {
        if (data.code == 1) {
          wx.navigateTo({
            url: '../order/submit/submit',
          })
        }
        if (data.code == 0) {
          wx.navigateTo({
            url: '../address/add/add?returnType=submit',
          })
        }
      },
      complete: () => {}
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

  see: function () {
    wx.switchTab({
      url: '../category/category',
    })
  }

})