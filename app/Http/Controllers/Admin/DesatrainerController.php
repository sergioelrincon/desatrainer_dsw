<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DesaTrainerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DesaTrainer;

class DesatrainerController extends Controller
{
    public function index(Request $request)
    {
        $trainers = DesaTrainer::all();

        return view('admin.desa-trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('admin.desa-trainers.create');
    }

    public function store(DesaTrainerRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('desa-trainers', 'public');
            $validatedData['image'] = $path;
        }

        DesaTrainer::create($validatedData);

        return redirect()
            ->route('desa-trainers.index')
            ->with('success', 'DESA Trainer creado exitosamente.');
    }

    public function show(DesaTrainer $desaTrainer)
    {
        return view('admin.desa-trainers.show', compact('desaTrainer'));
    }

    public function edit(DesaTrainer $desaTrainer)
    {
        return view('admin.desa-trainers.edit', compact('desaTrainer'));
    }

    public function update(DesaTrainerRequest $request, DesaTrainer $desaTrainer)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior
            if ($desaTrainer->image) {
                Storage::disk('public')->delete($desaTrainer->image);
            }
            $path = $request->file('image')->store('desa-trainers', 'public');
            $validatedData['image'] = $path;
        }

        $desaTrainer->update($validatedData);

        return redirect()
            ->route('desa-trainers.index')
            ->with('success', 'DESA Trainer actualizado exitosamente.');
    }

    public function destroy(DesaTrainer $desaTrainer)
    {
        try {
            if ($desaTrainer->image) {
                Storage::disk('public')->delete($desaTrainer->image);
            }
            
            $desaTrainer->delete();
            return redirect()
                ->route('desa-trainers.index')
                ->with('success', 'DESA Trainer eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()
                ->route('desa-trainers.index')
                ->with('error', 'Error al eliminar el DESA Trainer.');
        }
    }
}