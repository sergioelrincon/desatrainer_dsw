<div>
    <x-adminlte-card title="Flujo de Transiciones" theme="olive" icon=""
    header-class="text-uppercase rounded-bottom border-info" collapsible removable>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-info"></i> NOTAS:</h5>
        <ul class="mb-0">
            <li>Aquí se indica el flujo de las transiciones</li>
            <li>Es necesario indicar la transición de inicio</li>
            <li><b>Tiempo:</b> Si la instrucción de origen es un audio, indicamos la duración del mismo, si es de tiempo de espera, la duración de la espera</li>
            <li><b>Elección de usuario:</b> Indicamos el botón que debe seleccionar el usuario</li>
            <li><b>Bucle:</b> La instrucción de origen debe ser el bucle y la de destino la instrucción desde donde se tiene que reproducir, indicamos el nº de veces que se va a ejecutar</li>
        </ul>
    </div>
    
    <div class="mb-3">
        <button wire:click="createTransition" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Transición
        </button>
    </div>
    
    <div class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Buscar por título de instrucción..." 
            wire:model.live="search">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Paso</th>
                    <th>Desde</th>
                    <th>Condición</th>
                    <th>Configuración</th>
                    <th>Siguiente Paso</th>
                    <th width="10%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                $stepCount = 0;
                @endphp
                @forelse($transitions as $transition)
                <tr class="{{ !$transition->is_in_flow ? 'opacity-50 bg-gray-100' : '' }}">
                    <td class="text-center">
                        @if($transition->is_in_flow)
                        @php $stepCount++ @endphp
                        <span class="badge bg-olive">{{ $stepCount }}</span>
                        @else
                        <span class="badge bg-secondary">-</span>
                        @endif
                        @if($transition->is_initial)
                        <span class="badge badge-success d-block mt-1">
                            <i class="fas fa-flag"></i> Inicial
                        </span>
                        @endif
                        @if(!$transition->is_in_flow)
                        <span class="badge badge-warning d-block mt-1">
                            <i class="fas fa-exclamation-triangle"></i> No está activa
                        </span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($stepCount === 0)
                            <span class="badge badge-success mr-2"></span>
                            @endif
                            <strong>{{ $transition->fromInstruction->title }}</strong>
                        </div>
                        <small class="text-muted">{!! $transition->fromInstruction->content !!}</small>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                @if($transition->trigger === 'loop')
                                    <i class="fas fa-redo-alt text-warning me-2"></i>
                                @elseif($transition->trigger === 'time')
                                    <i class="fas fa-clock text-info me-2"></i>
                                @else
                                    <i class="fas fa-hand-pointer text-success me-2"></i>
                                @endif
                                <span class="badge badge-{{ $transition->trigger === 'time' ? 'info' : ($transition->trigger === 'user_choice' ? 'success' : 'warning') }}">
                                    {{ $transition->trigger_name }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @switch($transition->trigger)
                            @case('time')
                            <span class="text-info">
                                <i class="fas fa-stopwatch mr-1"></i>
                                {{ $transition->getTriggerConfigAttribute()['formatted'] }}
                            </span>
                            @break
                            @case('user_choice')
                            <span class="text-success">
                                <i class="fas fa-toggle-on mr-1"></i>
                                {{ $transition->getTriggerConfigAttribute()['formatted'] }}
                            </span>
                            @break
                            @case('loop')
                            <span class="text-warning">
                                <i class="fas fa-sync-alt mr-1"></i>
                                {{ $transition->getTriggerConfigAttribute()['formatted'] }}
                            </span>
                            @break
                            @endswitch
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-arrow-right text-muted mr-2"></i>
                            <div>
                                <strong>{{ $transition->toInstruction->title }}</strong>
                                <small class="text-muted d-block">{!! $transition->toInstruction->content !!}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button wire:click="editTransition({{ $transition->id }})" 
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteTransition({{ $transition->id }})" 
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay transiciones definidas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-adminlte-card>

<!-- Modal para crear/editar transición -->
@if($showModal)
<div id="{{ $modalId }}" class="modal show d-block" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $editingTransition ? 'Editar Transición' : 'Nueva Transición' }}
                </h5>
                <button type="button" class="close" wire:click="closeModal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Desde Instrucción</label>
                    <select wire:model="fromInstructionId" class="form-control">
                        <option value="">Seleccione una instrucción</option>
                        @foreach($availableInstructions as $instruction)
                        <option value="{{ $instruction->id }}">{{ $instruction->title }}</option>
                        @endforeach
                    </select>
                    @error('fromInstructionId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label>Hasta Instrucción</label>
                    <select wire:model="toInstructionId" class="form-control">
                        <option value="">Seleccione una instrucción</option>
                        @foreach($availableInstructions as $instruction)
                        <option value="{{ $instruction->id }}">{{ $instruction->title }}</option>
                        @endforeach
                    </select>
                    @error('toInstructionId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label>Tipo de Transición</label>
                    <select wire:model.live="trigger" class="form-control">
                        <option value="">Seleccione un tipo</option>
                        @foreach(App\Models\Transition::TRIGGER_TYPES as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('trigger') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
                @if($trigger === 'time')
                <div class="form-group">
                    <label>Tiempo (segundos)</label>
                    <input type="number" wire:model="timeSeconds" class="form-control">
                    @error('timeSeconds') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                @endif
                
                @if($trigger === 'user_choice')
                <div class="form-group">
                    <label>Botón DESA</label>
                    <select wire:model="desaButtonId" class="form-control">
                        <option value="">Seleccione un botón</option>
                        @foreach($availableDesaButtons as $button)
                        <option value="{{ $button->id }}">
                            {{ $button->label }} 
                        </option>
                        @endforeach
                    </select>
                    @error('desaButtonId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                @endif
                
                @if($trigger === 'loop')
                <div class="form-group">
                    <label>Número de repeticiones</label>
                    <input type="number" wire:model="loopCount" class="form-control">
                    @error('loopCount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                @endif
                
                <!-- Añadir justo antes del footer del modal -->
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" 
                        class="custom-control-input" 
                        id="is_initial" 
                        wire:model="isInitial">
                        <label class="custom-control-label" for="is_initial">
                            Establecer como transición inicial
                        </label>
                    </div>
                    @error('isInitial') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
            </div>
            
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="save">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif
</div>

@push('js')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('transition:confirm', (data) => {
            Swal.fire({
                title: data[0].title,
                text: data[0].text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.confirmedDelete(data[0].id);
                }
            });
        });
        
        Livewire.on('swal:success', (data) => {
            Swal.fire({
                title: data[0].title,
                text: data[0].text,
                icon: 'success',
            });
        });
        
    });
</script>
@endpush
