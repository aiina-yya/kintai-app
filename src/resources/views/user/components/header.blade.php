<header class="header">
    <div class="header__inner">
        <div class="header__logo">
            <a href="/"><img src="{{ asset('storage/images/COACHTECHヘッダーロゴ (2).png') }}" alt="ロゴ"></a>
        </div>

        @if(true)
        <nav class="header__nav">
            <ul>
            @if(Auth::check() && Auth::user()->hasVerifiedEmail())
                <li>
                    <a href="{{ route('attendance') }}">勤怠</a>
                </li>
                <li>
                    <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                </li>
                <li>
                    <a href="{{ route('correction.list') }}">申請</a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="post">
                    @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
                @endif
            </ul>
        </nav>
        @endif
    </div>
</header>