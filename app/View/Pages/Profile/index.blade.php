@component('layouts.default')
<main class="w-full h-screen grid place-items-center">
    <div class="flex flex-col items-center gap-8">
        <h1>Profile page of {{ $user }}</h1>
        <a @link('')>Main page</a>
    </div>
</main>
@endcomponent