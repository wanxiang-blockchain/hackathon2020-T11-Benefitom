<?php

namespace App\Model;

class Module
{
    var $menus =[
            1 => [
                "name" => "控制面板",
                "url"  => "/admin/hello?nav=1",
                "icon" => "fa fa-home",
                'class'=>'',
                "subMenu" => [
                ]
            ],
            2 => [
                "name" => "项目管理",
                "url"  => "/admin/project",
                "icon" => "fa fa-archive",
                'class'=>'',
                "subMenu" => [
                    1 => [                       
                        "name" => "项目列表",
                        "url"  => "/admin/project?nav=2|1",
                        "icon" => "fa fa-bars",
                        'class'=>'',
                    ],
                    2 => [                       
                        "name" => "添加项目",
                        "url"  => "/admin/project/create?nav=2|2",
                        "icon" => "fa fa-plus-square-o",
                        'class'=>'',
                    ],
                    3 => [
                        "name" => "订单列表",
                        "url"  => "/admin/projectOrder?nav=2|3",
                        "icon" => "fa fa-bars",
                        'class'=>'',
                    ]

                ]
            ],
            3 => [
                "name" => "交易管理",
                "url"  => "/admin/transaction",
                "icon" => "fa fa-money",
                'class'=>'',
                "subMenu" => [
                    1 => [
                        "name" => "委托记录",
                        "url"  => "/admin/trade/?nav=3|1",
                        "icon" => "fa fa-users",
                        'class'=>'',
                    ],
                    2 => [
                        "name" => "成交记录",
                        "url"  => "/admin/trade/tradeLog?nav=3|2",
                        "icon" => "fa fa-bars",
                        'class'=>'',
                    ],
                    3 => [
                        "name" => "交易设置",
                        "url"  => "/admin/trade/set?nav=3|3",
                        "icon" => "fa fa-bars",
                        'class'=>'',
                    ],
                    4 => [
	                    "name" => "资产列表",
	                    "url"  => "/admin/assets?nav=3|4",
	                    "icon" => "fa fa-bars",
	                    'class'=>'',
                    ]
                ]
            ],
            4 => [
                "name" => "财务管理",
                "url"  => "/admin/finance",
                "icon" => "fa fa-cny",
                'class'=>'',
                "subMenu" => [
                     1 => [
                        "name" => "提现审核",
                        "url"  => "/admin/finance/withdraw/?nav=4|1",
                        "icon" => "fa fa-exchange",
                        'class'=>'',
                    ],
                    2 => [
                        "name" => "财务统计",
                        "url"  => "/admin/finance/finance_sum/?nav=4|2",
                        "icon" => "fa fa-ticket",
                        'class'=>'',
                    ],
                    3 => [
                        "name" => "财务日志",
                        "url"  => "/admin/finance/log/?nav=4|3",
                        "icon" => "fa fa-list-alt",
                        'class'=>'',
                    ],
                    4 => [
                        "name" => "充值管理",
                        "url"  => "/admin/finance/recharge/?nav=4|4",
                        "icon" => "fa fa-ticket",
                        'class'=>'',
                    ],
	                8 => [
                        "name" => "支付宝充值记录",
                        "url"  => "/admin/finance/alilog/?nav=4|8",
                        "icon" => "fa fa-ticket",
                        'class'=>'',
                    ],
                    5 => [
                        "name" => "充值手续费",
                        "url"  => "/admin/finance/fee/?nav=4|5",
                        "icon" => "fa fa-ticket",
                        'class'=>'',
                    ],
                    6 => [
                        "name" => "管理员充值审核",
                        "url"  => "/admin/finance/audit_list/?nav=4|6",
                        "icon" => "fa fa-ticket",
                        'class'=>'',
                    ],
	                7 => [
	                	'name' => '代理管理',
		                'url'  => '/admin/agent?nav=4|7',
		                'icon' => 'fa fa-ticket',
		                'class' => ''
	                ],
	                8 => [
	                	'name' => '提货管理',
		                'url' => '/admin/delivery?nav=4|8',
		                'icon' => 'fa fa-exchange',
		                'class' => '',
	                ],
	                9 => [
	                	'name' => '管理员提现',
		                'url' => '/admin/withdrawAudit?nav=4|5',
		                'icon' => 'fa fa-ticket',
		                'class' => '',
	                ]
                ]
            ],
        5 => [
            "name" => "文章管理",
            "url"  => "/admin/article",
            "icon" => "fa fa-list-alt",
            'class'=>'',
            "subMenu" => [
                1 => [
                    "name" => "分类管理",
                    "url"  => "/admin/category/?nav=5|1",
                    "icon" => "fa fa-book",
                    'class'=>'',
                ],
            ]
        ],
        6 => [
            "name" => "会员管理",
            "url"  => "/admin/manage",
            "icon" => "fa fa-user",
            'class'=>'',
            "subMenu" => [
                1 => [
                    "name" => "会员管理",
                    "url"  => "/admin/member/?nav=6|1",
                    "icon" => "fa fa-user",
                    'class'=>'',
                ],
                2 => [
                    "name" => "实名审核",
                    "url"  => "/admin/profiles/?nav=6|2",
                    "icon" => "fa fa-user",
                    'class'=>'',
                ],

            ]
        ],
        8 => [
            "name" => "系统设置",
            "url"  => "/admin/system",
            "icon" => "fa fa-wrench",
            'class'=>'',
            "subMenu" => [
                /* 1 => [
                    "name" => "系统设置",
                    "url"  => "/admin/system/?nav=8|1",
                    "icon" => "fa fa-wrench",
                    'class'=>'',
                ],*/
                2 => [
                    "name" => "角色管理",
                    "url"  => "/admin/manage/user/?nav=8|2",
                    "icon" => "fa fa-user",
                    'class'=>'',
                ],
                3 => [
                    "name" => "幻灯片设置",
                    "url"  => "/admin/slide/?nav=8|3",
                    "icon" => "fa fa-picture-o",
                    'class'=>'',
                ],
                4 => [
                    "name" => "合作伙伴",
                    "url"  => "/admin/link/?nav=8|4",
                    "icon" => "fa fa-link",
                    'class'=>'',
                ]
            ],
        ],
        9 => [
            'name' => '艺百融',
            'url' => '/admin/rong',
            'icon' => 'fa fa-shopping-cart',
            'class' => '',
            'subMenu' => [
	            1 => [
		            'name' => '产品列表',
		            'url' => '/admin/rong?nav=9|1',
		            'icon' => 'fa fa-globe',
		            'class' => '',
	            ],
	            2 => [
		            'name' => '销售列表',
		            'url' => '/admin/rong/userProduct?nav=9|2',
		            'icon' => 'fa fa-list-alt',
		            'class' => ''
	            ],
	            3 => [
		            'name' => '审核列表',
		            'url' => '/admin/rong/endList?nav=9|3',
		            'icon' => 'fa fa-list-alt',
		            'class' => ''
	            ],
            ]
        ],
	    10 => [
	    	'name' => '艺奖堂',
		    'url' => '/admin/tender',
		    'icon' => 'fa fa-shopping-cart',
		    'class' => '',
		    'subMenu' => [
		    	1 => [
		    		'name' => '拍品管理',
				    'url' => '/admin/tender?nav=10|1',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    2 => [
				    'name' => '提现审核',
				    'url' => '/admin/tender/withdraw?nav=10|2',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    3 => [
				    'name' => '小红花流水',
				    'url' => '/admin/tender/flow?nav=10|3',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    4 => [
			    	'name' => '添加拍品',
				    'url' => '/admin/tender/create?nav=10|4',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    5 => [
				    'name' => '获奖列表',
				    'url' => '/admin/tender/winners?nav=10|5',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    6 => [
			    	'name' => '分配小红花',
				    'url' => '/admin/tender/charge?nav=10|6',
				    'icon' => 'fa fa-bars',
				    'class' => ''
			    ],
			    7 => [
				    'name' => '保证金管理',
				    'url' => '/admin/tender/margin?nav=10|7',
				    'icon' => 'fa fa-bars',
				    'class' => ''
			    ],
			    8 => [
				    'name' => '用户反馈',
				    'url' => '/admin/tender/feedback?nav=10|8',
				    'icon' => 'fa fa-bars',
				    'class' => ''
			    ],
			    9 => [
			    	'name' => '课程管理',
				    'url' => '/admin/tender/courses?nav=10|9',
				    'icon' => 'fa fa-bars',
				    'class' => ''
			    ]
		    ]
	    ],
	    11 => [
		    'name' => 'artbc',
		    'url' => '/admin/artbc/logs',
		    'icon' => 'fa fa-shopping-cart',
		    'class' => '',
		    'subMenu' => [
			    1 => [
				    'name' => 'artbc流水',
				    'url' => '/admin/artbc/logs?nav=11|1',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    2 => [
				    'name' => '提取管理',
				    'url' => '/admin/artbc/ti?nav=11|2',
				    'icon' => 'fa fa-bars',
				    'class' => '',
			    ],
			    3 => [
			    	'name' => '锁仓管理',
				    'url' => '/admin/artbc/unlocks?nav=11|3',
				    'icon' => 'fa fa-bars',
				    'class' => ''
			    ]
			]
	    ],
        12 => [
            'name' => '版通',
            'url' => '/admin/btscore/logs',
            'icon' => 'fa fa-shopping-cart',
            'class' => '',
            'subMenu' => [
                1 => [
                    'name' => '流水',
                    'url' => '/admin/btscore/logs?nav=12|1',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                2 => [
                    'name' => '锁仓管理',
                    'url' => '/admin/btscore/unlock/logs?nav=12|2',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                3 => [
                    'name' => '配置',
                    'url' => '/admin/btconfig/edit?nav=12|3',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                4 => [
                    'name' => '团队人统计',
                    'url' => '/admin/btscore/sum?nav=12|4',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ]
            ]
        ],
        13 => [
            'name' => '兑换中心',
            'url' => '/admin/btshop/products',
            'icon' => 'fa fa-shopping-cart',
            'class' => '',
            'subMenu' => [
                1 => [
                    'name' => '商品列表',
                    'url' => '/admin/btshop/products?nav=13|1',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                2 => [
                    'name' => '增加商品',
                    'url' => '/admin/btshop/product/create?nav=13|2',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                3 => [
                    'name' => '提货列表',
                    'url' => '/admin/btshop/delivery?nav=13|3',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],

            ]
        ],
        14 => [
            'name' => '区块链',
            'url' => '/admin/block/recharges',
            'icon' => 'fa fa-shopping-cart',
            'class' => '',
            'subMenu' => [
                1 => [
                    'name' => '充值列表',
                    'url' => '/admin/block/recharges?nav=14|1',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                2 => [
                    'name' => '流水',
                    'url' => '/admin/block/asset/logs?nav=14|2',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                3 => [
                    'name' => '提取列表',
                    'url' => '/admin/block/tiqu?nav=14|3',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                4 => [
                    'name' => '售出列表',
                    'url' => '/admin/block/sale?nav=14|4',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                5 => [
                    'name' => '提币列表',
                    'url' => '/admin/block/tibis?nav=14|5',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ]
            ]
        ],
        15 => [
            'name' => '艺行派-现金账户',
            'url' => '/admin/block/recharges',
            'icon' => 'fa fa-shopping-cart',
            'class' => '',
            'subMenu' => [
                1 => [
                    'name' => '支付宝提现列表',
                    'url' => '/admin/alipay/draws?nav=15|1',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ],
                2 => [
                    'name' => '银行卡提现列表',
                    'url' => '/admin/bankcard/draws?nav=15|2',
                    'icon' => 'fa fa-bars',
                    'class' => ''
                ]
            ]
        ]

    ];


    var $maps = [
        1   => [1=>[1],2 => [1,2,3],3=>[1,2,3, 4], 4 => [1, 2, 3, 4, 8], 5=>[1,2,3,4],
                6=>[1, 2],8=>[1,2,3,4], 9 => [1, 2], 10 => [1, 2, 3, 6, 7, 8, 9],
	            11 => [1, 2, 3], 12 => [1, 2, 3, 4], 13 => [1,3],
                14 => [1, 2, 3, 4, 5], 15 => [1, 2]
            ],
        2   => [1=>[1], 2=>[3], 3=>[1,2],6=>[1],8=>[3,4],  9=>[1] ],
        3   => [1=>[1], 2 => [1,2,3],3=>[1,2, 4],4 => [1,2,3,4,5,6,7, 8, 9], 6 =>[1], 9=>[1, 2, 3], 10 => [1, 2, 3, 6, 7],
	            11 => [1, 2]
	        ],
        4   => [1=>[1], 2 => [1,2,3],3=>[1,2], 4 => [1,2,3,4,5,6,7, 8], 6 =>[1], 9=>[1, 2]],
        -1  => [1=>[1]],
        // 客服
	    5 => [6 => [2], 13 => [3], 14=>[2, 3], 15=>[2]],
	    6 => [11 => [1]],
        7 => [12 => [1, 2, 3, 4], 13 => [1, 2, 3, 4], 14 => [1, 2, 3, 4, 5], 15=> [1, 2], 6 => [2]],
        8 => [12 => [4]],
    ];


	public function getMenus($role_type)
	{
		$nav = session()->get("nav");
		$navs = explode("|", $nav);
		$_map = $this->maps[$role_type];
		$ret = [];
		foreach ($_map as $k => $v) {
			$ret[$k] = $this->menus[$k];
			if ($k == $navs[0]) {
				$ret[$k]["class"] = "active";
			}
			$_subMenu = $ret[$k]["subMenu"];
			$ret[$k]["subMenu"] = [];
			foreach ($v as $_k) {
				if (isset($_subMenu[$_k])) {
					$ret[$k]["subMenu"][$_k] = $_subMenu[$_k];
					if (isset($navs[1]) && $_k == $navs[1]) {
						$ret[$k]["subMenu"][$_k]["class"] = "active";
					}
				}
			}
		}

		return $ret;
	}

    public function getCrumbs()
    {
        $nav = session()->get("nav");
        $navs = explode("|", $nav);
        $menus = $this->menus;
        if(count($navs) == 1) {
            return [$menus[$navs[0]]];
        }elseif(count($navs) == 2) {
            return [$menus[$navs[0]], $menus[$navs[0]]['subMenu'][$navs[1]]];
        } else {
            return [];
        }
    }

    public function getCrumbsView()
    {
        $crumbs = $this->getCrumbs();
        $crumbs_html = '';
        if(count($crumbs) == 1) {
            $crumbs_html .= '<li class="active">'.$crumbs[0]['name'].'</li>';
        } elseif(count($crumbs) ==  2)
        {
            $crumbs_html .= '<li>'.$crumbs[0]['name'].'</li>';
            $crumbs_html .= '<li class="active">'.$crumbs[1]['name'].'</li>';
        }
        return $crumbs_html;
    }
}
