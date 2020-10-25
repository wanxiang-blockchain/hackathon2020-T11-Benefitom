<!-- 暗标 -->
<a href="/tender/detail/{{$todo->id}}" class="dark_mark">
    <div class="left">
        <img src="{{asset('/storage/' . $todo->banner)}}" alt="">
    </div>
    <div class="right">
        <p class="prd_name">{{$todo->name}}</p>
        <p class='introducetext'>
            {{$todo->abstract}}
        </p>
        <div class="countdown">
            <input type="hidden" class="show_start" value="{{strtotime($todo->guess_start)}}" />
            <span class="f todo-hour"><b>21</b></span>
            <span>时</span><span class="f todo-min"><b>41</b></span>
            <span>分</span><span class="f todo-sec color"><b>8</b></span>
            <span>秒</span>
        </div>
    </div>
    <!-- 暗标图标 -->
    <span class="dmlogo">暗标</span>
    <!-- 箭头 -->
    <img class="arrow" src="/tender/images/arrow.png" alt="">
</a>