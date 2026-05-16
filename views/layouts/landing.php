<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Wareflow — Smart Warehouse & Inventory Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"','sans-serif']}}}}</script>
<style>
*, ::before, ::after { font-family: 'Plus Jakarta Sans', sans-serif; }
.material-symbols-outlined { font-family:'Material Symbols Outlined'; font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; vertical-align:-4px; }
.gradient-text { background:linear-gradient(135deg,#004ac6,#3e3fcc); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
</style>
</head>
<body class="bg-white text-gray-900">
<?= $content ?>
</body>
</html>
