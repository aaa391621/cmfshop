# cmfshop
后台文件：cmfshop
<br>小程序文件：wei-cmf
<br>数据库: thinkcmfshop.sql
<br>将域名指向cmfshop/public
<br>访问后台：域名/admin,账号:admin,密码:123456
<br>后台插件列表，小程序插件填写相关信息
<br>修改wei-cmf/utils/api.js里的host域名
<br>支付配置在后台设置/微信设置
<br>问题反馈：qq:29285674 邮箱：29285674@qq.com</font><br>
<br>本程序仅供学习和二次开发使用，本项目停止更新，近期在做UNIAPP版本，敬请期待
<br> 伪静态配置：
location / {
    index  index.php index.html index.htm;
     #如果请求既不是一个文件，也不是一个目录，则执行一下重写规则
     if (!-e $request_filename)
     {
        #地址作为将参数rewrite到index.php上。
        rewrite ^/(.*)$ /index.php?s=$1;
        #若是子目录则使用下面这句，将subdir改成目录名称即可。
        #rewrite ^/subdir/(.*)$ /subdir/index.php?s=$1;
     }
}

location /api/ {
    index  index.php index.html index.htm;
     #如果请求既不是一个文件，也不是一个目录，则执行一下重写规则
     if (!-e $request_filename)
     {
        #地址作为将参数rewrite到index.php上。
        #rewrite ^/(.*)$ /index.php?s=$1;
        #若是子目录则使用下面这句，将subdir改成目录名称即可。
        rewrite ^/api/(.*)$ /api/index.php?s=$1;
     }
}
<br>
<br>![image](http://wx4.sinaimg.cn/mw690/0060lm7Tly1fw3bb82rbdj30cc0kl0u6.jpg)
<br>![image](http://wx1.sinaimg.cn/mw690/0060lm7Tly1fw3bc2c9tfj30bg0ju406.jpg)
<br>![image](http://wx4.sinaimg.cn/mw690/0060lm7Tly1fw3bcf9wq5j30bp0k6wfr.jpg)
<br>![image](http://wx3.sinaimg.cn/mw690/0060lm7Tly1fw3bbs42ioj30b90jracz.jpg)
<br>![image](http://wx3.sinaimg.cn/mw690/0060lm7Tly1fw3bctna4gj30cn0c4dgn.jpg)
<br>![image](http://wx3.sinaimg.cn/mw690/0060lm7Tly1fw3bf7736vj31gs0dtabb.jpg)
<br>![image](http://wx1.sinaimg.cn/mw690/0060lm7Tly1fw3bg2j670j31h507sdgd.jpg)
<br>![image](http://wx2.sinaimg.cn/mw690/0060lm7Tly1fw3bg5s6slj30wg0bgglu.jpg)
