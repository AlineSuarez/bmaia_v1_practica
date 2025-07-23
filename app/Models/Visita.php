<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'apiario_id',
        'user_id',
        'colmena_id', 
        'fecha_visita',
        'vigor_de_colmena',
        'actividad_colmena',
        'ingreso_pollen',
        'bloqueo_camara_cria',
        'presencia_celdas_reales',
        'postura_de_reina',
        'estado_de_cria',
        'postura_zanganos',
        'reserva_alimento',
        'presencia_varroa',
        'observaciones',
        'tipo_visita',
        'num_colmenas_totales',
        'num_colmenas_activas',
        'num_colmenas_muertas',
        'num_colmenas_inspeccionadas',
        'num_colmenas_enfermas',
        'flujo_nectar_polen',
        'nombre_revisor_apiario',
        'sospecha_enfermedad',
        'observacion_primera_visita',
        'num_colmenas_tratadas',
        'motivo_tratamiento', 
        //'nombre_comercial_medicamento', 
        //'principio_activo_medicamento', 
        //'periodo_resguardo', 
        'responsable', 
        'nombres', 
        'apellidos', 
        'rut', 
        'motivo',
        'telefono',
        'firma',
        // reconstruccion de uso de medicamentos del cuaderno de campo
        'presencia_varroa_id',
        'presencia_nosemosis_id',
        'estado_nutricional_id',
    ];

    protected $casts = [
        'fecha_visita' => 'datetime', 
    ];

    public function toPrompt()
    {
        return "Informe Apícola:
            - Desarrollo cámara cría: " . optional($this->desarrolloCria)->vigor_colmena . ", actividad: " . optional($this->desarrolloCria)->actividad_abejas . "
            - Calidad de la reina: " . optional($this->calidadReina)->postura_reina . ", cría: " . optional($this->calidadReina)->estado_cria . "
            - Estado nutricional: " . optional($this->estadoNutricional)->reserva_miel_polen . ", tipo alimentación: " . optional($this->estadoNutricional)->tipo_alimentacion . "
            - Nivel de varroa: " . optional($this->presenciaVarroa)->diagnostico_visual . ", tratamiento: " . optional($this->presenciaVarroa)->tratamiento . "
            - Nosemosis: " . optional($this->presenciaNosemosis)->signos_clinicos . "
            - Cosecha: " . optional($this->indiceCosecha)->madurez_miel . ", alzas: " . optional($this->indiceCosecha)->num_alzadas . "
            - Preparación invernada: " . optional($this->preparacionInvernada)->control_sanitario . "
            ";
    }
    
    // Relaciones
    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }
    
    public function colmena()
    {
        return $this->belongsTo(Colmena::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function desarrolloCria()
    {
        return $this->belongsTo(DesarrolloCria::class, 'desarrollo_cria_id');
    }

    public function calidadReina()
    {
        return $this->belongsTo(CalidadReina::class, 'calidad_reina_id');
    }

    public function estadoNutricional()
    {
        return $this->hasOne(EstadoNutricional::class, 'visita_id');
    }

    public function presenciaVarroa()
    {
        return $this->hasOne(PresenciaVarroa::class, 'visita_id');
    }

    public function presenciaNosemosis()
    {
        return $this->hasOne(PresenciaNosemosis::class, 'visita_id');
    }

    public function indiceCosecha()
    {
        return $this->belongsTo(IndiceCosecha::class, 'indice_cosecha_id');
    }

    public function preparacionInvernada()
    {
        return $this->belongsTo(PreparacionInvernada::class, 'preparacion_invernada_id');
    }

    public function visitaGeneral()
    {
        return $this->hasOne(VisitaGeneral::class, 'visita_id');
    }

    public function inspeccion()
    {
        return $this->hasOne(VisitaInspeccion::class, 'visita_id');
    }
}