<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\EquipmentModel;
use App\Models\ReservationModel;

class Home extends BaseController
{
    protected $helpers = ['url', 'equipment'];

    public function index(): string
    {
        $session = session();
        $userId  = (int) $session->get('user_id');
        $db      = db_connect();

        $departmentList = $db->table('user_departments')
            ->select('departments.department_id, departments.department_name')
            ->join('departments', 'departments.department_id = user_departments.department_id')
            ->where('user_departments.user_id', $userId)
            ->orderBy('departments.department_name', 'ASC')
            ->get()
            ->getResultArray();

        // Older accounts may only have the single department stored on users.
        if ($departmentList === [] && $session->get('department_id')) {
            $department = (new DepartmentModel())->find((int) $session->get('department_id'));
            if ($department) {
                $departmentList = [$department];
            }
        }

        $allowedDepartmentIds = array_map('intval', array_column($departmentList, 'department_id'));
        $requestedDepartment  = filter_var($this->request->getGet('department_id'), FILTER_VALIDATE_INT);

        if ($requestedDepartment !== false && in_array($requestedDepartment, $allowedDepartmentIds, true)) {
            $selectedDepartmentId = $requestedDepartment;
        } elseif (in_array((int) $session->get('department_id'), $allowedDepartmentIds, true)) {
            $selectedDepartmentId = (int) $session->get('department_id');
        } else {
            $selectedDepartmentId = $allowedDepartmentIds[0] ?? null;
        }

        $equipmentQuery = (new EquipmentModel())
            ->select('equipment.*, equipment_categories.category_name')
            ->join('equipment_categories', 'equipment_categories.category_id = equipment.category_id', 'left')
            ->orderBy('equipment.equipment_id', 'DESC');

        if ($selectedDepartmentId !== null) {
            $equipmentQuery
                ->groupStart()
                    ->where('equipment.department_id', $selectedDepartmentId)
                    ->orWhere('equipment.department_id IS NULL', null, false)
                ->groupEnd();
        }

        $equipmentList = $equipmentQuery->findAll();
        $categories    = (new CategoryModel())->orderBy('category_name', 'ASC')->findAll();

        $equipmentStats = [
            'total'       => count($equipmentList),
            'available'   => count(array_filter($equipmentList, static fn ($item) => strtoupper($item['status'] ?? '') === 'AVAILABLE')),
            'reserved'    => count(array_filter($equipmentList, static fn ($item) => strtoupper($item['status'] ?? '') === 'RESERVED')),
            'maintenance' => count(array_filter($equipmentList, static fn ($item) => strtoupper($item['condition_status'] ?? '') === 'DAMAGED')),
        ];

        $reservationCount = (new ReservationModel())
            ->where('user_id', $userId)
            ->countAllResults();

        $userName = trim((string) $session->get('full_name')) ?: (string) $session->get('id_number');

        return view('dashboard/index', [
            'equipmentList'        => $equipmentList,
            'categories'           => $categories,
            'equipmentStats'       => $equipmentStats,
            'reservationCount'     => $reservationCount,
            'departmentList'       => $departmentList,
            'selectedDepartmentId' => $selectedDepartmentId,
            'userName'             => $userName,
            'userRole'             => (string) $session->get('role'),
        ]);
    }
}
