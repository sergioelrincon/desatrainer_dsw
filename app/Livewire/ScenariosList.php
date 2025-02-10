<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scenario;
use App\Models\DesaTrainer;
use Livewire\WithPagination; 

class ScenariosList extends Component
{
   // Usamos WithPagination para habilitar la paginación en Livewire
   use WithPagination;

   // Propiedades públicas que serán vinculadas con la vista
   public $categoryId; // ID de la categoría seleccionada
    public $categoryName; // Nombre de la categoría seleccionada
   public $search = ''; // Término de búsqueda
   public $desaTrainerId = ''; // ID del DESA Trainer seleccionado

   /**
    * Se ejecuta cuando el componente es montado
    * Recibe el categoryId como parámetro y lo asigna a la propiedad
    */
   public function mount($categoryId)
   {
       $this->categoryId = $categoryId;
       $this->categoryName = \App\Models\Category::find($categoryId)->name;
   }

   /**
    * Define qué propiedades se sincronizarán con la URL
    * El 'except' => '' evita que se muestre el parámetro en la URL cuando está vacío
    */
   protected $queryString = [
       'search' => ['except' => ''],
       'desaTrainerId' => ['except' => '']
   ];

   /**
    * Se ejecuta cuando la propiedad search está siendo actualizada
    * Resetea la paginación para mostrar los resultados desde la primera página
    */
   public function updatingSearch()
   {
       $this->resetPage();
   }

   /**
    * Resetea los filtros a sus valores por defecto
    * También resetea la paginación
    */
   public function resetFilters()
   {
       $this->reset(['search', 'desaTrainerId']);
       $this->resetPage();
   }

   /**
    * Renderiza el componente
    * Construye la consulta con los filtros aplicados
    * Retorna la vista con los datos necesarios
    */
   public function render()
   {
       // Iniciamos la consulta base
       $scenarios = Scenario::query()
           // Filtramos por la categoría seleccionada
           ->where('category_id', $this->categoryId)
           // Cargamos las relaciones necesarias
           ->with(['category', 'desaTrainer', 'instructions'])
           // Si hay término de búsqueda, filtramos por título o descripción
           ->when($this->search, function($query) {
               return $query->where(function($q) {
                   $q->where('title', 'LIKE', '%' . $this->search . '%')
                     ->orWhere('description', 'LIKE', '%' . $this->search . '%');
               });
           })
           // Si hay DESA Trainer seleccionado, filtramos por él
           ->when($this->desaTrainerId, function($query) {
               return $query->where('desa_trainer_id', $this->desaTrainerId);
           })
           // Ordenamos los resultados por título
           ->orderBy('title')
           // Paginamos los resultados, 6 por página
           ->paginate(6);

       // Obtenemos todos los DESA Trainers para el selector
       $desaTrainers = DesaTrainer::all();

       // Retornamos la vista con los datos necesarios
       return view('livewire.scenarios-list', [
           'scenarios' => $scenarios,
           'desaTrainers' => $desaTrainers,
       ]);
   }
}