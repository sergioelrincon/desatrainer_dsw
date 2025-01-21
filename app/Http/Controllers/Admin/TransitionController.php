<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transition;
use App\Models\Instruction;
use Illuminate\Http\Request;
use App\Models\Scenario;
use App\Models\DesaButton;


class TransitionController extends Controller
{
    protected $allowedPerPage = [10, 25, 50, 100];

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), $this->allowedPerPage) ? $request->input('per_page') : 10;

        $sortField = $request->get('sortField', 'id');
        $sortDirection = $request->get('sortDirection', 'desc');

        $transitions = Transition::with(['fromInstruction', 'toInstruction'])
            ->orderByField($sortField, $sortDirection)
            ->filterByFromInstruction($request->get('from_instruction_id'))
            ->filterByToInstruction($request->get('to_instruction_id'))
            ->filterByTrigger($request->get('trigger'))
            ->filterByScenario($request->input('scenario_id'))
            ->paginate($perPage);

        $instructions = Instruction::pluck('title', 'id');
        $triggerTypes = Transition::TRIGGER_TYPES;
        $scenarios = Scenario::pluck('title', 'id');


        return view('admin.transitions.index', compact('transitions', 'instructions', 'triggerTypes', 'scenarios'));
    }

    public function create(Request $request)
    {
        $selectedInstruction = $request->get('from_instruction_id');

        $instructions = Instruction::pluck('title', 'id');
        $triggerTypes = Transition::TRIGGER_TYPES;
        $desaButtons = DesaButton::pluck('label', 'id');

        $desaButtons = collect();
    
        if ($selectedInstruction) {
            $instruction = Instruction::with('scenario.desaTrainer.buttons')->find($selectedInstruction);
            if ($instruction && $instruction->scenario->desaTrainer) {
                $desaButtons = $instruction->scenario->desaTrainer->buttons->pluck('label', 'id');
            }
            //add instructions related with scenario
            $instructions = Instruction::where('scenario_id', $instruction->scenario_id)->pluck('title', 'id');
        }

        return view('admin.transitions.create', compact('instructions', 'triggerTypes',  'selectedInstruction', 'desaButtons'));
    }
    
    public function edit(Transition $transition)
    {
        $scenario = $transition->fromInstruction->scenario;
    
        // Obtener solo las instrucciones del mismo escenario
        $instructions = Instruction::where('scenario_id', $scenario->id)
            ->pluck('title', 'id');
        $triggerTypes = Transition::TRIGGER_TYPES;
        $desaButtons = collect();
        if ($scenario->desaTrainer) {
            $desaButtons = $scenario->desaTrainer->buttons->pluck('label', 'id');
        }
        
        return view('admin.transitions.edit', compact('transition', 'instructions', 'triggerTypes', 'desaButtons'));
    }


    public function store(Request $request)
    {
        $baseValidation = [
            'from_instruction_id' => [
                'required',
                'exists:instructions,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === $request->to_instruction_id) {
                        $fail('La instrucción de origen no puede ser igual a la instrucción de destino.');
                    }
                },
            ],
            'to_instruction_id' => 'required|exists:instructions,id',
            'trigger' => 'required|in:time,user_choice,loop',
        ];
    
        // Validaciones adicionales según el tipo de trigger
        $triggerValidations = [];
        switch ($request->trigger) {
            case 'time':
                $triggerValidations['time_seconds'] = 'required|integer|min:1';
                break;
            case 'user_choice':
                $triggerValidations['desa_button_id'] = 'required|exists:desa_buttons,id';
                break;
            case 'loop':
                $triggerValidations['loop_count'] = 'required|integer|min:1';
                break;
        }
    
        $validationRules = array_merge($baseValidation, $triggerValidations);
    
        $customMessages = [
            'from_instruction_id.required' => 'La instrucción de origen es obligatoria.',
            'from_instruction_id.exists' => 'La instrucción de origen seleccionada no existe.',
            'to_instruction_id.required' => 'La instrucción de destino es obligatoria.',
            'to_instruction_id.exists' => 'La instrucción de destino seleccionada no existe.',
            'trigger.required' => 'El tipo de disparador es obligatorio.',
            'trigger.in' => 'El tipo de disparador seleccionado no es válido.',
            'time_seconds.required' => 'Debe especificar el tiempo en segundos.',
            'time_seconds.integer' => 'El tiempo debe ser un número entero.',
            'time_seconds.min' => 'El tiempo debe ser mayor a 0.',
            'desa_button_id.required' => 'Debe seleccionar un botón del DESA.',
            'desa_button_id.exists' => 'El botón seleccionado no existe.',
            'loop_count.required' => 'Debe especificar el número de repeticiones.',
            'loop_count.integer' => 'El número de repeticiones debe ser un número entero.',
            'loop_count.min' => 'El número de repeticiones debe ser mayor a 0.',
        ];
    
        $request->validate($validationRules, $customMessages);
    
        try {
            // Verificar si ya existe una transición igual
            $existingTransition = Transition::where('from_instruction_id', $request->from_instruction_id)
                ->where('to_instruction_id', $request->to_instruction_id)
                ->where('trigger', $request->trigger)
                ->first();
    
            if ($existingTransition) {
                return redirect()
                    ->route('transitions.create')
                    ->withInput()
                    ->with('error', 'Ya existe una transición con la misma instrucción de origen, destino y disparador.');
            }
    
            // Preparar los datos según el tipo de trigger
            $transitionData = $request->only([
                'from_instruction_id', 
                'to_instruction_id', 
                'trigger', 
            ]);
    
            switch ($request->trigger) {
                case 'time':
                    $transitionData['time_seconds'] = $request->time_seconds;
                    break;
                case 'user_choice':
                    $transitionData['desa_button_id'] = $request->desa_button_id;
                    break;
                case 'loop':
                    $transitionData['loop_count'] = $request->loop_count;
                    break;
            }
    
            // Crear la transición
            Transition::create($transitionData);
    
            return redirect()
                ->route('transitions.index')
                ->with('success', 'Transición creada exitosamente.');
    
        } catch (\Exception $e) {
            return redirect()
                ->route('transitions.create')
                ->withInput()
                ->with('error', 'Error al crear la transición. Por favor, inténtelo de nuevo.');
        }
    }
    
    public function update(Request $request, Transition $transition)
    {
        $baseValidation = [
            'from_instruction_id' => [
                'required',
                'exists:instructions,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value === $request->to_instruction_id) {
                        $fail('La instrucción de origen no puede ser igual a la instrucción de destino.');
                    }
                    
                    // Verificar que las instrucciones pertenezcan al mismo escenario
                    $fromInstruction = Instruction::find($value);
                    $toInstruction = Instruction::find($request->to_instruction_id);
                    
                    if ($fromInstruction && $toInstruction && 
                        $fromInstruction->scenario_id !== $toInstruction->scenario_id) {
                        $fail('Las instrucciones deben pertenecer al mismo escenario.');
                    }
                },
            ],
            'to_instruction_id' => 'required|exists:instructions,id',
            'trigger' => 'required|in:' . implode(',', array_keys(Transition::TRIGGER_TYPES))
        ];
    
        // Validaciones adicionales según el tipo de trigger
        $triggerValidations = [];
        switch ($request->trigger) {
            case 'time':
                $triggerValidations['time_seconds'] = 'required|integer|min:1';
                break;
            case 'user_choice':
                $triggerValidations['desa_button_id'] = 'required|exists:desa_buttons,id';
                break;
            case 'loop':
                $triggerValidations['loop_count'] = 'required|integer|min:1';
                break;
        }
    
        $validationRules = array_merge($baseValidation, $triggerValidations);
    
        $request->validate($validationRules);
    
        try {
            // Verificar si ya existe una transición igual (excluyendo la actual)
            $existingTransition = Transition::where('from_instruction_id', $request->from_instruction_id)
                ->where('to_instruction_id', $request->to_instruction_id)
                ->where('trigger', $request->trigger)
                ->where('id', '!=', $transition->id)
                ->first();
    
            if ($existingTransition) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Ya existe una transición con la misma instrucción de origen, destino y disparador.');
            }
    
            // Preparar los datos según el tipo de trigger
            $transitionData = $request->only([
                'from_instruction_id', 
                'to_instruction_id', 
                'trigger'
            ]);
    
            // Resetear todos los campos específicos de trigger
            $transitionData['time_seconds'] = null;
            $transitionData['desa_button_id'] = null;
            $transitionData['loop_count'] = null;
    
            // Establecer el campo específico según el trigger seleccionado
            switch ($request->trigger) {
                case 'time':
                    $transitionData['time_seconds'] = $request->time_seconds;
                    break;
                case 'user_choice':
                    $transitionData['desa_button_id'] = $request->desa_button_id;
                    break;
                case 'loop':
                    $transitionData['loop_count'] = $request->loop_count;
                    break;
            }
    
            $transition->update($transitionData);
    
            return redirect()
                ->route('transitions.index')
                ->with('success', 'Transición actualizada exitosamente.');
    
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la transición. Por favor, inténtelo de nuevo.');
        }
    }


public function destroy(Transition $transition)
{
    try {
        $transition->delete();

        return redirect()->route('transitions.index')
            ->with('success', 'La transición se ha eliminado correctamente.');
    } catch (\Exception $e) {
        return redirect()->route('transitions.index')
            ->with('error', 'Ocurrió un error al eliminar la transición. Por favor, inténtelo de nuevo.');
    }
}

       
}