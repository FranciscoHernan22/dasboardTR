<?php
// DESTINO: app/Models/Ejercicio.php (reemplaza el actual)
// Único cambio real: usa App\Models\Entrenador en vez de App\Models\User
// para todo lo relacionado con el dueño del ejercicio.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ejercicio extends Model
{
    protected $fillable = [
        'entrenador_id',
        'nombre',
        'segmento',
        'imagen',
    ];

    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class, 'entrenador_id');
    }

    /**
     * La primera vez que un entrenador entra a su sección de Ejercicios
     * (o al editor de rutinas), le clonamos el catálogo "default"
     * (los ejercicios con entrenador_id = NULL, tu catálogo original)
     * como ejercicios propios, para que los pueda editar/eliminar
     * libremente sin afectar a otros entrenadores ni al catálogo original.
     *
     * Se ejecuta UNA SOLA VEZ por entrenador (bandera en la tabla entrenadores).
     */
    public static function asegurarDefaultsPara(int $entrenadorId): void
    {
        $entrenador = Entrenador::find($entrenadorId);

        if (!$entrenador || $entrenador->ejercicios_default_clonados) {
            return;
        }

        $defaults = static::whereNull('entrenador_id')->get();

        foreach ($defaults as $default) {
            static::create([
                'entrenador_id' => $entrenadorId,
                'nombre'        => $default->nombre,
                'segmento'      => $default->segmento,
                'imagen'        => $default->imagen, // reutiliza el mismo archivo físico, no lo duplica
            ]);
        }

        $entrenador->ejercicios_default_clonados = true;
        $entrenador->save();
    }

    /**
     * True si OTRO ejercicio (de cualquier entrenador) sigue usando
     * el mismo archivo de imagen. Sirve para no borrar del disco una
     * imagen que otro entrenador todavía necesita (por el clonado).
     */
    public static function archivoEnUsoPorOtros(string $path, ?int $exceptId = null): bool
    {
        return static::where('imagen', $path)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }

    protected static function booted(): void
    {
        static::deleting(function (Ejercicio $ejercicio) {
            if ($ejercicio->imagen && !static::archivoEnUsoPorOtros($ejercicio->imagen, $ejercicio->id)) {
                Storage::disk('public')->delete($ejercicio->imagen);
            }
        });
    }
}