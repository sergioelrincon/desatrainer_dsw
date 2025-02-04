<!-- Header con información del escenario -->
<div class="card mb-4">
    <!-- Cabecera de la tarjeta -->
    <div class="card-header bg-gray text-white">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Título del escenario -->
            <h3 class="mb-0 card-title">
                <i class="fas fa-play-circle"></i> 
                {{ $scenario->title }} <!-- Muestra el título del escenario -->
            </h3>

            <!-- Herramientas de la tarjeta -->
            <div class="card-tools d-flex align-items-center gap-2">
                <!-- Botón para volver a la lista de escenarios -->
                <a href="{{ route('scenarios.play-list', ['category_id' => $scenario->category_id]) }}">
                    <x-button><i class="fas fa-arrow-left"></i> Volver</x-button>
                </a>
                <!-- Botón para minimizar/colapsar la tarjeta -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus text-white"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Cuerpo de la tarjeta -->
    <div class="card-body">
        <div class="row">
            <!-- Columna de información del escenario -->
            <div class="col-md-6">
                <p><strong>Categoría:</strong> {{ $scenario->category->name }}</p> <!-- Muestra el nombre de la categoría -->
                <p><strong>DESA Trainer:</strong> {{ $scenario->desaTrainer->name }}</p> <!-- Muestra el nombre del entrenador -->
            </div>
            <div class="col-md-6">
                <p><strong>Total instrucciones:</strong> {{ $scenario->instructions->count() }}</p> <!-- Muestra el número de instrucciones -->
                <p>{!! $scenario->description !!}</p> <!-- Muestra la descripción del escenario -->
            </div>
        </div>
    </div>
</div>
