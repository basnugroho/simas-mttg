<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// create a webmaster user
$user = App\Models\User::factory()->create(['role' => 'webmaster', 'username' => 'simtest_'.time()]);
Illuminate\Support\Facades\Auth::loginUsingId($user->id);

$region = App\Models\Regions::find(2);
if (! $region) {
    echo "Region id=2 not found\n";
    exit(1);
}

// prepare request data: change level to AREA
$req = new Illuminate\Http\Request([
    'name' => $region->name,
    'pov' => $region->pov,
    'level' => 'AREA',
    'type_key' => $region->type_key,
    'code' => $region->code,
    'parent_id' => $region->parent_id,
]);

// call the controller update method via an instantiated controller so AuthorizesRequests
// (which uses the container's Gate/Auth) behaves as in HTTP flow
try {
    $controller = app()->make(App\Http\Controllers\Admin\RegionController::class);
    // Call update and capture output
    $res = $controller->update($req, $region);
    $after = App\Models\Regions::find(2);
    print_r($after->toArray());
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
