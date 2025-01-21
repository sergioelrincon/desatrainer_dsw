<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructionRequest;
use App\Models\Instruction;
use Illuminate\Http\Request;
use App\Models\Scenario;
use Illuminate\Support\Facades\Storage;

class InstructionController extends Controller
{
    protected $allowedPerPage = [10, 25, 50, 100];

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), $this->allowedPerPage) ? $request->input('per_page') : 10;

        $sortField = $request->get('sortField', 'id');
        $sortDirection = $request->get('sortDirection', 'desc');

        $instructions = Instruction::with('scenario')->orderByField($sortField, $sortDirection)->filterByTitle($request->get('search'))->filterByScenario($request->get('scenario_id'))->filterByDuration($request->get('duration'))->paginate($perPage);

        $scenarios = Scenario::pluck('title', 'id');

        return view('admin.instructions.index', compact('instructions', 'scenarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $scenarios = Scenario::pluck('title', 'id');
        $selectedScenario = $request->get('scenario_id');
        return view('admin.instructions.create', compact('scenarios', 'selectedScenario'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InstructionRequest $request)
    {

        $validatedData = $request->validated();

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('instructions/audio', 'public');
            $validatedData['audio_file'] = $path;
        }

        Instruction::create($validatedData);

        return redirect()->route('instructions.index')->with('success', 'Instrucción creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Instruction $instruction)
    {
        return view('admin.instructions.show', compact('instruction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instruction $instruction)
    {
        $scenarios = Scenario::pluck('title', 'id');
        return view('admin.instructions.edit', compact('instruction', 'scenarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstructionRequest $request, Instruction $instruction)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('audio_file')) {
            // Eliminar archivo de audio anterior si existe
            if ($instruction->audio_file) {
                Storage::disk('public')->delete($instruction->audio_file);
            }
            $path = $request->file('audio_file')->store('instructions/audio', 'public');
            $validatedData['audio_file'] = $path;
        }

        $instruction->update($validatedData);

        return redirect()->route('instructions.index')->with('success', 'Instrucción actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instruction $instruction)
    {
        // Eliminar archivo de audio si existe
        if ($instruction->audio_file) {
            Storage::disk('public')->delete($instruction->audio_file);
        }

        $instruction->delete();

        return redirect()->route('instructions.index')->with('success', 'Instrucción eliminada exitosamente.');
    }

    public function deleteAudio(Instruction $instruction)
    {
        try {
            if ($instruction->audio_file) {
                Storage::disk('public')->delete($instruction->audio_file);
                $instruction->update(['audio_file' => null]);
                return response()->json(['success' => true, 'message' => 'Audio eliminado correctamente']);
            }
            return response()->json(['success' => false, 'message' => 'No hay audio para eliminar'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el audio'], 500);
        }
    }
}
