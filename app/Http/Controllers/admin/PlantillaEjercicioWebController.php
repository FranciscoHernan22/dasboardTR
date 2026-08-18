<?php
// DESTINO: app/Http/Controllers/Admin/PlantillaEjercicioWebController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EntrenadorEjercicioController;
use App\Models\Ejercicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class PlantillaEjercicioWebController extends Controller
{
    // Misma lista de segmentos que usan los entrenadores — importante que
    // coincida exactamente, porque estos ejercicios se clonan a sus cuentas.
    public const SEGMENTOS = EntrenadorEjercicioController::SEGMENTOS;

    private const REGLAS_VIDEO = 'nullable|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo|max:51200';
    private const MENSAJES_VIDEO = [
        'video.file'      => 'El video no se pudo leer, intenta subirlo de nuevo.',
        'video.mimetypes' => 'El video debe ser MP4, MOV, WEBM o AVI.',
        'video.max'       => 'El video no puede pesar más de 50MB.',
    ];

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

    private function procesarVideo($archivo): string
    {
        $extension = strtolower($archivo->getClientOriginalExtension()) ?: 'mp4';
        $nombre    = uniqid() . '.' . $extension;

        Storage::disk('r2')->putFileAs('ejercicios/videos', $archivo, $nombre);

        return 'ejercicios/videos/' . $nombre;
    }

    public function index()
    {
        $ejercicios = Ejercicio::whereNull('entrenador_id')
            ->orderBy('nombre')
            ->get();

        $porSegmento = collect(array_keys(self::SEGMENTOS))
            ->mapWithKeys(fn ($seg) => [$seg => $ejercicios->where('segmento', $seg)->values()])
            ->filter(fn ($items) => $items->isNotEmpty());

        $segmentosFijos  = self::SEGMENTOS;
        $totalEjercicios = $ejercicios->count();

        return view('admin.plantilla.index', compact('porSegmento', 'segmentosFijos', 'totalEjercicios'));
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
            'imagen.max'        => 'La imagen no puede pesar más de 15MB.',
            ...self::MENSAJES_VIDEO,
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->procesarImagen($request->file('imagen'));
        }

        if ($request->hasFile('video')) {
            $data['video'] = $this->procesarVideo($request->file('video'));
        }

        $data['entrenador_id'] = null; // siempre plantilla

        Ejercicio::create($data);

        return back()->with('success', 'Ejercicio agregado a la plantilla.');
    }

    public function update(Request $request, Ejercicio $ejercicio)
    {
        $this->verificarEsPlantilla($ejercicio);

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

            if ($imagenAnterior && !Ejercicio::archivoEnUsoPorOtros($imagenAnterior, $ejercicio->id)) {
                Storage::disk('r2')->delete($imagenAnterior);
            }
        }

        if ($request->hasFile('video')) {
            $videoAnterior = $ejercicio->video;
            $data['video'] = $this->procesarVideo($request->file('video'));

            if ($videoAnterior && !Ejercicio::archivoEnUsoPorOtros($videoAnterior, $ejercicio->id, 'video')) {
                Storage::disk('r2')->delete($videoAnterior);
            }
        }

        $ejercicio->update($data);

        return back()->with('success', 'Ejercicio de plantilla actualizado.');
    }

    public function destroy(Ejercicio $ejercicio)
    {
        $this->verificarEsPlantilla($ejercicio);

        $ejercicio->delete();

        return back()->with('success', 'Ejercicio eliminado de la plantilla.');
    }

    public function subirVideoTemporal(Request $request)
    {
        $request->validate(['video' => self::REGLAS_VIDEO], self::MENSAJES_VIDEO);

        if (!$request->hasFile('video')) {
            return response()->json(['ok' => false, 'message' => 'No se recibió el video.'], 422);
        }

        return response()->json([
            'ok'   => true,
            'path' => $this->procesarVideo($request->file('video')),
        ]);
    }

    // Nunca operar sobre un ejercicio que ya pertenece a un entrenador
    private function verificarEsPlantilla(Ejercicio $ejercicio): void
    {
        abort_if(
            ! is_null($ejercicio->entrenador_id),
            422,
            'Este ejercicio no pertenece a la plantilla.'
        );
    }
}