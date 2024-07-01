<main class="w-full h-screen grid place-items-center">
    <div class="flex flex-col items-center gap-8">
        <h1>{{ $greeting }}</h1>
    
        @component('components.example')
            Component slot passed from <b>index</b>!
        @endcomponent
    </div>    
</main>
