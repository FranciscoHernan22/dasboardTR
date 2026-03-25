<div class="w-full flex justify-end  ">


<div x-data="{ open: false }" class="relative inline-block">

    <!-- BOTÓN PERFIL -->
    <button
        @click="open = !open"
        class="bg-white shadow rounded-full p-2 z-50"
    >
        <img
            class="w-10 h-10 rounded-full"
            src="https://ui-avatars.com/api/?name=Francisco+Hernan"
        >
    </button>

    <!-- PANEL DEBAJO DEL BOTÓN -->
    <aside
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="absolute right-0 top-full mt-0.5 w-72 bg-white shadow-lg rounded-lg z-40"
    >
        <div class="p-4 border-b">
            <p class="font-semibold">Francisco Hernan</p>
            <p class="text-sm text-gray-500">Entrenador</p>
        </div>

        <div class="p-4 space-y-2">
            <button class="block w-full text-left hover:bg-gray-100 rounded p-2">
                Mi perfil
            </button>

            <form method="POST" action="{{ route('entrenador.logout') }}">
                @csrf
                <button class="text-red-600 w-full text-left hover:bg-red-50 rounded p-2">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

</div>
</div>