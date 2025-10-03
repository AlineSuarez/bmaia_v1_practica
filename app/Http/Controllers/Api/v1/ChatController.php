<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $r) {
        // retorna lista de sesiones del usuario
        return response()->json([]); // TODO implementar
    }

    public function store(Request $r) {
        // crea sesión
        return response()->json(['id' => 123, 'title' => $r->input('title') ?? 'Nueva sesión']);
    }

    public function messages(Request $r, $sessionId) {
        // retorna mensajes
        return response()->json([
            ['id'=>1,'role'=>'user','content'=>'Hola'],
            ['id'=>2,'role'=>'assistant','content'=>'¡Hola! ¿En qué ayudo?'],
        ]);
    }

    public function send(Request $r, $sessionId) {
        $content = $r->input('content');
        // guarda y genera respuesta (puede llamar a tu motor IA)
        return response()->json(['id'=>3,'role'=>'assistant','content'=>"Respuesta a: $content"]);
    }
}
