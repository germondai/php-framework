<main class="w-full h-screen grid place-items-center">
    <h1><?= $greeting ?></h1>

    @if (!empty($greeting))
        {{$greeting}}
    @endif
</main>
