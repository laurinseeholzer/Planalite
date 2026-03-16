<?php
/**
 * CMS Installer & Updater
 * Designed for: laurinseeholzer/Planalite
 */

$repo = "laurinseeholzer/Planalite";
$zipUrl = "https://github.com/$repo/releases/download/latest/cms-latest.zip";
$tempZip = __DIR__ . "/cms_setup.zip";

if (isset($_GET['step'])) {
    header('Content-Type: application/json');

    // STEP 1: PREPARE DIRECTORIES
    if ($_GET['step'] === 'prepare') {
        $folders = ['data', 'template'];
        $created = [];
        foreach ($folders as $folder) {
            if (!is_dir(__DIR__ . '/' . $folder)) {
                if (mkdir(__DIR__ . '/' . $folder, 0755)) {
                    $created[] = $folder;
                }
            }
        }
        echo json_encode(['ok' => true, 'created' => $created]);
        exit;
    }

    // STEP 2: DOWNLOAD CORE
    if ($_GET['step'] === 'download') {
        if (!class_exists('ZipArchive') || !function_exists('curl_init')) {
            echo json_encode(['ok' => false, 'error' => "Missing ZipArchive or cURL extension."]);
            exit;
        }
        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Planalite-Installer');
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$data) {
            echo json_encode(['ok' => false, 'error' => "Download failed (HTTP $httpCode)."]);
            exit;
        }
        file_put_contents($tempZip, $data);
        echo json_encode(['ok' => true]);
        exit;
    }

    // STEP 3: EXTRACT CORE
    if ($_GET['step'] === 'extract') {
        $zip = new ZipArchive;
        if ($zip->open($tempZip) !== TRUE) {
            echo json_encode(['ok' => false, 'error' => 'Invalid ZIP archive.']);
            exit;
        }
        // Extracting directly to root. It will overwrite index, admin, and src/
        if (!$zip->extractTo(__DIR__)) {
            $zip->close();
            echo json_encode(['ok' => false, 'error' => 'Permission denied (755 required).']);
            exit;
        }
        $zip->close();
        unlink($tempZip);
        // Note: unlink(__FILE__) removed so you can rerun for updates
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
    <title>Planalite Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center bg-gray-50 dark:bg-gray-900 px-4">

<div class="w-full max-w-md">
    <div class="flex items-center justify-center mb-8">
        <span class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Planalite</span>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-xl ring-1 ring-gray-900/5 dark:ring-white/10 rounded-2xl px-8 py-10">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">Core Update & Setup</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Synchronizing your local environment with the latest GitHub release.</p>

        <div id="steps" class="space-y-3"></div>
        <div id="action" class="mt-8 hidden"></div>
    </div>
</div>

<script>
const stepsEl = document.getElementById('steps');
const actionEl = document.getElementById('action');

const SVG_SPIN = `<svg class="size-5 text-indigo-500 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>`;
const SVG_CHECK = `<svg class="size-5 text-emerald-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" /></svg>`;
const SVG_ERROR = `<svg class="size-5 text-rose-500 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>`;

function renderStep(id, label, state = 'loading') {
    const theme = {
        loading: 'bg-indigo-50/50 dark:bg-indigo-900/10 text-indigo-700 dark:text-indigo-300',
        done:    'bg-emerald-50/50 dark:bg-emerald-900/10 text-emerald-700 dark:text-emerald-300',
        error:   'bg-rose-50/50 dark:bg-rose-900/10 text-rose-700 dark:text-rose-300',
    };
    const icon = { loading: SVG_SPIN, done: SVG_CHECK, error: SVG_ERROR }[state];

    let el = document.getElementById(id);
    if (!el) {
        el = document.createElement('div');
        el.id = id;
        stepsEl.appendChild(el);
    }
    el.className = `rounded-xl border border-transparent transition-all duration-300 ${theme[state]} p-4`;
    el.innerHTML = `<div class="flex items-center gap-3">${icon}<p class="text-sm font-medium">${label}</p></div>`;
}

async function run() {
    // 1. Prepare
    renderStep('s1', 'Verifying local file structure…', 'loading');
    let res = await fetch('?step=prepare').then(r => r.json());
    renderStep('s1', 'Directories initialized', 'done');

    // 2. Download
    renderStep('s2', 'Downloading latest core from GitHub…', 'loading');
    res = await fetch('?step=download').then(r => r.json());
    if (!res.ok) return renderStep('s2', res.error, 'error');
    renderStep('s2', 'Download complete', 'done');

    // 3. Extract
    renderStep('s3', 'Updating source files…', 'loading');
    res = await fetch('?step=extract').then(r => r.json());
    if (!res.ok) return renderStep('s3', res.error, 'error');
    renderStep('s3', 'System updated successfully', 'done');

    // Finish
    actionEl.classList.remove('hidden');
    actionEl.innerHTML = `<a href="admin.php" class="w-full flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-500 transition-all">
        Launch Planalite &rarr;
    </a>`;
}

run();
</script>
</body>
</html>