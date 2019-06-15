import "./date";

var app = getApp();

var host = "http://cmfshop.xl.cn";

var tryingLogin = false;

module.exports = {
    HOST: host,
    API_ROOT: host + '/api/',
    API_VERSION: '1.1.0',  
    post(options) {
        this.request(options);
    },
    get(options) { 
        options.method = 'GET';
        this.request(options); 
    },
    delete(options) {
        options.method = 'DELETE';
        this.request(options); 
    },
    put(options) {
        options.method = 'PUT';
        this.request(options);
    },
    request(options) {
        var apiRoot = this.API_ROOT;
        var token   = '';
        try {
            token = wx.getStorageSync('token')
        } catch (e) {
            // Do something when catch error
        }

        var requireLogin = true;

        if (typeof options.login == 'undefined' || options.login == true) {
            requireLogin = true;
        } else {
            requireLogin = false;
        }

        wx.request({
            url: apiRoot + options.url,
            data: options.data,
            method: options.method ? options.method : 'POST',
            header: {
                'Cache-Control': 'no-cache',
                'Content-Type': 'application/x-www-form-urlencoded',
                'XX-Token': token,
                'XX-Device-Type': 'wxapp',
                'XX-Api-Version': this.API_VERSION
            },
            success: res => {
                var data = res.data;
                if (data.code == 10001 && requireLogin) {

                    if (!tryingLogin) {
                        tryingLogin        = true;
                        var hasGetUserInfo = wx.getStorageSync('hasGetUserInfo');
                        if (hasGetUserInfo) {
                            wx.showToast({
                                title: '正在重新登录',
                                icon: 'success',
                                duration: 1000
                            });
                            setTimeout(() => {
                                this.login();
                            }, 1000);
                        } else {
                            this.login();
                        }

                    }
                    // 登录注册
                    let currentPages = getCurrentPages();

                    console.log('-------no login!---------');

                    let currentRoute = currentPages.pop()['__route__'];
                    if (currentRoute != 'pages/login/login') {
                        wx.navigateTo({
                            url: '/pages/login/login'
                        });
                    }

                } else {
                    options.success(data);
                }

            },
            fail: function (res) {
                if (options.fail) {
                    options.fail(res)
                }
            },
            complete: options.complete ? options.complete : null
        });
    },
    login: function () {
        wx.login({
            success: loginRes => {
                console.log(loginRes);
                if (loginRes.code) {
                    wx.getUserInfo({
                        withCredentials: true,
                        success: res => {
                            console.log(res);
                            wx.setStorageSync('hasGetUserInfo', '1');
                            this.post({
                                url: 'wxapp/public/login',
                                data: {
                                    code: loginRes.code,
                                    encrypted_data: res.encryptedData,
                                    iv: res.iv,
                                    raw_data: res.rawData,
                                    signature: res.signature
                                },
                                success: data => {
                                    if (data.code == 1) {
                                        wx.showToast({
                                            title: '登录成功!',
                                            icon: 'success',
                                            duration: 1000
                                        });

                                        try {
                                            wx.setStorageSync('login', '1');
                                            wx.setStorageSync('token', data.data.token);
                                            wx.setStorageSync('user', data.data.user);
                                        } catch (e) {
                                        }

                                        setTimeout(function () {
                                            wx.switchTab({
                                                url: '/pages/index/index',
                                                success: res => {
                                                    //getCurrentPages()[0].onPullDownRefresh()
                                                }
                                            });
                                        }, 1000);
                                    }

                                },
                                complete: () => {
                                    tryingLogin = false;
                                }
                            });
                        },
                        fail: (res) => {
                            console.log(res);
                            tryingLogin = false;
                            if (res.errMsg == "getUserInfo:cancel" || res.errMsg == "getUserInfo:fail auth deny") {
                                wx.showModal({
                                    title: '用户授权失败',
                                    showCancel: false,
                                    content: '请删除此小程序重新授权!',
                                    success: function (res) {
                                        if (res.confirm) {
                                            console.log('用户点击确定')
                                        }
                                    }
                                });
                            }

                        }
                    });


                } else {
                    tryingLogin = false;
                }
            },
            fail: () => {
                tryingLogin = false;
            }
        });
    },
    uploadFile(options) {

        options.url = this.API_ROOT + options.url;

        let token = this.getToken();

        let that = this;

        let oldSuccess  = options.success;
        options.success = function (res) {
            console.log(res.data);
            let data = JSON.parse(res.data);
            console.log(data);
            if (data.code == 0 && data.data && data.data.code && data.data.code == 10001) {
                // wx.navigateTo({
                //     url: '/pages/login/login'
                // });
                that.login();
            } else {
                oldSuccess(data);
            }
        }

        options.header = {
            'Content-Type': 'multipart/form-data',
            'XX-Token': token,
            'XX-Device-Type': 'wxapp',
            'XX-Api-Version': this.API_VERSION
        };
        wx.uploadFile(options);

    },
    getToken() {
        var token = '';
        try {
            token = wx.getStorageSync('token')
        } catch (e) {
            // Do something when catch error
        }

        return token;
    },
    json2Form(json) {
        var str = []
        for (var p in json) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(json[p]))
        }
        return str.join("&")
    },
    timeFormat(second, fmt) {
        let mDate = new Date();
        mDate.setTime(second * 1000);
        return mDate.Format(fmt);
    },
    getCurrentPageUrl() {
        let currentPages = getCurrentPages();
        let currentPage  = currentPages.pop();
        let page         = currentPage['__route__'];
        let pageParams   = [];

        if (currentPage.params) {
            for (let key in currentPage.params) {
                pageParams.push(key + "=" + currentPage.params[key]);
            }
        }

        if (pageParams.length > 0) {
            page = page + '?' + pageParams.join("&");
        }

        return page;
    },
    /**
     *
     * @param itemKey
     * @param newItems
     * @param formatCallback
     * @param replace
     * @param listKey
     * @returns {Array}
     */
    updatePageList(itemKey, newItems, formatCallback, replace, listKey) {
        let page = this.getCurrentPageUrl();

        console.log(page + "ddd");

        return this.updatePageListByPage(page, itemKey, newItems, formatCallback, replace, listKey);
    },
    /**
     *
     * @param page
     * @param itemKey
     * @param newItems
     * @param formatCallback
     * @param replace
     * @param listKey
     * @returns {Array}
     */
    updatePageListByPage(page, itemKey, newItems, formatCallback, replace, listKey) {
        listKey = listKey ? listKey : 'list';

        console.log(page);

        if (!app.pagesData.hasOwnProperty(page)) {
            app.pagesData[page] = {};
        }

        if (!app.pagesData[page][listKey] || replace) {
            app.pagesData[page][listKey] = {};
        }

        if (newItems) {
            newItems.forEach(item => {
                let uniqueValue = '_' + item[itemKey];
                if (formatCallback && typeof formatCallback == "function") {
                    item = formatCallback(item);
                }
                app.pagesData[page][listKey][uniqueValue] = item;
            });
        }


        let list = [];

        for (let key in app.pagesData[page][listKey]) {
            list.push(app.pagesData[page][listKey][key]);
        }

        console.log(list);

        return list;
    },
    /**
     *
     * @param key
     * @param newItem
     * @param listKey
     * @returns {*|Array}
     */
    updatePageListItem(key, newItem, formatCallback, listKey) {
        let page = this.getCurrentPageUrl();

        return this.updatePageListItemByPage(page, key, newItem, formatCallback, listKey);
    },
    /**
     *
     * @param page
     * @param key
     * @param newItem
     * @param listKey
     * @returns {Array}
     */
    updatePageListItemByPage(page, key, newItem, formatCallback, listKey) {
        listKey = listKey ? listKey : 'list';

        if (!app.pagesData.hasOwnProperty(page)) {
            app.pagesData[page] = {};
        }

        if (!app.pagesData[page][listKey]) {
            app.pagesData[page][listKey] = {};
        }

        if (formatCallback && typeof formatCallback == "function") {
            newItem = formatCallback(newItem);
        }

        key = '_' + key;

        app.pagesData[page][listKey][key] = Object.assign(app.pagesData[page][listKey][key], newItem);


        let list = [];

        for (let key in app.pagesData[page][listKey]) {
            list.push(app.pagesData[page][listKey][key]);
        }

        console.log(list);

        return list;
    },
    /**
     *
     * @param key
     * @param listKey
     * @returns {*|Array}
     */
    deletePageListItem(key, listKey) {
        let page = this.getCurrentPageUrl();

        return this.deletePageListItemByPage(page, key, listKey);
    },
    /**
     *
     * @param page
     * @param key
     * @param listKey
     * @returns {Array}
     */
    deletePageListItemByPage(page, key, listKey) {
        listKey = listKey ? listKey : 'list';

        if (!app.pagesData.hasOwnProperty(page)) {
            app.pagesData[page] = {};
        }

        if (!app.pagesData[page][listKey]) {
            app.pagesData[page][listKey] = {};
        }

        key = '_' + key;

        delete app.pagesData[page][listKey][key];


        let list = [];

        for (let key in app.pagesData[page][listKey]) {
            list.push(app.pagesData[page][listKey][key]);
        }

        console.log(list);

        return list;
    },
    pageNeedUpdate(page, needUpdate) {
        app.pagesNeedUpdate[page] = needUpdate;
    },
    getUploadUrl(file) {
        if (file && file.indexOf('http') === 0) {
            return file;
        }
        return this.HOST + '/upload/' + file;
    }

};
