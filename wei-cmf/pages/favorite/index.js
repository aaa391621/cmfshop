var api = require('../../utils/api.js');
var app = getApp();
Page({
  data: {
    systemInfo: {},
    _api: {},
    list: [],
    total: 0,
    loadingMore: false,
    noMoreData: false
  },
  currentPageNumber: 1,
  onLoad() {
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
  onShow() {
    // console.log(app.pagesNeedUpdate);
    // if (app.pagesNeedUpdate['pages/index/index'] == 1) {
    //     let newItems = api.updatePageList('id');
    //     console.log(newItems);
    //     this.setData({
    //         list: newItems
    //     });
    // }
    //
    // if (app.pagesNeedUpdate['pages/index/index'] == 'refresh') {
    //     this.onPullDownRefresh();
    // }
    this.pullUpLoad();

    // api.pageNeedUpdate('pages/index/index', 0);
  },

  /**
   * 下拉刷新
   */
  onPullDownRefresh() {
    this.currentPageNumber = 1;
    this.setData({
      noMoreData: false,
      noData: false
    });
    api.get({
      url: 'user/favorites/my',
      data: {},
      success: data => {
        let newItems = api.updatePageList('id', data.data.list, this.formatListItem, true);
        //console.log(newItems);
        this.setData({
          list: newItems
        });

        if (data.data.list.length > 0) {
          this.currentPageNumber++;
        } else {
          this.setData({
            noMoreData: true
          });

          // 没有数据
          // this.setData({
          //     noMoreData: true,
          //     noData: true
          // });
        }

        wx.stopPullDownRefresh();
      }
    });
  },

  /**
   * 上拉刷新
   */
  pullUpLoad() {
    if (this.data.loadingMore || this.data.noMoreData) return;
    this.setData({
      loadingMore: true
    });
    wx.showNavigationBarLoading();

    api.get({
      url: 'user/favorites/my',
      data: {
        page: this.currentPageNumber
      },
      success: data => {
        let newItems = api.updatePageList('id', data.data.list, this.formatListItem);
        //console.log(newItems);
        this.setData({
          list: newItems
        });
        if (data.data.list.length > 0) {
          this.currentPageNumber++;
        } else {
          this.setData({
            noMoreData: true
          });

          // 没有数据
          // this.setData({
          //     noMoreData: true,
          //     noData: true
          // });
        }
      },
      complete: () => {
        this.setData({
          loadingMore: false
        });
        wx.hideNavigationBarLoading();
      }
    });
  },
  formatListItem(item) {
    if (item.Thumbnail) {
      item.Thumbnail = api.getUploadUrl(item.Thumbnail);
    }
    return item;
  },
  onReachBottom() {
    this.pullUpLoad();
  },
  onListItemTap(e) {
    let id = e.currentTarget.dataset.object_id;

    wx.navigateTo({
      url: '/pages/article/article?id=' + id
    });

  },
  onListItemLongPress(e) {
    let id = e.currentTarget.dataset.id;
    wx.showActionSheet({
      itemList: ['取消收藏'],
      success: (res) => {
        //console.log(res.tapIndex)
        api.delete({
          url: 'user/favorites/' + id,
          data: {},
          success: data => {
            if (data.code) {
              let newItems = api.deletePageListItem(id);
              //console.log(newItems);
              this.setData({
                list: newItems
              });
              wx.showToast({
                title: '取消成功!',
                icon: 'success',
                duration: 1000
              });
            } else {
              wx.showToast({
                title: data.msg,
                icon: 'success',
                duration: 1000
              });
            }

          },
          complete: () => {

          }
        });
      },
      fail: function (res) {
        //console.log(res.errMsg)
      }
    });
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
  }
});
