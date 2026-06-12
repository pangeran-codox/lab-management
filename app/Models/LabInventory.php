<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabInventory extends Model
{
    protected $table = 'lab_inventory';

    protected $fillable = [
        'resource_id', 'item_name', 'category', 'brand', 'model',
        'serial_number', 'specifications', 'condition', 'status',
        'quantity', 'quantity_good', 'quantity_broken', 'quantity_backup',
        'notes', 'created_by', 'updated_by', 'deleted_at',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(InventoryMaintenanceLog::class, 'lab_inventory_id');
    }
}
