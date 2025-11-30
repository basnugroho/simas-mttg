<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Article;
$a = Article::find(5);
if (!$a) {
    echo "NOTFOUND\n";
    exit(0);
}
echo "IMAGE_URL_RAW:" . ($a->image_url ?? '') . "\n";
echo "IMAGE_URL_PREFIX_CHECK:" . (strpos($a->image_url ?? '', 'storage/') === 0 ? 'starts_with_storage' : 'no') . "\n";
echo "IMAGE_URL_LEADING_SLASH:" . (isset($a->image_url) && strpos($a->image_url, '/') === 0 ? 'leading_slash' : 'no') . "\n";

// show storage disk url if possible
try {
    $u = Illuminate\Support\Facades\Storage::disk('public')->url($a->image_url);
    echo "STORAGE_URL:" . $u . "\n";
} catch (Exception $e) {
    echo "STORAGE_URL_ERROR:" . $e->getMessage() . "\n";
}

echo "ASSET_URL:" . asset($a->image_url) . "\n";

?>