<?php 
$theme = $_COOKIE['theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html lang="en" class="h-full <?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planalite Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900" x-data="{ sidebarOpen: false }">

<?php include __DIR__ . '/sidebar-mobile.php'; ?>
<?php include __DIR__ . '/sidebar-desktop.php'; ?>
<?php include __DIR__ . '/topbar.php'; ?>

<main class="py-10 lg:pl-72 dark:bg-gray-900">
    <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <?php if (isset($adminContentTemplate)) { include escapeshellcmd($adminContentTemplate); } ?>
    </div>
</main>

</body>
</html>
