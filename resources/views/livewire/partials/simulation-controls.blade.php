<!-- Mostrar errores -->
@if($errorState)
    <div class="alert alert-danger">
        {{ $errorState }} <!-- Muestra el mensaje de error si existe -->
    </div>
@endif

<!-- Comprobamos si el escenario tiene transiciones configuradas -->
@if($this->hasTransitions())
    <div class="card mb-3">
        <div class="card-body">
            <!-- Botón de iniciar o detener simulación -->
            @if(!$isPlaying)
                <button wire:click="startSimulation" class="btn btn-success btn-md mb-3 w-100">
                    <i class="fas fa-play"></i> Iniciar Simulación
                </button>
            @else
                <button wire:click="stopSimulation" class="btn btn-danger btn-md mb-3 w-100">
                    <i class="fas fa-stop"></i> Detener Simulación
                </button>
            @endif
        </div>
    </div>
@else
    <!-- Mensaje si no hay transiciones configuradas -->
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        Este escenario no tiene transiciones configuradas. Por favor, configure las transiciones antes de iniciar la simulación.
    </div>
@endif
