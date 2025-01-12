<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\ScenarioRequest; // Asegúrate de importar el request
use App\Models\DesaTrainer;


class ScenarioController extends Controller
{
    public function index(Request $request)
    {
        $scenarios = Scenario::all();
        
        /**
         * pluck extrae una lista de valores de una columna específica. En el primer argumento indicamos la columna cuyos valores queremos extraer. 
         * El segundo, la columna que se usará como clave en el array resultante. Esto generará un array asociativo donde las claves son los valores 
         * de la columna id y los valores son los valores de la columna name.
         */
        $categories = Category::pluck('name', 'id'); 
        
        return view('admin.scenarios.index', compact('scenarios', 'categories'));
    }
    /**
    * Show the form for creating a new resource.
    */
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $desaTrainers = DesaTrainer::pluck('name', 'id');
        return view('admin.scenarios.create', compact('categories', 'desaTrainers'));
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(ScenarioRequest $request)
    {        
        Scenario::create($request->all());
        return redirect()->route('scenarios.index')
        ->with('success', 'Escenario creado exitosamente.');
    }
    
    /**
    * Display the specified resource.
    */
    public function show(Scenario $scenario)
    {
        return view('admin.scenarios.show', compact('scenario'));
        
    }
    
    /**
    * Show the form for editing the specified resource.
    */
    public function edit(Scenario $scenario)
    {
        $categories = Category::pluck('name', 'id');
        $desaTrainers = Desatrainer::pluck('name', 'id');
        return view('admin.scenarios.edit', compact('scenario', 'categories', 'desaTrainers'));
    }
    
    /**
    * Update the specified resource in storage.
    */
    public function update(ScenarioRequest $request, Scenario $scenario)
    {
        $scenario->update($request->all());        
        return redirect()->route('scenarios.index', $scenario)
        ->with('success', 'Escenario actualizado exitosamente.');
    }
    
    /**
    * Remove the specified resource from storage.
    */
    public function destroy(Scenario $scenario)
    {
        $scenario->delete();
        return redirect()->route('scenarios.index')
        ->with('success', 'Escenario eliminado exitosamente.');
    }
}
