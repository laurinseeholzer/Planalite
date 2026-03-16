<?php

$repo = "laurinseeholzer/Planalite";
$zipUrl = "https://github.com/$repo/releases/download/latest/cms-latest.zip";
$tempZip = "cms_setup.zip";

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

        <?php

        if (!class_exists('ZipArchive')) {
            echo "
            <div class='rounded-md bg-red-50 dark:bg-red-900/30 p-4'>
                <div class='flex'>
                    <svg class='size-5 text-red-400 shrink-0' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' d='M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z' clip-rule='evenodd' />
                    </svg>
                    <div class='ml-3'>
                        <h3 class='text-sm font-medium text-red-800 dark:text-red-200'>Extension missing</h3>
                        <p class='mt-1 text-sm text-red-700 dark:text-red-300'>PHP <code class=\"font-mono\">ZipArchive</code> extension is not enabled on this server.</p>
                    </div>
                </div>
            </div>";
            echo "</div></body></html>";
            exit;
        }

        echo "
        <div class='rounded-md bg-blue-50 dark:bg-blue-900/20 p-4 mb-4'>
            <div class='flex items-center gap-3'>
                <svg class='size-5 text-blue-500 animate-spin shrink-0' fill='none' viewBox='0 0 24 24'>
                    <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                    <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8v8H4z'></path>
                </svg>
                <p class='text-sm font-medium text-blue-800 dark:text-blue-200'>Downloading latest version&hellip;</p>
            </div>
        </div>";
        ob_flush(); flush();

        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-CMS-Installer');
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$data) {
            echo "
            <div class='rounded-md bg-red-50 dark:bg-red-900/30 p-4'>
                <div class='flex'>
                    <svg class='size-5 text-red-400 shrink-0' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' d='M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z' clip-rule='evenodd' />
                    </svg>
                    <div class='ml-3'>
                        <h3 class='text-sm font-medium text-red-800 dark:text-red-200'>Download failed</h3>
                        <p class='mt-1 text-sm text-red-700 dark:text-red-300'>Could not download the ZIP. (HTTP $httpCode) &mdash; make sure the release is public.</p>
                    </div>
                </div>
            </div>";
            echo "</div></body></html>";
            exit;
        }

        file_put_contents($tempZip, $data);

        echo "
        <div class='rounded-md bg-blue-50 dark:bg-blue-900/20 p-4 mb-4'>
            <div class='flex items-center gap-3'>
                <svg class='size-5 text-blue-500 animate-spin shrink-0' fill='none' viewBox='0 0 24 24'>
                    <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                    <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8v8H4z'></path>
                </svg>
                <p class='text-sm font-medium text-blue-800 dark:text-blue-200'>Extracting files&hellip;</p>
            </div>
        </div>";
        ob_flush(); flush();

        $zip = new ZipArchive;

        if ($zip->open($tempZip) === TRUE) {
            if ($zip->extractTo(__DIR__)) {
                $zip->close();
                unlink($tempZip);

                echo "
                <div class='rounded-md bg-green-50 dark:bg-green-900/30 p-4 mb-6'>
                    <div class='flex'>
                        <svg class='size-5 text-green-400 shrink-0' viewBox='0 0 20 20' fill='currentColor'>
                            <path fill-rule='evenodd' d='M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z' clip-rule='evenodd' />
                        </svg>
                        <div class='ml-3'>
                            <h3 class='text-sm font-medium text-green-800 dark:text-green-200'>Installation complete</h3>
                            <p class='mt-1 text-sm text-green-700 dark:text-green-300'>Your CMS has been successfully extracted to this folder.</p>
                        </div>
                    </div>
                </div>
                <a href='index.php' class='w-full flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'>
                    Launch CMS &rarr;
                </a>";

                unlink(__FILE__);
            } else {
                echo "
                <div class='rounded-md bg-red-50 dark:bg-red-900/30 p-4'>
                    <div class='flex'>
                        <svg class='size-5 text-red-400 shrink-0' viewBox='0 0 20 20' fill='currentColor'>
                            <path fill-rule='evenodd' d='M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z' clip-rule='evenodd' />
                        </svg>
                        <div class='ml-3'>
                            <h3 class='text-sm font-medium text-red-800 dark:text-red-200'>Extraction failed</h3>
                            <p class='mt-1 text-sm text-red-700 dark:text-red-300'>Could not extract files. Check folder permissions (755).</p>
                        </div>
                    </div>
                </div>";
            }
        } else {
            echo "
            <div class='rounded-md bg-red-50 dark:bg-red-900/30 p-4'>
                <div class='flex'>
                    <svg class='size-5 text-red-400 shrink-0' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' d='M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-4.75a.75.75 0 0 0 1.5 0V8.75a.75.75 0 0 0-1.5 0v4.5Zm.75-7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z' clip-rule='evenodd' />
                    </svg>
                    <div class='ml-3'>
                        <h3 class='text-sm font-medium text-red-800 dark:text-red-200'>Invalid archive</h3>
                        <p class='mt-1 text-sm text-red-700 dark:text-red-300'>The downloaded file is not a valid ZIP archive.</p>
                    </div>
                </div>
            </div>";
        }

        ?>
    </div>
</div>

</body>
</html>