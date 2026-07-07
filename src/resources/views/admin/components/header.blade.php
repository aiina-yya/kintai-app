<header class="header">
    <div class="header__inner">
        <div class="header__logo">
            <a href="/"><img src="{{ asset('storage/images/COACHTECHヘッダーロゴ (2).png') }}" alt="ロゴ"></a>
        </div>

        @if(true)
        <nav class="header__nav">
            <ul>
            @if(Auth::check())
                <li>
                    <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                </li>
                <li>
                    <a href="{{ route('admin.staff') }}">スタッフ一覧</a>
                </li>
                <li>
                    <a href="{{ route('admin.attendance">申請一覧</a>
                </li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="post">
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