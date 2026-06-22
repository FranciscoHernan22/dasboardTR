<?php
// DESTINO: app/Console/Commands/FixRutinaEjerciciosClonados.php
//
// Comando de UN SOLO USO: re-apunta las rutinas ya creadas para que
// referencien el ejercicio CLONADO de cada entrenador (en vez del
// ejercicio "default" original, que ya no es visible para ellos).
// Después de correrlo una vez, lo puedes borrar si quieres.

namespace App\Console\Commands;

use App\Models\Entrenador;
use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\User;
use Illuminate\Console\Command;

class FixRutinaEjerciciosClonados extends Command
{
    protected $signature = 'app:fix-rutina-ejercicios';
    protected $description = 'Re-apunta las rutinas existentes al catálogo de ejercicios clonado de cada entrenador';

    public function handle(): int
    {
        $defaults = Ejercicio::whereNull('entrenador_id')->get();

        if ($defaults->isEmpty()) {
            $this->info('No hay catálogo default (entrenador_id NULL). Nada que hacer.');
            return self::SUCCESS;
        }

        $entrenadores = Entrenador::where('ejercicios_default_clonados', true)->get();

        if ($entrenadores->isEmpty()) {
            $this->info('Ningún entrenador tiene ejercicios clonados todavía. Nada que hacer.');
            return self::SUCCESS;
        }

        foreach ($entrenadores as $entrenador) {
            $this->info("Procesando entrenador #{$entrenador->id} ({$entrenador->nombre})...");

            $propios = Ejercicio::where('entrenador_id', $entrenador->id)->get();

            // Mapear: id del default original => id del clon de este entrenador
            // (se relacionan por nombre + segmento, que es lo único confiable
            // entre el original y su copia)
            $mapaIds = [];
            foreach ($defaults as $default) {
                $clon = $propios->first(fn ($e) => $e->nombre === $default->nombre && $e->segmento === $default->segmento);
                if ($clon) {
                    $mapaIds[$default->id] = $clon->id;
                }
            }

            if (empty($mapaIds)) {
                $this->warn('  No se encontró ningún match para este entrenador, se omite.');
                continue;
            }

            $clienteIds = User::where('entrenador_id', $entrenador->id)->pluck('id');

            $actualizadas = 0;
            foreach ($mapaIds as $idViejo => $idNuevo) {
                $actualizadas += Rutina::whereIn('user_id', $clienteIds)
                    ->where('ejercicio_id', $idViejo)
                    ->update(['ejercicio_id' => $idNuevo]);
            }

            $this->info("  {$actualizadas} rutinas actualizadas.");
        }

        $this->info('Listo.');
        return self::SUCCESS;
    }
}