<!DOCTYPE html>
<html lang="en">
<head>
    <meta name='viewport', content='width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no' />
    <meta charset="UTF-8">
    <title>艺行派下载</title>
    <style>
        .download-btn {
            display: inline-block;
            min-width: 240px;
            border-radius: 4px;
            background-color: #098de6;
            box-shadow: 0 4px 6px rgba(50,50,93,.11), 0 1px 3px rgba(0,0,0,.08);
            color: #fff;
            font-size: 18px;
            font-weight: 400;
            text-decoration: none;
            transition: all .15s ease;
            padding: 10px 40px;
            text-align: center;
        }
        #main{
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        #main > div{
            text-align: center;
            width: 100%;
        }
        #main > div:first-child{
            margin-top: 100px;
        }
        #main > div:last-child{
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div id="main">
    @if(is_weixin())
    <div>
        <p>1、点击右上角...按钮</p>
        <p>2、选择在浏览器在打开即可下载安装艺行派</p>
    </div>
    @endif
    <div>
        <p>
            <img class="download-item-icon" src="/img/wallet/i-android.svg">
        </p>
        <p>
            <a class="download-btn" href="/app-release.apk" data-source="android-international">安卓下载</a>
        </p>
    </div>
    <div>
        <p>
            <img class="download-item-icon" src="/img/wallet/i-ios.svg">
        </p>
        <p>
            <a class="download-btn" href="#" data-source="android-international">Coming soon</a>
        </p>
    </div>
</div>
</body>
</html>
