<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_inventory_id',
        'user_id',
        'maintenance_date',
        'maintenance_type',
        'description',
        'cost',
        'status',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function inventory()
    {
        return $this->belongsTo(LabInventory::class, 'lab_inventory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
