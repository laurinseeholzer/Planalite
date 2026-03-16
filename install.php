<?php

$repo = "laurinseeholzer/Planalite";
$zipUrl = "https://github.com/$repo/releases/download/latest/cms-latest.zip";
$tempZip = __DIR__ . "/cms_setup.zip";

if (isset($_GET['step'])) {
    header('Content-Type: application/json');

    if ($_GET['step'] === 'download') {
        if (!class_exists('ZipArchive')) {
            echo json_encode(['ok' => false, 'error' => "PHP ZipArchive extension is not enabled on this server."]);
            exit;
        }
        if (!function_exists('curl_init')) {
            echo json_encode(['ok' => false, 'error' => "PHP cURL extension is not enabled on this server."]);
            exit;
        }
        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-CMS-Installer');
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200 || !$data) {
            echo json_encode(['ok' => false, 'error' => "Could not download the ZIP. (HTTP $httpCode) — make sure the release is public."]);
            exit;
        }
        file_put_contents($tempZip, $data);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($_GET['step'] === 'extract') {
        $zip = new ZipArchive;
        if ($zip->open($tempZip) !== TRUE) {
            echo json_encode(['ok' => false, 'error' => 'The downloaded file is not a valid ZIP archive.']);
            exit;
        }
        if (!$zip->extractTo(__DIR__)) {
            $zip->close();
            echo json_encode(['ok' => false, 'error' => 'Could not extract files. Check folder permissions (755).']);
            exit;
        }
        $zip->close();
        unlink($tempZip);
        unlink(__FILE__);
        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'error' => 'Unknown step.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-white dark:bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planalite Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">

<div class="w-full max-w-md">
    <div class="flex items-center justify-center mb-8">
        <span class="text-2xl font-bold text-gray-900 dark:text-white">Planalite</span>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 rounded-xl px-8 py-10">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">CMS Installer</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Downloading and extracting the latest release from GitHub.</p>

        <div id="steps" class="space-y-3"></div>
        <div id="action" class="mt-6 hidden"></div>
    </div>
</div>

<script>
const stepsEl = document.getElementById('steps');
const actionEl = document.getElementById('action');

const SVG_SPIN = `<svg class="size-5 text-blue-500 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
</svg>`;
const SVG_CHECK = `<svg class="size-5 text-green-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
</svg>`;
const SVG_ERROR = `<svg class="size-5 text-red-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
</svg>`;

function addStep(id, label, state = 'loading') {
    const colours = {
        loading: 'bg-blue-50 dark:bg-blue-900/20',
        done:    'bg-green-50 dark:bg-green-900/20',
        error:   'bg-red-50 dark:bg-red-900/20',
    };
    const textColours = {
        loading: 'text-blue-800 dark:text-blue-200',
        done:    'text-green-800 dark:text-green-200',
        error:   'text-red-800 dark:text-red-200',
    };
    const icon = { loading: SVG_SPIN, done: SVG_CHECK, error: SVG_ERROR }[state];

    const div = document.createElement('div');
    div.id = id;
    div.className = `rounded-md ${colours[state]} p-4`;
    div.innerHTML = `<div class="flex items-center gap-3">${icon}<p class="text-sm font-medium ${textColours[state]}">${label}</p></div>`;
    stepsEl.appendChild(div);
    return div;
}

function updateStep(id, label, state) {
    const old = document.getElementById(id);
    if (old) old.remove();
    addStep(id, label, state);
}

async function run() {
    addStep('step-download', 'Downloading latest version…', 'loading');

    let res = await fetch('?step=download').then(r => r.json()).catch(() => ({ ok: false, error: 'Network error during download.' }));

    if (!res.ok) {
        updateStep('step-download', 'Download failed — ' + res.error, 'error');
        return;
    }

    updateStep('step-download', 'Download complete', 'done');
    addStep('step-extract', 'Extracting files…', 'loading');

    res = await fetch('?step=extract').then(r => r.json()).catch(() => ({ ok: false, error: 'Network error during extraction.' }));

    if (!res.ok) {
        updateStep('step-extract', 'Extraction failed — ' + res.error, 'error');
        return;
    }

    updateStep('step-extract', 'Extraction complete', 'done');

    actionEl.classList.remove('hidden');
    actionEl.innerHTML = `<a href="admin.php" class="w-full flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Launch CMS &rarr;
    </a>`;
}

run();
</script>

</body>
</html>