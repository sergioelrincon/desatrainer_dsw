<?php 

namespace App\Livewire; // Definimos el espacio de nombres del componente Livewire.
use Livewire\Component; // Importamos la clase base de Livewire\Component.
use App\Models\DesaTrainer; // Importamos el modelo DesaTrainer.
use App\Models\DesaButton; // Importamos el modelo DesaButton.

class DesaTrainerShow extends Component
{
    // Propiedades públicas del componente.
    public $desaTrainer; // Instancia de DesaTrainer.
    public $showButtonForm = false; // Controla la visibilidad del formulario del botón (al crear o editar uno)
    public $buttonLabel = ''; // Etiqueta del botón.
    public $buttonArea = []; // Área del botón (puntos).
    public $buttonColor = '#007bff'; // Color por defecto del botón (azul).
    public $isBlinking = false; // Controla si el botón parpadea.
    public $editingButton = null; // Guarda el botón que se está editando.

    // Definimos los listeners para escuchar eventos de Livewire.
    protected $listeners = ['areaSelected']; 

    // Reglas de validación para los campos del formulario.
    protected $rules = [
        'buttonLabel' => 'required|string|max:255',
        'buttonArea' => 'required|array|min:3',
        'buttonColor' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        'isBlinking' => 'boolean'
    ];

    // Mensajes personalizados de validación.
    protected $messages = [
        'buttonLabel.required' => 'La etiqueta del botón es obligatoria.',
        'buttonLabel.max' => 'La etiqueta no puede tener más de 255 caracteres.',
        'buttonArea.required' => 'Debe dibujar un área para el botón.',
        'buttonArea.min' => 'El área debe tener al menos 3 puntos.',
        'buttonColor.required' => 'Debe seleccionar un color.',
        'buttonColor.regex' => 'El color debe ser un valor hexadecimal válido.',
    ];

    // Método que se ejecuta cuando se monta el componente.
    public function mount(DesaTrainer $desaTrainer)
    {
        $this->desaTrainer = $desaTrainer; // Asignamos el trainer recibido.
    }

    // Inicia la creación de un nuevo botón.
    public function startNewButton()
    {
        $this->reset(['buttonLabel', 'buttonArea', 'editingButton']); // Resetea los valores.
        $this->buttonColor = '#007bff'; // Restablece el color por defecto.
        $this->isBlinking = false; // Desactiva el parpadeo.
        $this->showButtonForm = true; // Muestra el formulario para el botón. Sergio: Esta propiedad se consulta en la vista para mostrar el formulario de creación de botones.
        $this->dispatch('startDrawing'); // Inicia el dibujo del área. Sergio: Dispara el evento startDrawing ¿?
    }

    // Método para gestionar la selección de un área.
    public function areaSelected($points)
    {
        $this->buttonArea = $points; // Asigna los puntos seleccionados al área del botón.
    }

    // Método para guardar un botón (nuevo o editado).
    public function saveButton()
    {
        $this->validate(); // Valida los datos del formulario.

        try {
            if ($this->editingButton) { // Si estamos editando un botón existente...
                $button = DesaButton::find($this->editingButton); // Buscamos el botón.
                $button->update([ // Actualizamos los datos del botón.
                    'label' => $this->buttonLabel,
                    'area' => $this->buttonArea,
                    'color' => $this->buttonColor,
                    'is_blinking' => $this->isBlinking
                ]);
            } else { // Si es un nuevo botón...
                DesaButton::create([ // Creamos un nuevo botón.
                    'desa_trainer_id' => $this->desaTrainer->id,
                    'label' => $this->buttonLabel,
                    'area' => $this->buttonArea,
                    'color' => $this->buttonColor,
                    'is_blinking' => $this->isBlinking
                ]);
            }

            // Resetea los datos y oculta el formulario.
            $this->reset(['showButtonForm', 'buttonLabel', 'buttonArea', 'editingButton', 'buttonColor', 'isBlinking']);
            $this->dispatch('resetCanvas'); // Resetea el lienzo.

            $this->desaTrainer->refresh(); // Refresca el modelo para obtener los datos actualizados.
            $this->dispatch('buttonSaved', ['buttons' => $this->desaTrainer->buttons]); // Emite evento para actualizar los botones.

            session()->flash('success', 
                $this->editingButton ? 'Botón actualizado correctamente.' : 'Botón creado correctamente.'
            ); // Mensaje de éxito.
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el botón.'); // Mensaje de error si falla.
        }
    }

    // Método para editar un botón existente.
    public function editButton($buttonId)
    {
        $button = DesaButton::findOrFail($buttonId); // Busca el botón por su ID.

        // Rellena los campos del formulario con los datos del botón a editar.
        $this->editingButton = $buttonId;
        $this->buttonLabel = $button->label;
        $this->buttonArea = $button->area;
        $this->buttonColor = $button->color;
        $this->isBlinking = $button->is_blinking;
        $this->showButtonForm = true;

        // Dispara el evento para cargar el área del botón en el lienzo.
        $this->dispatch('loadArea', [
            'area' => $button->area,
            'buttonId' => $buttonId,
            'color' => $button->color,
            'isBlinking' => $button->is_blinking,
            'editable' => true
        ]);
    }

    // Método para eliminar un botón.
    public function deleteButton($buttonId)
    {
        try {
            $button = DesaButton::findOrFail($buttonId); // Busca el botón a eliminar.
            $button->delete(); // Elimina el botón.

            $this->desaTrainer->refresh(); // Refresca el modelo para obtener los datos actualizados.
            $this->dispatch('buttonDeleted', ['buttons' => $this->desaTrainer->buttons]); // Emite evento para eliminar el botón.

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el botón.'); // Mensaje de error si falla.
        }
    }

    // Método para cancelar la creación o edición de un botón.
    public function cancelButton()
    {
        $this->reset(['showButtonForm', 'buttonLabel', 'buttonArea', 'editingButton', 'buttonColor', 'isBlinking']); // Resetea los datos.
        $this->dispatch('resetCanvas'); // Resetea el lienzo.
    }

    // Método para renderizar la vista del componente.
    public function render()
    {
        return view('livewire.desa-trainer-show'); // Devuelve la vista del componente.
    }
}
