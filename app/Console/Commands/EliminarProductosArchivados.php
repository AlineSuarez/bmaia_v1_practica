<?php

namespace App\Console\Commands;

use App\Models\Inventory;
use Illuminate\Console\Command;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductosArchivadosEliminados;

class EliminarProductosArchivados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:eliminar-productos-archivados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina productos archivados hace más de 30 días';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $productosAgrupados = Inventory::where('archivada', true)
        // ->where('fecha_archivado', '<', now()->subDays(30))
        // ->get()
        // ->groupBy('user_id');

        // $totalEliminados = 0;

        // foreach ($productosAgrupados as $userId => $productos) {
        //     $user = User::find($userId);

        //     if ($user && $productos->count()) {
        //         // Enviar correo al usuario
        //         Mail::to($user->email)->send(new ProductosArchivadosEliminados($productos));

        //         // Eliminar productos
        //         foreach ($productos as $producto) {
        //             $producto->delete();
        //             $totalEliminados++;
        //         }
        //     }
        // }

        // $this->info("Se eliminaron {$totalEliminados} productos archivados.");

        //Eliminar productos con 30 dias de archivado
        $eliminados = Inventory::where('archivada', true)
            ->where('fecha_archivado', '<', now()->subDays(30) )
            ->delete();

        $this->info("Se eliminaron {$eliminados} productos archivados.");
    }
}
