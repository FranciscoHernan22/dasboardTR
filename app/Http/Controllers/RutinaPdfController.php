<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

class RutinaPdfController extends Controller
{
    public function generar(User $cliente, $semana, $dia)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $bloques = Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->groupBy('grupo');

        if ($bloques->isEmpty()) {
            return back()->with('error', 'No hay rutina para este día.');
        }

        $ejerciciosDB = Ejercicio::all()->keyBy('id');
        $letras       = ['A', 'B', 'C', 'D'];
        $bloquesData  = [];

        foreach ($bloques as $grupo => $rutinasGrupo) {
            $tipo       = $rutinasGrupo->first()->tipo;
            $ejercicios = [];

            foreach ($rutinasGrupo as $i => $rutina) {
                $series = $rutina->series ?? [];
                if (is_string($series)) {
                    $series = json_decode($series, true) ?? [];
                }

                $ejercicioDB = $ejerciciosDB->get($rutina->ejercicio_id);
                $imagen      = $ejercicioDB->imagen ?? null;

                $ejercicios[] = [
                    'letra'  => $letras[$i] ?? chr(65 + $i),
                    'nombre' => $rutina->nombre,
                    'imagen' => $imagen,
                    'series' => $series,
                ];
            }

            $bloquesData[] = [
                'tipo'       => strtoupper($tipo),
                'ejercicios' => $ejercicios,
            ];
        }

        $jsonData = json_encode([
            'cliente' => $cliente->name,
            'semana'  => $semana,
            'dia'     => $dia,
            'bloques' => $bloquesData,
        ], JSON_UNESCAPED_UNICODE);

        // Ruta absoluta al python3 de Homebrew
        $python      = '/opt/homebrew/bin/python3';
        $scriptPath  = base_path('scripts/generar_rutina_pdf.py');
        $storagePath = storage_path('app/public');
        $pdfPath     = storage_path("app/rutinas/rutina_{$cliente->id}_s{$semana}_d{$dia}.pdf");

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        $process = new Process([
            $python, $scriptPath,
            '--data',    $jsonData,
            '--output',  $pdfPath,
            '--storage', $storagePath,
        ]);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($pdfPath)) {
            \Log::error('PDF stderr: ' . $process->getErrorOutput());
            \Log::error('PDF stdout: ' . $process->getOutput());
            abort(500, 'Error generando PDF: ' . $process->getErrorOutput());
        }

        $filename = "Rutina_{$cliente->name}_S{$semana}_D{$dia}.pdf";

        return response()->download($pdfPath, $filename, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])->deleteFileAfterSend(false);
    }
}
