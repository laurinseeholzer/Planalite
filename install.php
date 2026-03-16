<?php

$repo = "laurinseeholzer/Planalite";
$zipUrl = "https://github.com/$repo/releases/download/latest/cms-latest.zip";
$tempZip = "cms_setup.zip";

echo "
<style>
    body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 50px auto; padding: 20px; background: #f4f7f6; }
    .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    h2 { margin-top: 0; color: #2c3e50; }
    .status { padding: 10px; border-radius: 4px; margin-bottom: 10px; font-weight: bold; }
    .loading { background: #e1f5fe; color: #0277bd; }
    .success { background: #e8f5e9; color: #2e7d32; }
    .error { background: #ffebee; color: #c62828; }
    .btn { display: inline-block; background: #2ecc71; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 10px; }
</style>
<div class='card'>
    <h2>CMS Installer</h2>
";

if (!class_exists('ZipArchive')) {
    die("<div class='status error'>Error: PHP 'ZipArchive' extension is not enabled on this server.</div>");
}

echo "<div class='status loading'>Downloading latest version...</div>";

$ch = curl_init($zipUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-CMS-Installer');
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$data) {
    die("<div class='status error'>Error: Could not download the ZIP. (HTTP Code: $httpCode). Make sure the Release is 'Public'.</div>");
}

file_put_contents($tempZip, $data);

echo "<div class='status loading'>Extracting files...</div>";
$zip = new ZipArchive;

if ($zip->open($tempZip) === TRUE) {
    if ($zip->extractTo(__DIR__)) {
        $zip->close();
        unlink($tempZip);
        
        echo "<div class='status success'>Installation Complete!</div>";
        echo "<p>Your CMS has been successfully extracted to this folder.</p>";
        echo "<a href='index.php' class='btn'>Launch CMS</a>";
        
        unlink(__FILE__); 
    } else {
        echo "<div class='status error'>Error: Could not extract files. Check folder permissions (755).</div>";
    }
} else {
    echo "<div class='status error'>Error: The downloaded file is not a valid ZIP archive.</div>";
}

echo "</div>";