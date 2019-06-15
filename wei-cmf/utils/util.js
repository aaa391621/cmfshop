const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}

const wxRequest = (url, params, successCallback, errorCallback, completeCallback) => {
  wx.request({
    url: getApp().globalData.domain + 'index.php/api/' + url,
    data: params || {},
    header: { 'content-type': 'application/json' },
    method: 'POST',
    success: function (res) {
      if (res.statusCode == 200) {
        successCallback(res.data)
      } else {
        errorCallback(res)
      }
    },
    fail: function (res) {
      errorCallback(res)
    },
    complete: function (res) {
      completeCallback(res)
    }
  })
}

//设置信息
const getWindowSize = () => {
  var data = {}
  wx.getSystemInfo({
    success: function (res) {
      data.screenWidth = res.windowWidth
      data.screenHeight = res.windowHeight
    }
  })
  return data
}

module.exports = {
  formatTime: formatTime,
  wxRequest: wxRequest,
  getWindowSize: getWindowSize
}
