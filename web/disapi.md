## 分销系统外调接口设计 

    test host: https://trade.yigongpan.com/disapi
    prod host: https://shop.yigongpan.com/disapi
    method: post 
    sign: sha1(keysort(params) + appkey + timestamp) 按 key 正序。比如 ['nationcode' => '86', 'phone' => '110', 'id' => 1]; 排序拼起来就是 11108
    appkey: test(Llasd1jksafasdl) prod(Pdlak282200J323)
	
#### 拉取用户信息 
------
### /members

param|type|is must|note
:----: | :----: | :----: | :----:
lastId | int | 1 | last id of last page, default 0

    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
            "harMore": 1, // 1 有下一页 0无
            "list": [
                {
                    phone: "13800138000",
                    id: 100,  // 
                    pid: 20  // 上级id
                }
            ]
        },
        "userMsg":"成功"
    }

	
#### 拉取单个用户信息
------
### /member

param|type|is must|note
:----: | :----: | :----: | :----:
id | int | 1 |  

    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
            phone: "13800138000",
            id: 100,  // 
            pid: 20  // 上级id
        },
        "userMsg":"成功"
    }

#### 版通价格
------
### /arttbc/price

param|type|is must|note
:----: | :----: | :----: | :----:

    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
            "price": 4.34
        },
        "userMsg":"成功"
    }


#### 身份验证
------
### /member/verify

param|type|is must|note
:----: | :----: | :----: | :----:
ticket | string | 1  | 用户从艺行派进入h5种的身份令牌，有效时间两小时，分销系统可缓存两小时

    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
             phone: "13800138000",
             id: 100,  // 
             pid: 20  // 上级id
        },
        "userMsg":"成功"
    }
    
#### 积分提取
------
### /score/draw

param|type|is must|note
:----: | :----: | :----: | :----:
ticket | string | 1  | 用户从艺行派进入h5种的身份令牌，有效时间两小时，分销系统可缓存两小时
amount | float | 1  | 提取数量

    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
            
        },
        "userMsg":"成功"
    }

#### 推送注册用户
------
### /member/reg

param|type|is must|note
:----: | :----: | :----: | :----:
phone | string  | 1 | 手机号
id | int  | 1 | id
pid  | int  | 1 | parent id
    
    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
        },
        "userMsg":"成功"
    }

#### 推送锁仓积分数量
------
### /member/score

param|type|is must|note
:----: | :----: | :----: | :----:
id | int  | 1 | id
score  | float  | 1 | 用户购买积分数量
    
    {
        "code":200,  // 成功为 200，失败为 其他
        "data":{
        },
        "userMsg":"成功"
    }


