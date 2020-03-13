 <?php
 
$variables = [
    'TOKEN' => '76rNtQif6hcC5ZQUz4yL7266xc3qFhmQM2UY28X3',
    'UPLOAD_DIR' => '../uploads/',
    'EXT' => 'txt',
    'EXT_DELETE' => 'txt.old',
];

foreach ($variables as $key => $value) {
    putenv("{$key}={$value}");
}