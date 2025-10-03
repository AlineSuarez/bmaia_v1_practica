<?php 
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI;

class AIController extends Controller
{
    public function ask(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string',
        ]);

        // Crear el cliente de OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $response = $client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente experto en apicultura chilena.'],
                ['role' => 'user', 'content' => $validated['question']],
            ],
        ]);

        return response()->json([
            'answer' => $response['choices'][0]['message']['content'],
        ]);
    }
}
