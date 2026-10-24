<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\EquipmentModel;
use App\Models\CategoryModel;
use App\Models\ReservationModel;
use App\Models\UserProfileModel;

/**
 * Quick smoke test for all 4 models. Delete after verifying.
 * Usage: php spark test:models
 */
class TestModels extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:models';
    protected $description = 'Smoke-test the 4 core models against the live DB.';

    public function run(array $params)
    {
        CLI::write('=== Model Smoke Test ===', 'yellow');
        CLI::newLine();

        // 1. CategoryModel — insert + read
        CLI::write('1) CategoryModel', 'green');
        $cat = new CategoryModel();
        $catId = $cat->insert([
            'category_name' => '_test_category_' . time(),
            'description'   => 'Smoke test',
        ]);
        CLI::write("   Inserted category ID: {$catId}");
        $row = $cat->find($catId);
        CLI::write("   Read back: {$row['category_name']}");

        // 2. EquipmentModel — insert + read
        CLI::write('2) EquipmentModel', 'green');
        $eq = new EquipmentModel();
        $eqId = $eq->insert([
            'equipment_code' => '_TEST_' . time(),
            'equipment_name' => 'Test Equipment',
            'category_id'    => $catId,
        ]);
        CLI::write("   Inserted equipment ID: {$eqId}");
        $row = $eq->find($eqId);
        CLI::write("   Status default: {$row['status']}");

        // 3. UserModel — insert + verify password hash
        CLI::write('3) UserModel', 'green');
        $user = new UserModel();
        $plainPassword = 'testpassword123';
        $userId = $user->insert([
            'id_number' => '_test_id_' . time(),
            'password'  => $plainPassword,
            'role'      => 'STUDENT',
        ]);
        CLI::write("   Inserted user ID: {$userId}");
        $row = $user->find($userId);
        $hashed = password_verify($plainPassword, $row['password']);
        CLI::write("   Password hashed correctly: " . ($hashed ? 'YES ✓' : 'NO ✗'));

        // 4. ReservationModel — insert + checkAvailability
        CLI::write('4) ReservationModel', 'green');
        $res = new ReservationModel();
        $resId = $res->insert([
            'user_id'          => $userId,
            'equipment_id'     => $eqId,
            'reservation_date' => '2026-12-25',
            'start_time'       => '09:00',
            'end_time'         => '11:00',
            'purpose'          => 'Smoke test',
        ]);
        CLI::write("   Inserted reservation ID: {$resId}");

        // Same slot should conflict
        $available = $res->checkAvailability($eqId, '2026-12-25', '10:00', '12:00');
        CLI::write("   Overlap 10:00-12:00: " . ($available ? 'AVAILABLE (unexpected)' : 'CONFLICT ✓'));

        // Non-overlapping slot should be free
        $available2 = $res->checkAvailability($eqId, '2026-12-25', '13:00', '15:00');
        CLI::write("   No-overlap 13:00-15:00: " . ($available2 ? 'AVAILABLE ✓' : 'CONFLICT (unexpected)'));

        // 5. UserProfileModel — create + read + update
        CLI::write('5) UserProfileModel', 'green');
        $profile = new UserProfileModel();
        $profId = $profile->createProfile($userId, 'Test', 'User', 'testuser@example.com', '09123456789', 'CCS');
        CLI::write("   Created profile ID: {$profId}");

        $pRow = $profile->getProfileByUserId($userId);
        CLI::write("   Read back email: {$pRow['email']}");

        $updated = $profile->updateProfile($userId, ['department' => 'Engineering']);
        CLI::write("   Updated department: " . ($updated ? 'YES ✓' : 'NO ✗'));
        $pRowUpdated = $profile->getProfileByUserId($userId);
        CLI::write("   New department: {$pRowUpdated['department']}");

        // 6. Cleanup
        CLI::newLine();
        CLI::write('Cleaning up test data...', 'yellow');
        $profile->where('user_id', $userId)->delete();
        $res->delete($resId);
        $eq->delete($eqId);
        $cat->delete($catId);
        $user->delete($userId);
        CLI::write('Done — all test rows removed.', 'green');
    }
}
