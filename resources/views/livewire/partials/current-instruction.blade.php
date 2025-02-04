<div class="card">
    <div class="card-header bg-gray text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Instrucción Actual</h5> <!-- Título de la sección -->
            <div class="card-tools">
                <!-- Botón para colapsar la tarjeta -->
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus text-white"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="current-instruction">
            <!-- Verificamos si existe una instrucción actual -->
            @isset($currentInstructionId)
                <div class="instruction-title mb-3">
                    <h4 class="mb-0">{{ $scenario->instructions->find($currentInstructionId)->title }}</h4> <!-- Título de la instrucción actual -->
                    
                    @if($audioPlaying)
                        <!-- Indicador de audio si está sonando -->
                        <div class="audio-indicator mt-2">
                            <i class="fas fa-volume-up"></i>
                            <div class="audio-wave">
                                <span></span><span></span><span></span> <!-- Indicador visual de audio -->
                            </div>
                        </div>
                    @endif
                </div>

                <div class="instruction-content">
                    {!! $scenario->instructions->find($currentInstructionId)->content !!} <!-- Muestra el contenido de la instrucción actual -->
                </div>
            @endisset
        </div>
    </div>
</div>