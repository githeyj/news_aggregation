<x-app-layout>
    <div class="relative isolate px-6 pt-14 lg:px-8 min-h-screen">
        <!-- Background image -->
        <div class="absolute inset-0 -z-10">
            <img src="/img/welcome-bg.jpg" alt="Background" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gray-900/60"></div>
        </div>

        <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                    The News Aggregating Platform
                </h1>
                <p class="mt-6 text-xl leading-8 text-gray-200">
                    Start your journey today with our innovative solutions.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="{{ route('login') }}">
                        <x-button>
                            Get Started
                        </x-button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
