<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scenario;
use App\Models\Instruction;
use App\Models\Transition;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ScenarioSimulation extends Component
{
    //Propiedades del componente
    public $scenario; // Escenario actual
    public $currentInstructionId = null; //ID de la instrucción actual
    public $currentTransitionId = null; //ID de la transición actual
    public $isPlaying = false; // Estado de la simulación (si está en reproducción)
    public $audioPlaying = false; // Estado de la reproducción de audio
    public $loopCounts = [];  //Conteo de bucles para las transiciones
    public $errorState = null; // Estado de error
    public $simulationCompleted = false; // Indica si la simulación ha terminado
    
    // Método que se ejecuta al montar el componente
    // Se ejecuta al montar el componente, se pasa el escenario
    public function mount(Scenario $scenario)
    {
        $this->scenario = $scenario;
        
    }
    
    // Maneja un error, mostrando el mensaje y deteniendo la simulación
    protected function handleError($message) {
        $this->errorState = $message;
        $this->stopSimulation();
        $this->dispatch('simulationError', $message); // Se dispara un evento de error
    }
    
    // Valida que la transición sea válida
    protected function validateTransition($transition)
    {
        if (!$transition || !$transition->from_instruction_id) {
            $this->handleError('Transición inválida');
            return false;
        }
        return true;
    }
    
    // Inicia la simulación
    public function startSimulation()
    {
        $this->isPlaying = true; // Activa la simulación
        $this->loopCounts = []; // Resetea los contadores de bucles
        
        // Disparar evento para activar fullscreen
        $this->dispatch('startFullscreen');

        // Busca la transición inicial (is_initual = true)
        $firstTransition = Transition::whereHas('fromInstruction', function($query) {
            $query->where('scenario_id', $this->scenario->id);
        })
        ->where('is_initial', true)
        ->first();
        
        if (!$firstTransition) {
            $this->handleError('No se ha definido una transición inicial');
            return;
        }
        
        if (!$this->validateTransition($firstTransition)) {
            return;
        }

        // Ejecuta la transición inicial
        $this->executeTransition($firstTransition);
    }
    
    // Método que verifica si existen transiciones para el escenario actual
    public function hasTransitions()
    {
        return Transition::whereHas('fromInstruction', function($query) {
            $query->where('scenario_id', $this->scenario->id);
        })->exists();
    }
    
    // Ejecuta una transición, gestionando los diferentes tipos de triggers
    protected function executeTransition($transition)
    {
        // Registra la información de la transición
        Log::info('Ejecutando transición:', [
            'id' => $transition->id,
            'from' => $transition->from_instruction_id,
            'to' => $transition->to_instruction_id,
            'trigger' => $transition->trigger,
            'time_seconds' => $transition->time_seconds ?? null
        ]);
        
        // Limpiar estado anterior
        if ($this->audioPlaying) {
            $this->dispatch('stopCurrentAudio');
            $this->audioPlaying = false;
        }

        //Limpiar timers y botones
        $this->dispatch('clearTimers');
        $this->dispatch('deactivateAllButtons');
        
        // Establece la instrucción actual
        $this->currentTransitionId = $transition->id;
        $this->currentInstructionId = $transition->from_instruction_id;
        $instruction = Instruction::find($transition->from_instruction_id);
        
        // Manejar el audio si la instrucción tiene un archivo de audio
        if ($instruction && $instruction->audio_file) {
            $this->audioPlaying = true;
            $audioUrl = Storage::url($instruction->audio_file);
            $this->dispatch('initAudioPlayback', $audioUrl);
        }
        
        // Procesar la transición según el tipo de trigger
        switch ($transition->trigger) {
            //Transición basada en tiempo
            case 'time':
                Log::info('Iniciando timer:', [
                    'transitionId' => $transition->id,
                    'seconds' => $transition->time_seconds
                ]);
                
                // Inicia un timer para transiciones basadas en tiempo
                $this->dispatch('startTransitionTimer', data: [
                    'transitionId' => $transition->id,
                    'seconds' => $transition->time_seconds
                ]);
                break;
                
                // Transición que depende de la elección del usuario (botones)
            case 'user_choice':
                // Activa el botón correspondiente para interacción del usuario
                if ($transition->desa_button_id) {
                    \Log::info('Activando botón:', ['button_id' => $transition->desa_button_id]);
                    $this->dispatch('activateButton', [
                        'buttonId' => (int)$transition->desa_button_id
                    ]);
                }
                break;
                    
            // Transición tipo bucle
            case 'loop':
                if (!isset($this->loopCounts[$transition->id])) {
                    $this->loopCounts[$transition->id] = 0;
                }
                    
                Log::info('Procesando loop:', [
                    'current_count' => $this->loopCounts[$transition->id],
                    'max_count' => $transition->loop_count
                ]);
                    
                // Ejecutar bucle o transición siguiente
                if ($this->loopCounts[$transition->id] < $transition->loop_count) {
                        // Si aún no hemos completado el bucle
                    $this->loopCounts[$transition->id]++;
                        
                    // Encontrar la primera transición que parte desde la instrucción destino del loop
                    $nextTransition = Transition::where('from_instruction_id', $transition->to_instruction_id)->first();
                        
                    if ($nextTransition) {
                        $this->executeTransition($nextTransition);
                    }
                } else {
                    // El bucle ha terminado, buscar la siguiente transición desde la instrucción actual
                    Log::info('Bucle completado, buscando siguiente transición');
                    // Si el bucle está completo, busca la siguiente transición
                    $nextTransition = Transition::where('from_instruction_id', $transition->from_instruction_id)
                     ->where('trigger', '!=', 'loop')
                    ->first();
                        
                    if ($nextTransition) {
                        Log::info('Ejecutando transición después del bucle:', [
                            'from' => $nextTransition->from_instruction_id,
                            'to' => $nextTransition->to_instruction_id
                        ]);
                        $this->executeTransition($nextTransition);
                    }
                }
                break;
        }         
    } //Fin de executeTransition 
                
    // Maneja cuando se presiona un botón
    #[On('buttonPressed')]
    public function handleButtonPress($buttonId)
    {
        Log::info('Button pressed recibido:', ['buttonId' => $buttonId]);
               
        // Verifica si la simulación está activa
        if (!$this->isPlaying) {
            Log::info('Simulación no activa');
               return;
        }
               
        // Buscar la transición correspondiente al botón presionado
        $currentTransition = Transition::where('from_instruction_id', $this->currentInstructionId)
            ->where('trigger', 'user_choice')
            ->where('desa_button_id', $buttonId)
            ->first();
               
        Log::info('Buscando transición:', [
            'currentInstructionId' => $this->currentInstructionId,
            'buttonId' => $buttonId,
            'transitionFound' => $currentTransition ? 'yes' : 'no'
        ]);
               
        // Si no se encuentra la transición, salir
        if (!$currentTransition) {
            Log::info('No se encontró la transición');
               return;
        }
               
        // Limpiar estado actual
        $this->dispatch('clearTimers');
        $this->dispatch('deactivateAllButtons');
           if ($this->audioPlaying) {
               $this->dispatch('stopAudio');
           }
               
           Log::info('Ejecutando transición:', [
               'from' => $currentTransition->from_instruction_id,
               'to' => $currentTransition->to_instruction_id
           ]);
               
           // Cambiar a la instrucción destino
           $this->currentInstructionId = $currentTransition->to_instruction_id;
           $toInstruction = Instruction::find($currentTransition->to_instruction_id);
               
           // Reproducir audio si existe
           $this->audioPlaying = !empty($toInstruction->audio_file);
           if ($toInstruction && $toInstruction->audio_file) {
               $this->dispatch('playAudio', Storage::url($toInstruction->audio_file));
           }
               
           // Buscar y ejecutar la siguiente transición
           $nextTransition = Transition::where('from_instruction_id', $currentTransition->to_instruction_id)
           ->first();
               
           if ($nextTransition) {
               Log::info('Ejecutando siguiente transición:', ['id' => $nextTransition->id]);
               $this->executeTransition($nextTransition);
           }
    }
           
    // Maneja cuando termina un timer de transición
    #[On('transitionTimeout')]
    public function handleTransitionTimeout(int $transitionId)
    {
        // Verificar si la simulación está activa
        if (!$this->isPlaying) return;
        // Buscar la transición actual
        $currentTransition = Transition::find($transitionId);
        if (!$currentTransition) return;
            
        // Establecer la nueva instrucción
        $this->currentInstructionId = $currentTransition->to_instruction_id;
        $instruction = Instruction::find($currentTransition->to_instruction_id);
                
        if ($instruction) {
            // Reproducir audio si existe
            $this->audioPlaying = !empty($instruction->audio_file);
            if ($instruction->audio_file) {
                $this->dispatch('playAudio', Storage::url($instruction->audio_file));
            }
            
            // Buscar y ejecutar la siguiente transición
            $nextTransition = Transition::where('from_instruction_id', $currentTransition->to_instruction_id)
            ->first();
                
            if ($nextTransition) {
                $this->executeTransition($nextTransition);
            }
        }
    }
            
    // Maneja cuando termina un audio
    #[On('audioEnded')]
    public function handleAudioEnd()
    {
        $this->audioPlaying = false;
    }
           
    // Detiene la simulación                
    public function stopSimulation()
    {
        //Recargar la página ya que el resto no
        Log::info('Deteniendo simulación...');
           
        // Detener audio, botones y timers
        $this->dispatch('stopCurrentAudio');
        $this->dispatch('deactivateAllButtons');
        $this->dispatch('clearTimers');
           
        // Asegurarnos que todos los estados se limpian correctamente
        $this->reset([
            'isPlaying',
            'currentInstructionId', 
            'currentTransitionId',
            'loopCounts',
            'audioPlaying'
        ]);
           
        // Actualizar la vista para reflejar el estado inicial
        $this->dispatch('simulationStopped');
        Log::info('Simulación detenida completamente');
    }
           
    // Renderiza la vista principal
    public function render()
    {
        return view('livewire.scenario-simulation');
    }

}