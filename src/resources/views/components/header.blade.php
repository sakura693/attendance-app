<header class="header">
    <div class="header__logo">
        <a href=""><img src="{{ asset('img/logo.png') }}" alt="ロゴ"></a>
    </div>
    <nav class="header__nav">
        <!--ログインしてる場合-->
        @if(Auth::check())
            <ul>
                <!--管理者ログインの場合-->
                @if(Auth::user()->role === 'admin')
                <li><a href="/admin/attendance/list">勤怠一覧</a></li>
                <li><a href="/admin/staff/list">スタッフ一覧</a></li>
                <li><a href="/stamp_correction_request/list">申請一覧</a></li>
                <li>
                    <form action="/logout" method="post">
                        @csrf
                        <button class="logout">ログアウト</button>
                    </form>
                </li>
                <!--一般ユーザーでログインの場合-->
                @else
                    @if(Request::is(''))<!--勤怠登録画面-勤怠後の場合-->
                        <li><a href="">今日の出勤一覧</a></li>
                        <li><a href="">申請一覧</a></li>
                        <li>
                            <form action="/logout" method="post">
                                @csrf
                                <button class="logout">ログアウト</button>
                            </form>
                        </li>
                    @else <!--その他の場合-->
                        <li><a href="">勤怠</a></li>
                        <li><a href="">勤怠一覧</a></li>
                        <li><a href="">申請</a></li>
                        <li>
                            <form action="/logout" method="post">
                                @csrf
                                <button class="logout">ログアウト</button>
                            </form>
                        </li>
                    @endif
                @endif
            </ul>
        @endif <!--未ログインの場合は何も表示されない-->
    </nav>

</header>