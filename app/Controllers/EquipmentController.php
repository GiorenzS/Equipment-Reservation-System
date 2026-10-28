<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\EquipmentModel;

class EquipmentController extends BaseController
{
    protected $helpers = ['url', 'equipment'];

    public function index()
    {
        $departmentList = (new DepartmentModel())->orderBy('department_name', 'ASC')->findAll();
        $departmentIds = array_map('intval', array_column($departmentList, 'department_id'));
        $assignedDepartmentId = (int) session()->get('department_id');
        $requestedDepartment = filter_var($this->request->getGet('department_id'), FILTER_VALIDATE_INT);

        if ($requestedDepartment !== false && in_array($requestedDepartment, $departmentIds, true)) {
            $selectedDepartmentId = $requestedDepartment;
        } elseif (in_array($assignedDepartmentId, $departmentIds, true)) {
            $selectedDepartmentId = $assignedDepartmentId;
        } else {
            $selectedDepartmentId = $departmentIds[0] ?? null;
        }

        $applyDepartment = static function (EquipmentModel $model) use ($selectedDepartmentId): EquipmentModel {
            if ($selectedDepartmentId !== null) {
                $model->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                    ->groupEnd();
            }
            return $model;
        };

        $statsQuery = $applyDepartment((new EquipmentModel())->select(
            "COUNT(*) AS total, SUM(CASE WHEN status = 'AVAILABLE' THEN 1 ELSE 0 END) AS available, SUM(CASE WHEN status = 'RESERVED' THEN 1 ELSE 0 END) AS reserved, SUM(CASE WHEN condition_status = 'DAMAGED' THEN 1 ELSE 0 END) AS maintenance",
            false
        ));
        $stats = $statsQuery->get()->getRowArray() ?? [];
        $equipmentStats = [
            'total' => (int) ($stats['total'] ?? 0),
            'available' => (int) ($stats['available'] ?? 0),
            'reserved' => (int) ($stats['reserved'] ?? 0),
            'maintenance' => (int) ($stats['maintenance'] ?? 0),
        ];

        $search = $this->request->getGet('search');
        $search = is_string($search) ? trim($search) : '';
        $categoryId = filter_var($this->request->getGet('category_id'), FILTER_VALIDATE_INT);
        $statusInput = $this->request->getGet('status');
        $conditionInput = $this->request->getGet('condition');
        if (! is_string($conditionInput) || trim($conditionInput) === '') {
            $conditionInput = $this->request->getGet('condition_status');
        }
        $status = is_string($statusInput) ? strtoupper($statusInput) : '';
        $condition = is_string($conditionInput) ? strtoupper($conditionInput) : '';

        $equipmentModel = $applyDepartment((new EquipmentModel())
            ->select('equipment.*, equipment_categories.category_name')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->orderBy('equipment.equipment_id', 'DESC'));

        if ($search !== '') {
            $equipmentModel->groupStart()
                ->like('equipment.equipment_name', $search)
                ->orLike('equipment.equipment_code', $search)
                ->orLike('equipment.brand', $search)
                ->orLike('equipment.model', $search)
                ->orLike('equipment_categories.category_name', $search)
                ->groupEnd();
        }
        if ($categoryId !== false && $categoryId > 0) {
            $equipmentModel->where('equipment.category_id', $categoryId);
        }
        if (in_array($status, ['AVAILABLE', 'IN_USE', 'RESERVED'], true)) {
            $equipmentModel->where('equipment.status', $status);
        } else {
            $status = '';
        }
        if (in_array($condition, ['GOOD', 'FAIR', 'DAMAGED'], true)) {
            $equipmentModel->where('equipment.condition_status', $condition);
        } else {
            $condition = '';
        }

        $equipmentList = $equipmentModel->paginate(8, 'catalog');
        $pager = $equipmentModel->pager;

        return view('equipment/catalog', [
            'equipmentList' => $equipmentList,
            'categories' => (new CategoryModel())->orderBy('category_name', 'ASC')->findAll(),
            'equipmentStats' => $equipmentStats,
            'departmentList' => $departmentList,
            'selectedDepartmentId' => $selectedDepartmentId,
            'userName' => trim((string) session()->get('full_name')) ?: (string) session()->get('id_number'),
            'userRole' => (string) session()->get('role'),
            'search' => $search,
            'categoryId' => $categoryId !== false && $categoryId > 0 ? $categoryId : null,
            'statusFilter' => $status,
            'conditionFilter' => $condition,
            'pager' => $pager,
            'paginationDetails' => $pager->getDetails('catalog'),
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