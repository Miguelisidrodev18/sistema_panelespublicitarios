<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlPublicitarioPanel extends Model
{
    protected $table = 'control_publicitario_paneles';

    protected $fillable = ['control_publicitario_id', 'panel_codigo', 'tipo_panel'];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlPublicitario::class, 'control_publicitario_id');
    }
}
