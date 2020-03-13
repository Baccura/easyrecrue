 <?php
 
$variables = [
    'TOKEN' => '',
    'UPLOAD_DIR' => '',
    'EXT' => '',
    'EXT_DELETE' => '',
];

foreach ($variables as $key => $value) {
    putenv("{$key}={$value}");
}