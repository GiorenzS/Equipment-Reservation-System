<?php

namespace App\Controllers;

use App\Models\EquipmentModel;

class EquipmentController extends BaseController
{
    protected $helpers = ['url'];

    // List all equipment with category names
    public function index()
    {
        $equipmentModel = new EquipmentModel();

        $equipment = $equipmentModel
            ->select('equipment.*, equipment_categories.category_name')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->orderBy('equipment.equipment_id', 'DESC')
            ->findAll();

        return view('equipment/index', [
            'equipment' => $equipment,
        ]);
    }

    // Display a single equipment item by ID
    public function show($id = null)
    {
        if (! $id) {
            return redirect()->to('equipment')->with('error', 'Invalid equipment ID.');
        }

        $equipmentModel = new EquipmentModel();

        $item = $equipmentModel
            ->select('equipment.*, equipment_categories.category_name')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->where('equipment.equipment_id', $id)
            ->first();

        if (! $item) {
            return redirect()->to('equipment')->with('error', 'Equipment item not found.');
        }

        return view('equipment/detail', [
            'item' => $item,
        ]);
    }
}
