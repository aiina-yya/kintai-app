<header class="header">
    <div class="header__logo">
        <a href="/"><img src="{{ asset('/images/COACHTECHヘッダーロゴ (2).png') }}" alt="ロゴ"></a>
    </div>

    @if(true)
    <nav class="header__nav">
        <ul>
            @if(Auth::check())
            <li>
                <a href="#">勤怠一覧</a>
            </li>
            <li>
                <a href="#">スタッフ一覧</a>
            </li>
            <li>
                <a href="#">申請一覧</a>
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
</header>