<header class="header">
    <div class="header__logo">
        <a href=""><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
    </div>
    <nav class="header__nav">
        @if(Auth::check())
            <ul>
                @if(Auth::user()->role === 'admin')
                <div class="header__link--inner">
                    <li><a class="header__link" href="/admin/attendance/list">勤怠一覧</a></li>
                    <li><a class="header__link" href="/admin/staff/list">スタッフ一覧</a></li>
                    <li><a class="header__link" href="/stamp_correction_request/list">申請一覧</a></li>
                    <li>
                        <form action="/logout" method="post">
                        @csrf
                        <button class="logout header__link">ログアウト</button>
                        </form>
                    </li>
                </div>
                @else
                    @if(Request::is(''))
                    <div class="header__link--inner">
                        <li><a class="header__link" href="/attendance/list">今日の出勤一覧</a></li>
                        <li><a class="header__link" href="/stamp_correction_request/list">申請一覧</a></li>
                        <li>
                            <form action="/logout" method="post">
                                @csrf
                                <button class="logout header__link">ログアウト</button>
                            </form>
                        </li>
                    </div>
                    @else 
                    <div class="header__link--inner">
                        <li><a class="header__link" href="/attendance">勤怠</a></li>
                        <li><a class="header__link" href="/attendance/list">勤怠一覧</a></li>
                        <li><a class="header__link" href="/stamp_correction_request/list">申請</a></li>
                        <li>
                            <form action="/logout" method="post">
                                @csrf
                                <button class="logout header__link">ログアウト</button>
                            </form>
                        </li>
                    </div>
                    @endif
                @endif
            </ul>
        @endif 
    </nav>
</header>