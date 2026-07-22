<?php
// DESTINO: app/Http/Controllers/EntrenadorEjercicioController.php
namespace App\Http\Controllers;

use App\Models\Ejercicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
 
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use Intervention\Image\Encoders\WebpEncoder;


class EntrenadorEjercicioController extends Controller
{
    public const SEGMENTOS = [
        'ABDOMEN'        => 'Abdomen',
        'ANTEBRAZO'      => 'Antebrazo',
        'BICEPS'         => 'Bíceps',
        'TRICEPS'        => 'Tríceps',
        'CUADRICEPS'     => 'Cuádriceps',
        'DELTOIDES'      => 'Deltoides',
        'ESPALDA'        => 'Espalda',
        'ISQUIOS/GLUTEO' => 'Isquiotibiales y glúteos',
        'PECTORAL_MAYOR' => 'Pectoral',
        'TRICEPS_SURAL'  => 'Tríceps sural',
    ];

    // Reglas de validación del video, reutilizadas en store() y update()
    private const REGLAS_VIDEO = 'nullable|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:51200';
    private const MENSAJES_VIDEO = [
        'video.file'       => 'El video no se pudo leer, intenta subirlo de nuevo.',
        'video.mimetypes'  => 'El video debe ser MP4, MOV, WEBM o AVI.',
        'video.max'        => 'El video no puede pesar más de 50MB.',
    ];

    // ─── Comprime y sube la imagen a R2, devuelve la ruta guardada ───
    private function procesarImagen($archivo): string
    {
        $manager = new ImageManager(Driver::class);
        $encoded = $manager->decode($archivo->getRealPath())
            ->scaleDown(width: 800, height: 800)
            ->encode(new WebpEncoder(quality: 92));

        $ruta = 'ejercicios/' . uniqid() . '.webp';
        Storage::disk('r2')->put($ruta, (string) $encoded);

        return $ruta;
    }

    // ─── Sube el video (ya recortado desde el navegador) a R2, devuelve la ruta guardada ───
    private function procesarVideo($archivo): string
    {
        $extension = strtolower($archivo->getClientOriginalExtension()) ?: 'mp4';
        $nombre    = uniqid() . '.' . $extension;

        Storage::disk('r2')->putFileAs('ejercicios/videos', $archivo, $nombre);

        return 'ejercicios/videos/' . $nombre;
    }

    public function index()
    {
        $entrenadorId = Auth::id();

        Ejercicio::asegurarDefaultsPara($entrenadorId);

        $ejercicios = Ejercicio::where('entrenador_id', $entrenadorId)
            ->orderBy('nombre')
            ->get();

        $porSegmento = collect(array_keys(self::SEGMENTOS))
            ->mapWithKeys(fn ($seg) => [$seg => $ejercicios->where('segmento', $seg)->values()])
            ->filter(fn ($items) => $items->isNotEmpty());

        $segmentosFijos  = self::SEGMENTOS;
        $totalEjercicios = $ejercicios->count();

        return view('ejercicios.index', compact('porSegmento', 'segmentosFijos', 'totalEjercicios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => 'required|string|max:120',
            'segmento' => ['required', Rule::in(array_keys(self::SEGMENTOS))],
            'imagen'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:15360',
            'video'    => self::REGLAS_VIDEO,
         ], [
            'nombre.required'   => 'Ponle un nombre al ejercicio.',
            'segmento.required' => 'Selecciona un segmento.',
            'segmento.in'       => 'Selecciona un segmento válido de la lista.',
            'imagen.image'      => 'El archivo debe ser una imagen.',
            'imagen.max'        => 'La imagen no puede pesar más de 4MB.',
            ...self::MENSAJES_VIDEO,
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        if ($request->hasFile('video')) {
            $data['video'] = $this->procesarVideo($request->file('video'));
        }

        $data['entrenador_id'] = Auth::id();
        Ejercicio::create($data);

        return back()->with('success', 'Ejercicio agregado correctamente');
    }

    public function update(Request $request, Ejercicio $ejercicio)
    {
        if ($ejercicio->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'nombre'   => 'required|string|max:120',
            'segmento' => ['required', Rule::in(array_keys(self::SEGMENTOS))],
            'imagen'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:15360',
            'video'    => self::REGLAS_VIDEO,
        ], [
            'nombre.required'   => 'Ponle un nombre al ejercicio.',
            'segmento.required' => 'Selecciona un segmento.',
            'segmento.in'       => 'Selecciona un segmento válido de la lista.',
            'imagen.image'      => 'El archivo debe ser una imagen.',
            'imagen.max'        => 'La imagen no puede pesar más de 15MB.',
            ...self::MENSAJES_VIDEO,
        ]);

        if ($request->hasFile('imagen')) {
            $imagenAnterior = $ejercicio->imagen;
            $data['imagen'] = $this->procesarImagen($request->file('imagen'));

            // Borra la imagen anterior de R2 si nadie más la usa
            if ($imagenAnterior && !Ejercicio::archivoEnUsoPorOtros($imagenAnterior, $ejercicio->id)) {
                Storage::disk('r2')->delete($imagenAnterior);
            }
        }

        if ($request->hasFile('video')) {
            $videoAnterior = $ejercicio->video;
            $data['video'] = $this->procesarVideo($request->file('video'));

            // Borra el video anterior de R2 si nadie más lo usa (ej. ejercicios default clonados)
            if ($videoAnterior && !Ejercicio::archivoEnUsoPorOtros($videoAnterior, $ejercicio->id, 'video')) {
                Storage::disk('r2')->delete($videoAnterior);
            }
        }

        $ejercicio->update($data);

        return back()->with('success', 'Ejercicio actualizado correctamente');
    }

    public function destroy(Ejercicio $ejercicio)
    {
        if ($ejercicio->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $ejercicio->delete();

        return back()->with('success', 'Ejercicio eliminado');
    }
}