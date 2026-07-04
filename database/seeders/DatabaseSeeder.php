<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Relaticle\Flowforge\Services\DecimalPosition;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
            ],
        );

        $tasks = [
            ['todo', 'Preparar brief del proyecto', 'Definir alcance inicial, entregables y responsables.', 'high', 2],
            ['todo', 'Revisar assets de marca', 'Validar logo, colores y tipografias disponibles.', 'medium', 4],
            ['todo', 'Crear checklist de QA', 'Anotar flujos criticos para probar antes de entregar.', 'low', 8],
            ['todo', 'Configurar tablero de contenidos', 'Separar ideas, guiones, edicion y publicacion.', 'medium', 10],
            ['in_progress', 'Disenar pantalla principal', 'Ajustar layout del primer viewport y navegacion.', 'urgent', 1],
            ['in_progress', 'Conectar formulario de contacto', 'Validar campos y preparar correo de prueba.', 'high', 3],
            ['in_progress', 'Optimizar version mobile', 'Revisar espaciados y botones pequenos.', 'medium', 5],
            ['review', 'Validar copy del hero', 'Revisar tono, claridad y llamada a la accion.', 'medium', 2],
            ['review', 'Prueba de arrastrar tarjetas', 'Mover tareas entre columnas y confirmar posiciones.', 'high', 3],
            ['review', 'Revisar accesibilidad', 'Contraste, labels y estados focus visibles.', 'medium', 6],
            ['completed', 'Instalar Laravel en Docker', 'Proyecto base funcionando en contenedores.', 'high', -2],
            ['completed', 'Instalar Filament 5', 'Panel administrativo disponible en /admin.', 'high', -1],
            ['completed', 'Instalar Flowforge', 'Plugin kanban agregado al proyecto.', 'high', 0],
        ];

        $positions = [];

        foreach ($tasks as [$status, $title, $description, $priority, $dueInDays]) {
            $positions[$status] ??= 0;
            $positions[$status]++;

            Task::query()->updateOrCreate(
                ['title' => $title],
                [
                    'description' => $description,
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => Carbon::today()->addDays($dueInDays),
                    'position' => DecimalPosition::generateSequence($positions[$status])[$positions[$status] - 1],
                ],
            );
        }
    }
}
