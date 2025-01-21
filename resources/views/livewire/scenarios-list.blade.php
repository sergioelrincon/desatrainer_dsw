<div>
    <!-- Filtros -->
    @if($categoryName == 'DesaTrainers')
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid md:grid-cols-3 gap-4">
            <!-- Buscador -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                </span>
                <x-input type="text" 
                wire:model.live="search"
                class="pl-10 w-full"
                placeholder="Buscar escenarios..." />
            </div>
            
            <!-- Filtro de DesaTrainers -->
            <div>
                <select wire:model.live="desaTrainerId"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Todos los DESA</option>
                @foreach($desaTrainers as $desa)
                <option value="{{ $desa->id }}">{{ $desa->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Botón limpiar -->
        @if($search || $desaTrainerId)
        <div class="flex items-center">
            <button wire:click="resetFilters"
            class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300">
            <i class="fas fa-times mr-2"></i> Limpiar filtros
        </button>
    </div>
    @endif
</div>
</div>
@endif

<!-- Lista de Escenarios -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($scenarios as $scenario)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden pb-4">
        @if($scenario->desaTrainer)
        <div class="relative">
            <div class="absolute top-0 right-0 px-3 py-1 bg-green-500 text-white text-sm font-semibold rounded-bl-lg">
                DESA
            </div>
        </div>
        @endif
        
        <div class="p-6 pb-4">
            <h3 class="text-lg font-semibold mb-2">{{ $scenario->title }}</h3>
            {{-- <p class="text-gray-600 mb-4">{{ Str::limit($scenario->description, 100) }}</p> --}}
            <div class="space-y-2 text-sm text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-layer-group mr-2"></i> 
                    <span>Categoría: {{ $scenario->category->name }}</span>
                </div>
                
                <div class="flex items-center">
                    <i class="fas fa-list mr-2"></i> 
                    <span>{{ $scenario->instructions->count() }} instrucciones</span>
                </div>
                
                @if($scenario->desaTrainer)
                <div class="flex items-center">
                    <i class="fas fa-heartbeat mr-2"></i> 
                    <span>DESA: {{ $scenario->desaTrainer->name }}</span>
                    @if($scenario->desaTrainer->image)
                    <img src="{{ asset('storage/' . $scenario->desaTrainer->image) }}" 
                    alt="{{ $scenario->desaTrainer->name }}" 
                    class="ml-2 h-8 w-8">
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <div class="px-6 pb-6">
            <x-button class="w-full justify-center bg-green-500 hover:bg-green-600" 
            onclick="window.location='{{ route('scenarios.play', $scenario) }}'">
            <i class="fas fa-play mr-2"></i> Iniciar Escenario
        </x-button>
    </div>
</div>
@empty
<div class="col-span-full">
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-400 mr-3"></i>
            <p class="text-blue-700">No hay escenarios disponibles con los filtros seleccionados.</p>
        </div>
    </div>
</div>
@endforelse
</div>

<!-- Paginación -->
<div class="mt-6">
    {{ $scenarios->links() }}
</div>
</div>