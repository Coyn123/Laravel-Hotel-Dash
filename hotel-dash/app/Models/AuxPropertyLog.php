<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuxPropertyLog extends Model
{
    use HasFactory;

    protected $table = 'aux_property_logs';

    protected $fillable = [
        'aux_id',
        'aux_log',
    ];

    protected $casts = [
        'aux_log' => 'array', // Automatically cast JSON to PHP array
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to the aux_property_config table
     */
    public function config()
    {
       // return $this->belongsTo(AuxPropertyConfig::class, 'aux_id');
    }

    /**
     * Scope: filter logs for a specific type (pool, spa, etc.)
     */
    public function scopeForType($query, $type)
    {
        return $query->whereHas('config', function ($q) use ($type) {
            $q->where('type', strtolower($type));
        });
    }

    /**
     * Scope: filter by date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope: filter by month/year
     */
    public function scopeForMonth($query, $month, $year)
    {
        return $query
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year);
    }
}
