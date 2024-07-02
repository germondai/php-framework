@component('layouts.default')
<main class="w-full h-screen grid place-items-center">
    <div class="flex flex-col items-center gap-4">
        <b>{{$error}}</b>
        <h1>Page Not Found!</h1>
        <br/>
        <a @link('')>Home</a>
    </div>
</main>
@endcomponent