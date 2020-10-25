<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>绍德艺品易货中心</title>
    <meta name='viewport', content='width=device-width, initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no' />
    <meta http-equiv='X-UA-Compatible', content='ie=edge' />
    <link rel="shortcut icon" href="{{'/front/favicon.ico?v=1'}}">
    <style>
        html, body{
            margin: 0;
            padding: 0;
        }
        #tang-nav-ul {
            width: 100%;
            height: auto;
            display: flex;
            justify-content: space-around;
            padding: 0;
        }
        #tang-nav-ul > li{
            list-style: none;
            float: left;
            max-width: 20%;
        }
        #tang-nav-ul > li img{
            width: 100%;
            height: auto;
        }
        header{
            width: 100%;
            text-align: center;
        }
        header > img:first-child{
            width: auto;
        }
        header > img:last-child{
            width: 100%;
            height: auto;
        }
        header > div
        {
            width:220%;
            height:auto;
            position:relative;
            animation:mymove 30s infinite;
            -webkit-animation:mymove 30s infinite; /*Safari and Chrome*/
            animation-timing-function:linear;
            -webkit-animation-timing-function:linear; /* Safari 和 Chrome */
        }

        @keyframes mymove
        {
            from {left:100%;}
            to {left:-220%;}
        }

        @-webkit-keyframes mymove /*Safari and Chrome*/
        {
            from {left:100%;}
            to {left:-220%;}
        }
        #footer {
            padding: 15px 0px 13px;
            background: #a91414;
            border-top: 4px solid #faf9f7;
            color: #fff;
            margin: 0 auto;
            max-width: 100%;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 12px;
        }
    </style>
</head>
<body>
<header>
    <img src="/front/image/logo.png">
    <div>
    </div>
    <img src="/img/front/home-001.jpg">
</header>
<nav>
    <ul id="tang-nav-ul">
        <li>
            <a href="/subscription"><img src="/img/front/yipintang-1.png" alt="" width="197" height="270" /></a>  
        </li>
    </ul>
    <div style="text-align: center;">
        <a href="/member"><img style="magin:0 auto; max-width: 40%; height: auto;" src="/img/front/center.png" alt=""/></a>
    </div>
</nav>
<footer id="footer">
    Copyright 2020 益通云 | All Rights Reserved
</footer>
<script async src="/js/google-analysis.js"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-46679934-7');
</script>
</body>
</html>
