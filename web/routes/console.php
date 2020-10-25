<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

use App\Service\MemberService;
Artisan::command('t-member', function (MemberService $service) {
    $service->deleteForce(1);
    $service->create([
        "name"     => "zhangye",
        "nickname" => "zhangye",
        "phone"    => "13426197806",
        'code'     => "220381xxxx1224yyyy",
        "password" => "123456",
    ]);
})->describe('Display an inspiring quote');


use App\Model\Category;
use App\Service\SubscriptionService;
Artisan::command('sub', function (MemberService $service, SubscriptionService $subscriptionService) {
    $member = $service->create([
        "name"     => "zhangye",
        "nickname" => "zhangye",
        "phone"    => "13426197806",
        'code'     => "220381xxxx1224yyyy",
        "password" => "123456",
    ]);
    $project_id = 1;
    $member_id  = $member->id;
    $quantity = 100;
    $price = 1.2;
    $order = $subscriptionService->makeOrder($project_id, $member_id, $quantity, $price);
    $subscriptionService->payOrder($order->order_id);

})->describe('测试');


