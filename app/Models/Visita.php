<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = [
        'apiario_id',
        'colmena_id', // Agregado
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
        'num_colmenas_totales' ,
        'num_colmenas_inspeccionadas',
        'num_colmenas_enfermas',
        'observacion_primera_visita',
        'num_colmenas_tratadas', // Agregado
        'motivo_tratamiento', // Agregado
        'nombre_comercial_medicamento', // Agregado
        'principio_activo_medicamento', // Agregado
        'periodo_resguardo', // Agregado
        'responsable', // Agregado
        'nombres', // Agregado
        'apellidos', // Agregado
        'rut', // Agregado
        'motivo', // Agregado
        'telefono', // Agregado
        'firma', // Agregado
    ];

    public function toPrompt()
    {
        return "Informe apícola:\n".
            "- Actividad de la colmena: {$this->actividad_colmena}\n".
            "- Vigor: {$this->vigor_de_colmena}\n".
            "- Nosemosis: ".optional($this->presenciaNosemosis)->signos_clinicos."\n".
            "- Cosecha: ".optional($this->indiceCosecha)->madurez_miel."\n".
            "- Preparación para invernada: ".optional($this->preparacionInvernada)->control_sanitario."\n";
    }

    
    // Relaciones
    public function apiario()
    {
        return $this->belongsTo(Apiario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function desarrolloCria() {
        return $this->belongsTo(DesarrolloCria::class);
    }
    
    public function calidadReina() {
        return $this->belongsTo(CalidadReina::class);
    }
    
    public function estadoNutricional() {
        return $this->belongsTo(EstadoNutricional::class);
    }
    
    public function presenciaVarroa() {
        return $this->belongsTo(PresenciaVarroa::class);
    }
    
    public function presenciaNosemosis() {
        return $this->belongsTo(PresenciaNosemosis::class);
    }
    
    public function preparacionInvernada() {
        return $this->belongsTo(PreparacionInvernada::class);
    }
    
    public function indiceCosecha() {
        return $this->belongsTo(IndiceCosecha::class);
    }


/*
public function scopeSistemaExperto($query)
    {
        return $query->where('tipo_visita', 'Sistema Experto');
    }

    public function scopeInspeccion($query)
    {
        return $query->where('tipo_visita', 'Inspección');
    }

    public function scopeGeneral($query)
    {
        return $query->where('tipo_visita', 'Visita General');
    }

    public function scopeMedicamentos($query)
    {
        return $query->where('tipo_visita', 'Uso de Medicamentos');
    }
 */
}