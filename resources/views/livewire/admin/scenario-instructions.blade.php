<div>
    <x-adminlte-card title="Instrucciones del Escenario" theme="olive" icon=""
    header-class="text-uppercase rounded-bottom border-info" collapsible removable>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-info"></i> NOTAS</h5>
        <ul class="mb-0">
            <li><strong>Títulos:</strong> Use números para indicar el orden (1, 2, 3, etc.)</li>
            <li><strong>Contenido:</strong> Describa claramente la acción a realizar. Sea conciso y específico.</li>
            <li><strong>Audio:</strong> Los archivos deben ser en formato MP3 o WAV</li>
            <li><strong>Tiempos de espera:</strong> Se deben añadir como una instrucción</li>
            <li><strong>Bucles:</strong> Se deben añadir como una instrucción</li>
        </ul>
    </div>
    
    <div class="mb-3">
        <button class="btn btn-primary" wire:click="createInstruction">
            <i class="fas fa-plus"></i> Nueva Instrucción
        </button>
    </div>

    <div class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Buscar" 
                   wire:model.live="search">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th wire:click="sortBy('id')" style="cursor: pointer;">
                        ID 
                        @if ($sortField === 'id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th width="30%" wire:click="sortBy('title')" style="cursor: pointer;">
                        Título
                        @if ($sortField === 'title')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th width="30%">Contenido</th>
                    <th>Audio</th>
                    <th width="10%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($instructions as $instruction)
                <tr>
                    <td>{{ $instruction->id }}</td>
                    <td>{{ $instruction->title }}</td>
                    <td>
                        <div class="instruction-content">
                            {!! Str::limit(strip_tags($instruction->content), 100) !!}
                            @if (strlen(strip_tags($instruction->content)) > 100)
                            <a href="#" onclick="showFullContent('{{ $instruction->id }}')">Ver más</a>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if ($instruction->audio_file)
                        <audio controls class="audio-player">
                            <source src="{{ asset('storage/' . $instruction->audio_file) }}" type="audio/mpeg">
                                Tu navegador no soporta el elemento de audio.
                            </audio>
                            @else
                            <span class="text-muted">Sin audio</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm" wire:click="editInstruction({{ $instruction->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="deleteInstruction({{ $instruction->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table> 
        </div>
    </x-adminlte-card>
    
    @if($showModal)
        <div id="{{ $modalId }}" class="modal show d-block" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditing ? 'Editar Instrucción' : 'Nueva Instrucción' }}</h5>
                    <button type="button" class="close" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                            wire:model="title">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group" wire:ignore>
                            <label>Contenido</label>
                            <textarea id="summernote-modal" 
                            class="form-control @error('content') is-invalid @enderror" 
                            wire:model.defer="content">{{ $content }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Audio</label>
                            @if($isEditing && $editingInstructionId)
                            @php
                            $instruction = $instructions->find($editingInstructionId);
                            @endphp
                            @if($instruction && $instruction->audio_file)
                            <div class="mb-2">
                                <audio controls class="audio-player">
                                    <source src="{{ asset('storage/' . $instruction->audio_file) }}" type="audio/mpeg">
                                        Tu navegador no soporta el elemento de audio.
                                    </audio>
                                    <div class="mt-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                            id="removeAudio" wire:model="removeAudio">
                                            <label class="custom-control-label text-danger" for="removeAudio">
                                                Eliminar audio actual
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endif
                                
                                <input type="file" class="form-control @error('audio_file') is-invalid @enderror"
                                wire:model="audio_file" accept="audio/mp3,audio/wav"
                                @if($removeAudio) disabled @endif>
                                @error('audio_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ $isEditing ? 'Actualizar' : 'Crear' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
        @endif
    </div>

    
    @push('js')

    <script>
        let modalEditor = null;
        
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('startEditing', () => {
                setTimeout(initSummernote, 100);
            });
            
            // Añadir este nuevo evento para el modal de creación
            @this.on('show-modal', () => {
                setTimeout(initSummernote, 100);
            });
            
            Livewire.on('endEditing', () => {
                if (modalEditor) {
                    modalEditor.summernote('destroy');
                    modalEditor = null;
                }
            });
            
            // SweetAlert confirmations
            Livewire.on('instructions:confirm', (data) => {
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
        
        function initSummernote() {
            if (modalEditor) {
                modalEditor.summernote('destroy');
            }
            
            modalEditor = $('#summernote-modal').summernote({
                height: 300,
                lang: 'es-ES',
                toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'table', 'picture']],
                ['misc', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        @this.set('content', contents);
                    }
                }
            });
        }
        
        function showFullContent(instructionId) {
            Swal.fire({
                title: 'Contenido completo',
                html: document.querySelector(`[data-instruction="${instructionId}"]`).innerHTML,
                width: '80%',
                padding: '2em'
            });
        }
    </script>
    @endpush