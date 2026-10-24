<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestEquipmentHelper extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:helper';
    protected $description = 'Test equipment_helper functions';

    public function run(array $params)
    {
        helper('equipment');

        CLI::write('Testing equipment_helper functions...', 'yellow');

        // Test 1: Null or empty should return data:image/svg+xml
        $svg1 = equipment_image_url(null);
        $svg2 = equipment_image_url('');
        $pass1 = str_starts_with($svg1, 'data:image/svg+xml') && str_starts_with($svg2, 'data:image/svg+xml');
        CLI::write('1) Fallback SVG for null/empty: ' . ($pass1 ? 'PASS ✓' : 'FAIL ✗'), $pass1 ? 'green' : 'red');

        // Test 2: Absolute external URL
        $external = 'https://images.unsplash.com/photo-projector.jpg';
        $resExternal = equipment_image_url($external);
        $pass2 = ($resExternal === $external);
        CLI::write('2) External URL preservation: ' . ($pass2 ? 'PASS ✓' : 'FAIL ✗'), $pass2 ? 'green' : 'red');

        // Test 3: Local filename resolves to uploads/equipment/
        $filename = 'canon-dslr.png';
        $resLocal = equipment_image_url($filename);
        $pass3 = str_contains($resLocal, 'uploads/equipment/canon-dslr.png');
        CLI::write('3) Local filename to upload path: ' . ($pass3 ? 'PASS ✓' : 'FAIL ✗'), $pass3 ? 'green' : 'red');

        // Test 4: equipment_has_custom_image
        $pass4 = (! equipment_has_custom_image(null)) && (! equipment_has_custom_image('   ')) && equipment_has_custom_image('canon-dslr.png');
        CLI::write('4) equipment_has_custom_image checks: ' . ($pass4 ? 'PASS ✓' : 'FAIL ✗'), $pass4 ? 'green' : 'red');

        if ($pass1 && $pass2 && $pass3 && $pass4) {
            CLI::write('ALL HELPER TESTS PASSED ✓', 'green');
        } else {
            CLI::error('ONE OR MORE TESTS FAILED ✗');
        }
    }
}
