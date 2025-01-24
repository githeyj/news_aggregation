<header class="px-4 py-2 bg-black/50 text-zinc-100 w-full shadow-lg border-b border-zinc-400">
    <div class="container flex justify-between h-16 mx-auto">
        <a href="{{ route('home') }}" aria-label="Back to homepage" class="flex items-center p-2 border-b-2 border-transparent hover:border-zinc-400 hover:text-white transition rounded-sm">
            <h1>{{ config('app.name') }}</h1>
        </a>

        <ul class="items-stretch hidden space-x-3 lg:flex">
            <li class="flex">
                <a href="{{ route('home') }}" class="flex items-center px-6 -mb-1 border-b-2 border-transparent hover:border-zinc-400 hover:text-white transition rounded-sm">
                    Home
                </a>
            </li>
        </ul>
        <div class="items-center flex-shrink-0 hidden lg:flex">
            @auth
                <a href="{{ route('dashboard') }}">
                    <button class="self-center px-8 py-3 rounded">Dashboard</button>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="self-center px-8 py-3 font-semibold rounded bg-blue-600 text-white">
                        Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}">
                    <button class="self-center px-8 py-3 border-b-2 border-transparent hover:border-zinc-400 hover:text-white transition rounded-sm">Sign in</button>
                </a>
            @endauth
        </div>

        <button class="p-4 lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-6 h-6 text-coolGray-800">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>


    </div>
</header>
