<div>
    <!-- Incluye el encabezado del escenario desde un partial -->
    @include('livewire.partials.scenario-header', ['scenario' => $scenario])
    
    <div class="row">
        <!-- Panel izquierdo contiene el DESA trainer -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <!-- Contenedor del DESA con wire:ignore para evitar re-renders de Livewire 
                        wire:ignore evita que Livewire re-renderice esta sección
                        Es necesario porque los botones del DESA se manejan con JavaScript-->
                    <div class="position-relative d-flex justify-content-center" wire:ignore>
                        {{-- Botón para activar modo pantalla completa --}}
                        <button data-bs-toggle="tooltip" id="toggleFullscreen" class="btn btn-secondary position-absolute top-0 end-0 m-2">
                            <i class="fas fa-expand"></i>
                        </button>
                        <!-- Contenedor de la imagen del DESA, ancho 60% -->
                        <div id="desaContainer" class="w-60 position-relative">
                            {{-- Imagen del DESA trainer --}}
                            <img src="{{ Storage::url($scenario->desaTrainer->image) }}" class="w-100" alt="{{ $scenario->desaTrainer->name }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel derecho contiene controles e instrucciones -->
        <div class="col-md-4">
            @include('livewire.partials.simulation-controls')
            <!-- Muestra instrucciones solo si la simulación está activa -->
            @if($isPlaying && $currentInstructionId)
                @include('livewire.partials.current-instruction')
            @endif
        </div>
    </div>

    {{-- 
        Sección de JavaScript
        Se añade al stack 'js' definido en el layout principal 
    --}}
    @push('js')
    <script>
        /**
        * CONFIGURACIÓN DEL MODO PANTALLA COMPLETA
        * Esta función maneja toda la lógica relacionada con el modo de pantalla completa
        * del simulador DESA.
        */
        function setupFullscreenMode() {
            // Obtener referencias a elementos DOM necesarios
            const toggleButton = document.getElementById('toggleFullscreen');
            const desaWrapper = document.querySelector('.position-relative.d-flex');

            // Lista de selectores CSS de elementos que se ocultarán en modo pantalla completa
            const elementsToHide = [
            'nav',
            '.content-header',
            '.main-footer',
            '.card-header',
            '.col-md-4'
            ];
            
            /**
            * Alterna entre modo pantalla completa y normal
            * Cambia clases CSS y visibilidad de elementos
            */
            function toggleFullscreen() {
                const isFullscreen = desaWrapper.classList.contains('fullscreen-mode');
                const icon = toggleButton.querySelector('i');
                
                if (!isFullscreen) {
                    desaWrapper.classList.add('fullscreen-mode');
                    icon.classList.replace('fa-expand', 'fa-compress');
                    elementsToHide.forEach(selector => {
                        document.querySelectorAll(selector).forEach(el => {
                            el.classList.add('hidden-element');
                        });
                    });
                } else {
                    // Desactivar modo pantalla completa
                    desaWrapper.classList.remove('fullscreen-mode');
                    icon.classList.replace('fa-compress', 'fa-expand');
                    elementsToHide.forEach(selector => {
                        document.querySelectorAll(selector).forEach(el => {
                            el.classList.remove('hidden-element');
                        });
                    });
                }
                
                // Disparar evento resize para actualizar posición de botones del DESA
                window.dispatchEvent(new Event('resize'));
            }
            
            // Event listener para el botón de toggle
            // Asociar evento click al botón de pantalla completa
            toggleButton.addEventListener('click', toggleFullscreen);
            

            /**
            * Escuchar evento de Livewire para activar pantalla completa automáticamente
            * cuando se inicia la simulación
            */
            Livewire.on('startFullscreen', () => {
                // Delay para asegurar que los botones estén inicializados
                setTimeout(() => {
                    if (!desaWrapper.classList.contains('fullscreen-mode')) {
                        toggleFullscreen();
                        
                        // Reactivar botones que estaban activos
                        /*document.querySelectorAll('.desa-button').forEach(button => {
                            const polygon = button.querySelector('polygon');
                            const buttonId = button.getAttribute('data-button-id');

                            if (polygon && polygon.classList.contains('active')) {
                                Livewire.dispatch('activateButton', { 
                                    buttonId: parseInt(buttonId)
                                });
                            }
                        }); */
                    }
                }, 100); // 100ms de delay
            });
        }

        /**
        * INICIALIZACIÓN DEL SIMULADOR
        * Este evento se dispara cuando Livewire está listo
        */
        document.addEventListener('livewire:initialized', () => {
            // Obtener datos del DESA y sus botones del backend
            const desaTrainer = @json($scenario->desaTrainer);
            const buttons = @json($scenario->desaTrainer->buttons);
            
            // Variable global para acceder a los botones desde otras funciones
            globalButtons = buttons;

            // Inicializar todos los componentes
            initializeDesaButtons(buttons); //Inicializa botones del DESA
            setupResizeHandler();           //Configurar manejo de redimensionamiento
            setupDesaButtons();             // Configurar eventos de botones
            setupTransitionTimers();        // Configurar temporizadores
            setupAudioHandling();           // Configurar manejo del audio
            setupFullscreenMode();          // Configurar modo pantalla completa
        });
        
        /**
        * INICIALIZACIÓN DE BOTONES DEL DESA
        * Crea y configura los botones interactivos sobre la imagen del DESA
        */
        function initializeDesaButtons(buttons) {
            const container = document.getElementById('desaContainer');
            const img = container.querySelector('img');
            
            // Esperar a que la imagen esté cargada antes de crear los botones
            if (img.complete) {
                createButtons();
            } else {
                img.onload = createButtons;
            }
        
            /**
            * Crea todos los botones del DESA
            * Elimina botones existentes y crea nuevos basados en la configuración
            */
            function createButtons() {
                // Eliminar botones existentes
                container.querySelectorAll('.desa-button').forEach(el => el.remove());
            
                // Crear nuevos botones
                buttons.forEach(button => {
                    console.log('Creando botón:', button.id);
                    
                    const buttonElement = createButtonElement(button, img);
                    container.appendChild(buttonElement);
                    
                    // Cambiar el event listener al polígono
                    // Configurar evento click en el polígono del botón
                    const polygon = buttonElement.querySelector('polygon');
                    polygon.addEventListener('click', (event) => {
                        const buttonId = parseInt(buttonElement.getAttribute('data-button-id'));
                        console.log('Click en botón:', buttonId, 'Activo:', polygon.classList.contains('active'));
                        
                        if (polygon.classList.contains('active')) {
                        console.log('Enviando evento buttonPressed para botón:', buttonId);
                            Livewire.dispatch('buttonPressed', { 
                            id: buttonId
                            });
                        }
                    });
                });
            }   
        }
        
        /**
        * CREACIÓN DE ELEMENTOS DE BOTÓN
        * Crea un único botón del DESA con sus elementos SVG
        */
        function createButtonElement(buttonData, img) {
            // Crear contenedor del botón
            const button = document.createElement('div');
            button.id = `desa-button-${buttonData.id}`;
            button.className = 'desa-button';
            button.setAttribute('data-button-id', buttonData.id);
            
            // Crear elemento SVG
            const svgNS = "http://www.w3.org/2000/svg";
            const svg = document.createElementNS(svgNS, "svg");
            
            // Configurar dimensiones basadas en la imagen
            const imgNaturalWidth = img.naturalWidth;
            const imgNaturalHeight = img.naturalHeight;
            
            svg.setAttribute("width", "100%");
            svg.setAttribute("height", "100%");
            svg.setAttribute("viewBox", `0 0 ${imgNaturalWidth} ${imgNaturalHeight}`);
            svg.style.position = "absolute";
            
            // Crear el polígono con las coordenadas originales
            const polygon = document.createElementNS(svgNS, "polygon");
            const points = buttonData.area.map(p => `${p.x},${p.y}`).join(' ');
            polygon.setAttribute("points", points);
            polygon.setAttribute("fill", `${buttonData.color}80`);
            polygon.setAttribute("stroke", buttonData.color);
            polygon.setAttribute("stroke-width", "2");

            // Ensamblar elementos
            svg.appendChild(polygon);
            button.appendChild(svg);
            
            return button;
        }
        
        /**
        * MANEJO DE REDIMENSIONAMIENTO
        * Gestiona la actualización de botones cuando cambia el tamaño de la ventana
        */
        function setupResizeHandler() {
            let resizeTimeout;
            let activeButtons = new Set(); //Nuevo: Set para guardar botones activos
            
            window.addEventListener('resize', () => {
                // Guardar estado de botones activos antes del resize
                document.querySelectorAll('.desa-button polygon.active').forEach(polygon => {
                    const buttonId = polygon.closest('.desa-button').getAttribute('data-button-id');
                    activeButtons.add(buttonId);
                });
                
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    const container = document.getElementById('desaContainer');
                    const img = container.querySelector('img');
                    const buttons = container.querySelectorAll('.desa-button');
                    
                    // Recrear cada botón con las nuevas dimensiones
                    buttons.forEach(button => {
                        const buttonId = button.getAttribute('data-button-id');
                        const buttonData = globalButtons.find(b => b.id === parseInt(buttonId));
                        if (buttonData) {
                            const newButton = createButtonElement(buttonData, img);
                            button.replaceWith(newButton);
                            
                            // Restaurar estado activo si es necesario
                            if (activeButtons.has(buttonId)) {
                                const newPolygon = newButton.querySelector('polygon');
                                if (newPolygon) {
                                    newPolygon.classList.add('active');
                                }
                            }
                        }
                    });
                    activeButtons.clear();
                }, 100);
            });
        }
        
        // Variables globales para el manejo de audio
        let audioPlayer = null;
        let progressBarElement = null;

        /**
        * DETENER AUDIO ACTUAL
        * Detiene la reproducción y limpia los elementos de audio
        */
        function stopCurrentAudio() {
            if (audioPlayer) {
                audioPlayer.pause();
                audioPlayer.removeAttribute('src');
                audioPlayer.load();
                audioPlayer = null;
            }
            removeProgressBar();
        }
        
        /**
        * ELIMINAR BARRA DE PROGRESO
        * Elimina el elemento visual de progreso de audio
        */
        function removeProgressBar() {
            if (progressBarElement) {
                progressBarElement.remove();
                progressBarElement = null;
            }
        }
        
        /**
        * CONFIGURACIÓN DE MANEJO DE AUDIO
        * Configura la reproducción de audio y su interfaz visual
        */
        function setupAudioHandling() {
            // Actualiza la barra de progreso durante la reproducción
            function updateProgressBar() {
                if (!audioPlayer || !progressBarElement) return;
                
                const bar = progressBarElement.querySelector('.progress-bar');
                if (bar && !isNaN(audioPlayer.duration) && isFinite(audioPlayer.duration)) {
                    const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                    bar.style.width = `${progress}%`;
                }
            }
            
            // Crea la barra de progreso visual
            function createProgressBar() {
                if (!audioPlayer) return;
                
                removeProgressBar(); // Eliminar barra anterior si existe

                //Crear nueva barra de progreso
                progressBarElement = document.createElement('div');
                progressBarElement.className = 'audio-progress mt-2';
                progressBarElement.innerHTML = `
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>`;
                
                // Agregar al DOM
                const instructionCard = document.querySelector('.current-instruction');
                if (instructionCard) {
                    instructionCard.appendChild(progressBarElement);
                }
                
                // Configurar el evento timeupdate
                // Actualizar progreso durante la reproducción
                audioPlayer.addEventListener('timeupdate', updateProgressBar);
            }
            
            /**
            * Evento Livewire: Iniciar reproducción de audio
            */
            Livewire.on('initAudioPlayback', (url) => {
                console.log('Iniciando reproducción:', url);
                
                stopCurrentAudio();
                
                audioPlayer = new Audio();

                // Configurar eventos del reproductor de audio
                audioPlayer.addEventListener('canplaythrough', () => {
                    try {
                        const playPromise = audioPlayer.play();
                        if (playPromise !== undefined) {
                            playPromise
                            .then(() => {
                                console.log('Reproducción iniciada exitosamente');
                                createProgressBar();
                            })
                            .catch(error => {
                                console.error('Error iniciando reproducción:', error);
                                stopCurrentAudio();
                                Livewire.dispatch('audioEnded');
                            });
                        }
                    } catch (error) {
                        console.error('Error en reproducción:', error);
                        stopCurrentAudio();
                        Livewire.dispatch('audioEnded');
                    }
                }, { once: true });
                
                // Manejar errores de audio
                audioPlayer.addEventListener('error', (e) => {
                    console.error('Error cargando audio:', e);
                    stopCurrentAudio();
                    Livewire.dispatch('audioEnded');
                });
                
                // Manejar fin de reproducción
                audioPlayer.addEventListener('ended', () => {
                    console.log('Audio finalizado');
                    // Asegurarse de que la barra llegue al 100% antes de eliminarla
                    const bar = progressBarElement?.querySelector('.progress-bar');
                    if (bar) {
                        bar.style.width = '100%';
                        // Pequeño retraso antes de limpiar todo
                        setTimeout(() => {
                            stopCurrentAudio();
                            Livewire.dispatch('audioEnded');
                        }, 100);
                    } else {
                        stopCurrentAudio();
                        Livewire.dispatch('audioEnded');
                    }
                });

                // Iniciar carga del audio
                audioPlayer.src = url;
                audioPlayer.load();
            });
            
            // Evento Livewire: Detener audio actual
            Livewire.on('stopCurrentAudio', () => {
                console.log('Deteniendo audio actual');
                stopCurrentAudio();
            });
        }
        
        /**
        * CONFIGURACIÓN DE BOTONES DEL DESA
        * Configura los eventos y comportamiento de los botones
        */
        function setupDesaButtons() {
            // Eliminar eventos de click anteriores
            document.querySelectorAll('.desa-button').forEach(button => {
                const clone = button.cloneNode(true);
                button.parentNode.replaceChild(clone, button);
            });

            // Configurar manejo de clicks
            document.getElementById('desaContainer').addEventListener('click', (event) => {
                const polygon = event.target.closest('polygon');
                if (polygon) {
                    const button = polygon.closest('.desa-button');
                    if (button && polygon.classList.contains('active')) {
                        const buttonId = parseInt(button.getAttribute('data-button-id'));
                        console.log('Click en botón activo:', buttonId);
                        
                        // Intentar llamar directamente al método
                        // Llamar al método de Livewire
                        @this.handleButtonPress(buttonId).then(() => {
                            console.log('Método llamado directamente');
                        }).catch(error => {
                            console.error('Error llamando al método:', error);
                        });
                    }
                }
            });
            
            /**
            * Evento Livewire: Activar un botón específico
            * Maneja diferentes formatos de datos del evento
            */
            Livewire.on('activateButton', (eventData) => {
                let buttonId;
                // Extraer buttonId según el formato de los datos
                if (Array.isArray(eventData)) {
                    buttonId = eventData[0]?.buttonId;
                } else if (eventData.data) {
                    buttonId = eventData.data.buttonId;
                }
                
                console.log('Activando botón_:', buttonId);
                const button = document.querySelector(`#desa-button-${buttonId}`);
                if (button) {
                    const polygon = button.querySelector('polygon');
                    if (polygon) {
                        polygon.classList.add('active');
                        console.log('Botón activado:', buttonId);
                    }
                }
            });

            /**
            * Evento Livewire: Desactivar todos los botones
            * Elimina la clase 'active' de todos los polígonos
            */
            Livewire.on('deactivateAllButtons', () => {
                document.querySelectorAll('.desa-button polygon').forEach(polygon => {
                    polygon.classList.remove('active');
                });
            });
        }
        
        /**
        * CONFIGURACIÓN DE TEMPORIZADORES
        * Maneja los temporizadores para las transiciones automáticas
        */
        function setupTransitionTimers() {
            let timers = new Map();
            /**
            * Evento Livewire: Iniciar temporizador de transición
            * Configura un temporizador para una transición específica
            */
            Livewire.on('startTransitionTimer', (eventData) => {
                console.log('Datos recibidos:', eventData);
                
                // Los datos están dentro de eventData.data
                const data = eventData.data;
                
                if (data && data.transitionId && data.seconds) {
                    // Limpiar timer existente si existe
                    if (timers.has(data.transitionId)) {
                        clearTimeout(timers.get(data.transitionId));
                    }
                    
                    const seconds = parseInt(data.seconds);
                    console.log(`Configurando timer para transición ${data.transitionId} por ${seconds} segundos`);
                    
                    // Agregar un contador visual
                    let timeLeft = seconds;
                    const countInterval = setInterval(() => {
                        timeLeft--;
                        console.log(`Tiempo restante para transición ${data.transitionId}: ${timeLeft} segundos`);
                        if (timeLeft <= 0) {
                            clearInterval(countInterval);
                        }
                    }, 1000);
                    
                    const timer = setTimeout(() => {
                        clearInterval(countInterval);
                        console.log('Timer completado, disparando transitionTimeout para:', data.transitionId);
                        Livewire.dispatch('transitionTimeout', { transitionId: data.transitionId });
                    }, seconds * 1000);
                    
                    timers.set(data.transitionId, timer);
                } else {
                    console.error('Datos de timer incompletos:', eventData);
                }
            });
            
            /**
            * Evento Livewire: Limpiar todos los temporizadores
            * Cancela todos los temporizadores activos
            */
            Livewire.on('clearTimers', () => {
                console.log('Limpiando todos los timers activos');
                timers.forEach((timer, id) => {
                    clearTimeout(timer);
                });
                timers.clear();
            });
        }
        
        /**
        * Evento Livewire: Simulación detenida
        * Limpia todos los estados y elementos visuales cuando se detiene la simulación
        */
        Livewire.on('simulationStopped', () => {
            // Detener reproducción de audio
            stopCurrentAudio();
            
            // Ocultar la instrucción actual si está visible
            const currentInstruction = document.querySelector('.current-instruction');
            if (currentInstruction) {
                currentInstruction.style.opacity = '0';
                setTimeout(() => {
                    currentInstruction.style.opacity = '1';
                }, 100);
            }
            
            // Reiniciar cualquier otro estado visual que sea necesario
            // Desactivar todos los botones
            document.querySelectorAll('.desa-button polygon').forEach(polygon => {
                polygon.classList.remove('active');
            });
            
            console.log('Simulación detenida y estado limpio');
        });
    </script>
    @endpush
    
    {{-- 
        Sección de estilos CSS
        Se añade al stack 'css' definido en el layout principal 
    --}}
    @push('css')
    <style>
        /* Estilos para modo pantalla completa */
        .fullscreen-mode {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 !important;
        }
        
        /* Posicionamiento del botón en modo pantalla completa */
        .fullscreen-mode #toggleFullscreen {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10000;
        }

        /* Ajustes del contenedor DESA en pantalla completa */
        .fullscreen-mode #desaContainer {
            width: auto !important;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        /* Ajustes de la imagen en pantalla completa */
        .fullscreen-mode #desaContainer img {
            max-height: 100vh;
            width: auto;
            object-fit: contain;
        }
        
        /* Clase para ocultar elementos */
        .hidden-element {
            display: none !important;
        }
    
        /* DESA Buttons en fullscreen */
        .fullscreen-mode .desa-button {
            position: absolute;
            inset: 0;
            pointer-events: none;
            width: 100%;
            height: 100%;
        }
        
        .fullscreen-mode .desa-button svg {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
        }
        

        /* DESA Container */
        #desaContainer {
            position: relative;
            width: 100%;
        }
        
        #desaContainer img {
            display: block;
            max-width: 100%;
            height: auto;
        }
        
        /* DESA Buttons */
        .desa-button {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }
        
        .desa-button polygon {
            pointer-events: all;
            cursor: pointer;
            transition: opacity 0.3s;
            opacity: 0.7; /* Opacidad base más alta */
        }
        
        .desa-button:hover polygon { opacity: 0.8; }
        .desa-button polygon.active { animation: blink 1.2s infinite; }
        
        /* Estilos para el indicador de audio */
        .audio-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #17a2b8;
        }

        /* Animación de onda de audio */
        .audio-wave {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        
        .audio-wave span {
            width: 3px;
            height: 15px;
            background-color: currentColor;
            animation: audio-wave 1.2s infinite ease-in-out;
        }

        /* Retrasos en la animación de onda */
        .audio-wave span:nth-child(2) { animation-delay: 0.2s; }
        .audio-wave span:nth-child(3) { animation-delay: 0.4s; }
        
        /* Animations */
        /* Keyframes para las animaciones */
        @keyframes audio-wave {
            0%, 100% { transform: scaleY(0.5); }
            50% { transform: scaleY(1); }
        }
        
        @keyframes blink {
            0% { opacity: 0.15; }  /* Más transparente al inicio */  
            50% { opacity: 0.85; } /* Más opaco en el medio */
            100% { opacity: 0.15; }
        }
        
        /* Utilities */
        .w-60 { width: 60% !important; }
        .instruction-content { font-size: 1.1rem; line-height: 1.6; }
        
        /* Progress Bar */
        /* Estilos para la barra de progreso de audio */
        .audio-progress {
            margin-top: 1rem;
        }
        
        .audio-progress .progress {
            background-color: #e9ecef;
            border-radius: 0.25rem;
            height: 10px;
        }
        
        .audio-progress .progress-bar {
            background-color: #007bff;
            transition: width 0.1s ease;
        }
    </style>
    @endpush