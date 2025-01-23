<div>
    <!-- Información general del DESA Trainer -->
    <div class="card mb-4 mt-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-info-circle"></i> Información del DESA Trainer
            </h3>
            <a href="{{ route('desa-trainers.index') }}" class="btn btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nombre:</dt>
                        <dd class="col-sm-9">{{ $desaTrainer->name }}</dd>
                        
                        <dt class="col-sm-3">Modelo:</dt>
                        <dd class="col-sm-9">{{ $desaTrainer->model }}</dd>
                        
                        <dt class="col-sm-3">Descripción:</dt>
                        <dd class="col-sm-9">
                            {!! $desaTrainer->description ?: 'Sin descripción' !!}
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Creado:</dt>
                        <dd class="col-sm-9">{{ $desaTrainer->created_at->format('d/m/Y H:i') }}</dd>
                        
                        <dt class="col-sm-3">Actualizado:</dt>
                        <dd class="col-sm-9">{{ $desaTrainer->updated_at->format('d/m/Y H:i') }}</dd>
                        
                        <dt class="col-sm-3">Total botones:</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-primary">
                                {{ $desaTrainer->buttons->count() }} botones configurados
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Áreas Interactivas</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" wire:loading.remove> <!-- Muestra este mensaje cuando Livewire no está cargando --> 
                        <i class="fas fa-info-circle"></i> 
                        Click en "Nuevo Botón" y después dibuja el área haciendo clicks en la imagen.
                        Doble click para terminar el área.
                    </div>
                    <div wire:loading> <!-- Muestra este mensaje cuando Livewire está procesando una acción -->
                        <div class="alert alert-warning">
                            <i class="fas fa-spinner fa-spin"></i> Procesando...
                        </div>
                    </div>
                    <div class="image-container position-relative" wire:ignore> <!-- Ignora Livewire en esta imagen para evitar interferencias -->
                        <img id="desaImage" src="{{ Storage::url($desaTrainer->image) }}" class="img-fluid" alt="{{ $desaTrainer->name }}">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Botones</h3>
                        <button type="button" 
                        class="btn btn-primary btn-sm"
                        wire:click="startNewButton"> <!-- Llama al método 'startNewButton' cuando se hace clic -->
                        <i class="fas fa-plus"></i> Nuevo Botón
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($showButtonForm)
                <div class="mb-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $editingButton ? 'Editar Botón' : 'Nuevo Botón' }}
                            </h5>
                            <form wire:submit.prevent="saveButton"> <!-- Llama al método 'saveButton' en el backend al enviar el formulario -->
                                <div class="form-group mt-3">
                                    <br>
                                    <label for="buttonLabel">Etiqueta</label>
                                    <!-- Vincula el valor del campo a la propiedad 'buttonLabel' en Livewire -->
                                    <input type="text" class="form-control @error('buttonLabel') is-invalid @enderror"  id="buttonLabel"  wire:model.live="buttonLabel" placeholder="Introduce un nombre para el botón">
                                    @error('buttonLabel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Color del botón -->
                                <div class="form-group mt-3">
                                    <label for="buttonColor">Color del área</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color @error('buttonColor') is-invalid @enderror"  id="buttonColor" wire:model.live="buttonColor" title="Elige el color del área">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fas fa-paint-brush"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @error('buttonColor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Colores predefinidos -->
                                    <div class="mt-2">
                                        @foreach(\App\Models\DesaButton::AVAILABLE_COLORS as $colorCode => $colorName)
                                        <!-- Cambia el color del botón con Livewire -->
                                        <button type="button" class="btn btn-sm rounded-circle m-1" style="width: 25px; height: 25px; background-color: {{ $colorCode }}" wire:click="$set('buttonColor', '{{ $colorCode }}')" title="{{ $colorName }}">
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Opción de parpadeo -->
                                <div class="form-group mt-3">
                                    <div class="custom-control custom-switch">
                                        <!-- Vincula el estado del checkbox a la propiedad 'isBlinking' en Livewire -->
                                        <input type="checkbox" class="custom-control-input" id="isBlinking" wire:model.live="isBlinking">
                                        <label class="custom-control-label" for="isBlinking">
                                            Área parpadeante
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si está activado, el área parpadeará para llamar la atención
                                    </small>
                                </div>
                                
                                <!-- Estado del área -->
                                <div class="form-group mt-3">
                                    <div class="d-flex align-items-center">
                                        <span class="mr-2">Estado del área:</span>
                                        @if(empty($buttonArea))
                                            <span class="badge bg-warning">Pendiente de dibujar</span>
                                        @else
                                            <span class="badge bg-success">Área definida</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary"
                                    @if(empty($buttonArea)) disabled @endif>
                                    <i class="fas fa-save"></i> 
                                    {{ $editingButton ? 'Actualizar' : 'Guardar' }}
                                </button>
                                <!-- Llama al método 'cancelButton' cuando se hace clic -->
                                <button type="button" class="btn btn-secondary" wire:click="cancelButton">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                        </form> <!-- Fin del formulario de crear/editar botones-->
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Lista de botones -->
            <div class="mt-3">
                <h5>Botones existentes</h5><br>
                @forelse($desaTrainer->buttons as $button)
                <div class="button-item card mb-2">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="color-preview mr-2" 
                                style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid #ddd; background-color: {{ $button->color }}">
                            </div>
                            <span class="button-label">
                                {{ $button->label }}
                                @if($button->is_blinking)
                                <i class="fas fa-lightbulb text-warning ml-1" title="Área parpadeante"></i>
                                @endif
                            </span>
                        </div>
                        
                        <div class="btn-group">
                            <!-- Llama al método 'editButton' para editar un botón -->
                            <button type="button" class="btn btn-sm btn-warning" wire:click="editButton({{ $button->id }})" data-toggle="tooltip" title="Editar botón">
                                <i class="fas fa-edit"></i>
                            </button>
                            <!-- Llama al método 'deleteButton' para eliminar un botón -->
                            <button type="button" class="btn btn-sm btn-danger" wire:click="deleteButton({{ $button->id }})"  data-toggle="tooltip" title="Eliminar botón">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-3">
                <i class="fas fa-info-circle"></i>
                <p class="mb-0">No hay botones definidos</p>
            </div>
            @endforelse
        </div>
    </div>
    
    
    @push('css')
    <style>
        .image-container {
            position: relative;
            width: fit-content;
            margin: 0 auto;
        }
        .canvas-container {
            position: absolute !important;
            top: 0;
            left: 0;
            z-index: 100;
        }
        #desaImage {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .color-preview {
            box-shadow: 0 0 3px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .color-preview:hover {
            transform: scale(1.2);
        }
        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.75em;
        }
        .form-control-color {
            width: 60px !important;
            padding: 0.375rem;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        .custom-control-input:checked ~ .custom-control-label::before {
            border-color: #007bff;
            background-color: #007bff;
        }
    </style>
    @endpush
    
    @push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
        let canvas; //El lienzo sobre el que vamos a dibujar
        let isDrawing = false; //Controla si estamos dibujando o no
        let points = []; //Array para almacenar los puntos del dibujo
        let activeShape; // La forma que estamos dibujando actualmente
        let blinkingIntervals = new Map(); // Para mantener registro de los intervalos de parpadeo
        
        // Función helper para convertir hex a rgba
        function hexToRgba(hex, alpha = 1) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }
        
        // Espera a que la imagen esté completamente cargada antes de inicializar el lienzo
        window.addEventListener('load', function() {
            const img = document.getElementById('desaImage'); //Obtiene la imagen
            if (img.complete) { //Si la imagen ya está cargada, inicializa el lienzo
                initCanvas(); 
            } else { //Si no, espera a que cargue
                img.onload = initCanvas;
            }
        });
        
        // Función que inicializa el lienzo de Fabric.js
        function initCanvas() {
            // Obtiene la imagen y crea un canvas sobre ella
            const img = document.getElementById('desaImage'); //Obtenemos la imagen
            // configuramos el tamaño del canvas igual que la imagen
            const container = img.parentElement; //Obtenemos el contenedor de la imagen
            
            const canvasEl = document.createElement('canvas'); //Creamos un nuevo canvas
            canvasEl.id = 'drawingCanvas'; //Añadimos un id al canvas
            container.appendChild(canvasEl); //Añade el canvas al DOM
            
            canvas = new fabric.Canvas('drawingCanvas', {
                width: img.naturalWidth, //Establecemos el ancho del lienzo  al ancho de la imagen
                height: img.naturalHeight //Establecemos la altura del lienzo a la altura de la imagen
            });
            
            const scale = img.clientWidth / img.naturalWidth; //Calculamos la escala en base al tamaño de la imagen
            canvas.setZoom(scale); //Ajustamos el zoom según la escala
            canvas.setDimensions({
                width: img.clientWidth, // Establece el ancho del lienzo según el tamaño del contenedor
                height: img.clientHeight // Establece la altura del lienzo según el tamaño del contenedor
            });
            
            setupCanvasEvents(); // Configura los eventos del lienzo
            loadExistingButtons();  // Carga los botones existentes si los hay
            
            canvas.setBackgroundImage(img.src, canvas.renderAll.bind(canvas)); // Establece la imagen como fondo del lienzo
        }
        
        // Configura los eventos del lienzo
        function setupCanvasEvents() {
            let lastClickTime = 0; // Variable para controlar el doble clic
            const doubleClickDelay = 300; // Retardo en milisegundos entre clics
            
            // Evento cuando se hace clic en el lienzo
            canvas.on('mouse:down', function(options) {
                if (!isDrawing) return; //Si no estamos dibujando no hacemos nada
                
                const currentTime = new Date().getTime(); //Obtiene el tiempo actual
                const timeDiff = currentTime - lastClickTime;  //Calcula la diferencia de tiempo entre clics
                
                if (timeDiff < doubleClickDelay) { //Si es un doble clic
                    if (points.length >= 3) { // Si tenemos suficientes puntos, terminamos el dibujo
                        completeDrawing();
                    }
                } else {
                    // Es un clic normal
                    const pointer = canvas.getPointer(options.e); //Obtiene la posición del puntero
                    points.push({ x: pointer.x, y: pointer.y }); //Añade el punto al array
                    
                    // Crea un círculo en la posición del clic
                    const circle = new fabric.Circle({
                        radius: 3,
                        fill: @this.buttonColor, //Color del botón
                        left: pointer.x, //Posición X del círculo
                        top: pointer.y, // Posición Y del círculo
                        selectable: false, // No se puede seleccionar
                        originX: 'center', 
                        originY: 'center'
                    });
                    
                    canvas.add(circle); //Añadimos el círculo al lienzo
                    
                    if (points.length > 1) { //Si ya hay más de un punto
                        drawPolygon(); //Dibuja el polígono provisional.
                    }
                }
                
                lastClickTime = currentTime; //Actualiza el tiempo del último clic
            });
            
            // Evento cuando el mouse se mueve
            canvas.on('mouse:move', function(options) {
                if (!isDrawing || points.length === 0) return; // Si no estamos dibujando o no hay puntos, no hacer nada
                
                if (activeShape) {
                    canvas.remove(activeShape); // Si ya hay una forma activa, la eliminamos
                }
                
                const pointer = canvas.getPointer(options.e);  // Obtiene la posición del puntero
                const tempPoints = [...points, { x: pointer.x, y: pointer.y }]; // Crea un array temporal con los puntos actuales y el nuevo punto
                
                // Crea un polígono provisional mientras se dibuja
                activeShape = new fabric.Polygon(tempPoints, {
                    fill: hexToRgba(@this.buttonColor, 0.2),
                    stroke: @this.buttonColor,
                    strokeWidth: 2,
                    selectable: false
                });
                
                canvas.add(activeShape); //Añade el polígono al lienzo
                canvas.renderAll(); //Renderiza todos los objetos en el lienzo
            });
        }
        
        // Función para completar el dibujo
        function completeDrawing() {
            if (!isDrawing || points.length < 3) return; // Si no estamos dibujando o hay menos de 3 puntos, no hacer nada
            
            isDrawing = false; //Detiene el dibujo
            
            if (activeShape) {
                canvas.remove(activeShape); //Elimina la forma activa
            }
            
            // Elimina los puntos temporales (círculos)
            canvas.getObjects('circle').forEach(obj => canvas.remove(obj));
            
            // Crea el polígono final con los puntos
            const finalPolygon = new fabric.Polygon(points, {
                fill: hexToRgba(@this.buttonColor, 0.2),
                stroke: @this.buttonColor,
                strokeWidth: 2,
                selectable: false
            });
            
            canvas.add(finalPolygon); //Añade el polígono al final del lienzo
            canvas.renderAll(); //Renderiza todos los objetos
            
            // Enviar puntos al componente Livewire
            @this.dispatch('areaSelected', { points: points });
            
            // Resetear los puntos y la forma activa
            points = [];
            activeShape = null;
        }
        
        // Función para dibujar el polígono
        function drawPolygon() {
            if (activeShape) {
                canvas.remove(activeShape);  // Si ya hay una forma activa, la eliminamos
            }
            
            // Crea el polígono con los puntos actuales
            activeShape = new fabric.Polygon(points, {
                fill: hexToRgba(@this.buttonColor, 0.2),
                stroke: @this.buttonColor,
                strokeWidth: 2,
                selectable: false
            });
            
            canvas.add(activeShape); //Añade el polígono a la imagen
            canvas.renderAll(); //Renderiza la imagen
        }
        
        function startBlinking(polygon, polygonId) {
            // Detener el parpadeo anterior si existe
            if (blinkingIntervals.has(polygonId)) {
                clearInterval(blinkingIntervals.get(polygonId));
            }
            
            const originalFill = polygon.fill; //Guarda el color original del relleno
            const originalStroke = polygon.stroke; //Guarda el color original del borde
            let isHighlighted = false; //controla si el polígono está resaltado o no
            
            // Intervalo para alternar entre opacidad
            const interval = setInterval(() => {
                isHighlighted = !isHighlighted; //Cambia el estado de resaltado
                const opacity = isHighlighted ? 0.6 : 0.2; //Alterna la opacidad
                polygon.set({ 
                    fill: hexToRgba(originalStroke, opacity) //Cambia el relleno con la nueva opacidad
                });
                canvas.renderAll(); //renderiza el lienzo
            }, 1000); //Cada segundo (1000ms)
            
            blinkingIntervals.set(polygonId, interval); //Guarda el intervalo en el mapa
        }
        
        // Función para cargar los botones existentes desde el backend
        function loadExistingButtons() {
            const buttons = @json($desaTrainer->buttons); //Obtiene los botones del backend
            clearCanvas(); //limpia el lienzo
            
            buttons.forEach(button => {
                //Crea un polígono para cada botón
                const polygon = new fabric.Polygon(button.area, {
                    id: button.id,
                    fill: hexToRgba(button.color, 0.2),
                    stroke: button.color,
                    strokeWidth: 2,
                    selectable: false,
                });
                canvas.add(polygon); //añade el polígono al lienzo
                
                if (button.is_blinking) { //Si el botón debe parpadear, inicia el parpadeo
                    startBlinking(polygon, button.id);
                }
            });
            canvas.renderAll(); //Renderiza el lienzo
        }
        
        // Función para limpiar el lienzo, excluyendo algunos objetos
        function clearCanvas(excludeIds = []) {
            // Detiene todos los intervalos de parpadeo
            blinkingIntervals.forEach((interval) => clearInterval(interval));
            blinkingIntervals.clear(); // Limpia el mapa de intervalos
            
            // Filtra los objetos que no deben ser eliminados
            const objectsToRemove = canvas.getObjects().filter(obj => {
                return !(obj.id && excludeIds.includes(obj.id)); // Excluye los objetos por ID
            });
            
            objectsToRemove.forEach(obj => canvas.remove(obj)); // Elimina los objetos del lienzo
            canvas.renderAll(); // Renderiza el lienzo
        }
        
        // Eventos de Livewire
        // Escucha cuando Livewire se ha inicializado en el frontend
        document.addEventListener('livewire:initialized', () => {
            //Inicializa el proceso de dibujo en un lienzo (canvas), limpiándolo y preparando variables para comenzar a dibujar.
            
            // Evento para iniciar el proceso de dibujo en el lienzo
            Livewire.on('startDrawing', () => {
                clearCanvas(); // Limpia el lienzo para empezar desde cero
                isDrawing = true; // Marca que se está dibujando
                points = []; // Reinicia el array de puntos de la figura
            });
            
            // Evento para resetear el lienzo, manteniendo los botones existentes
            Livewire.on('resetCanvas', () => {
                const existingButtons = canvas.getObjects().filter(obj => obj.id); //Obtiene los objetos del lienzo que tienen ID
                canvas.clear(); //Limpia todo el lienzo
                
                // Vuelve a agregar los botones existentes al lienzo
                existingButtons.forEach(button => {
                    canvas.add(button);
                });
                
                isDrawing = false; //desactiva el modo dibujo
                points = []; // Reinicia los puntos
                canvas.renderAll(); //Vuelve a renderizar todo el contenido del lienzo
            });
            
            // Evento para guardar los botones en el lienzo, generando polígonos con áreas y colores definidos
            //  Si el botón es parpadeante, se activa una función de parpadeo.
            Livewire.on('buttonSaved', (data) => {
                clearCanvas(); // Limpia el lienzo para preparar el espacio para los nuevos botones
                // Itera sobre los botones pasados desde el backend
                data[0].buttons.forEach(button => {
                    // Crea un nuevo polígono en el lienzo usando las coordenadas definidas en el área del botón
                    const polygon = new fabric.Polygon(button.area, {
                        id: button.id, // Asigna un ID único al polígono
                        fill: hexToRgba(button.color, 0.2), // Define el color de relleno con una transparencia
                        stroke: button.color, // Define el color del borde del polígono
                        strokeWidth: 2, // Establece el grosor del borde
                        selectable: false,  // Impide que el polígono sea seleccionado o modificado
                    });
                    canvas.add(polygon); // Añade el polígono al lienzo
                    
                    if (button.is_blinking) { // Si el botón es parpadeante, activa la función de parpadeo
                        startBlinking(polygon, button.id); // Llama a la función que inicia el parpadeo en el polígono
                    }
                });
                canvas.renderAll(); // Vuelve a renderizar el lienzo para mostrar todos los objetos
            });
            
            // Evento para manejar la eliminación de botones y actualizar el lienzo
            Livewire.on('buttonDeleted', (data) => {
                clearCanvas(); // Limpia el lienzo antes de añadir los nuevos elementos
                data[0].buttons.forEach(button => {     // Itera sobre los botones pasados desde el backend
                    // Crea un nuevo polígono usando las coordenadas del área del botón
                    const polygon = new fabric.Polygon(button.area, {
                        id: button.id, // Asigna un ID único al polígono
                        fill: hexToRgba(button.color, 0.2), // Define el color de relleno con transparencia
                        stroke: button.color,  // Establece el color del borde del polígono
                        strokeWidth: 2, // Define el grosor del borde
                        selectable: false, // Impide que el polígono sea seleccionado o modificado
                    });
                    canvas.add(polygon); // Añade el polígono al lienzo
                    
                    // Si el botón es parpadeante, activa la función de parpadeo
                    if (button.is_blinking) {
                        startBlinking(polygon, button.id); // Llama a la función que inicia el parpadeo del polígono
                    }
                });
                canvas.renderAll(); // Vuelve a renderizar el lienzo para reflejar todos los objetos añadidos
            });
            
            // Carga una nueva área de dibujo, permitiendo redibujar el área seleccionada y añadir un botón para volver a dibujar.
            Livewire.on('loadArea', (data) => {
                // Filtra los polígonos actuales del lienzo que no tengan el mismo id que el botón
                const existingPolygons = canvas.getObjects().filter(obj => obj.id !== data.buttonId);
                canvas.clear();  // Limpia el lienzo para preparar el área nueva
                
                // Vuelve a añadir los polígonos existentes al lienzo
                existingPolygons.forEach(polygon => {
                    canvas.add(polygon);
                });
                
                isDrawing = true;  // Activa el estado de dibujo
                points = data.area.map(p => ({ x: p.x, y: p.y })); // Asigna las coordenadas del área a la variable `points`
                
                // Crea un botón para permitir redibujar el área
                const btnRedibujar = document.createElement('button');
                btnRedibujar.className = 'btn btn-warning btn-sm mb-3';
                btnRedibujar.innerHTML = '<i class="fas fa-pencil-alt"></i> Redibujar área';
                btnRedibujar.onclick = function() {
                    // Filtra los polígonos actuales para excluir el botón que se está redibujando
                    const otherPolygons = canvas.getObjects().filter(obj => obj.id && obj.id !== data.buttonId);
                    canvas.clear(); // Limpia el lienzo de nuevo
                    otherPolygons.forEach(polygon => canvas.add(polygon));  // Añade los otros polígonos al lienzo
                    
                    isDrawing = true;  // Activa el estado de dibujo
                    points = []; // Reinicia los puntos del área
                    this.remove(); // Elimina el botón de redibujar
                };
                
                // Inserta el botón de redibujar en el contenedor de la imagen, antes del lienzo
                document.querySelector('.image-container').insertBefore(btnRedibujar, canvas.wrapperEl);
                
                // Crea un nuevo polígono con las coordenadas del área y lo añade al lienzo
                const editingPolygon = new fabric.Polygon(data.area, {
                    id: data.buttonId, // Asigna el ID del botón
                    fill: hexToRgba(data.color, 0.2), // Define el color de relleno con transparencia
                    stroke: data.color, // Establece el color del borde
                    strokeWidth: 2, // Establece el grosor del borde
                    selectable: false  // Impide que el polígono sea seleccionado
                });
                
                canvas.add(editingPolygon); // Añade el polígono de edición al lienzo
                // Si el área es parpadeante, inicia el parpadeo del polígono
                if (data.isBlinking) {
                    startBlinking(editingPolygon, data.buttonId); // Llama a la función para iniciar el parpad
                }
                
                canvas.renderAll();   // Vuelve a renderizar el lienzo para reflejar los cambios
            });
        });
        
        // Inicializar tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    @endpush
</div>