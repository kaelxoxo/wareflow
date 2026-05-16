<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($title ?? 'Sign In') ?> — Wareflow</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
*, ::before, ::after { font-family: 'Plus Jakarta Sans', sans-serif; }
.material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; vertical-align: -4px; }
input:focus, select:focus, textarea:focus { outline: none; box-shadow: 0 0 0 3px rgba(0,74,198,.2); }
</style>
</head>
<body style="background:linear-gradient(135deg,#eef2ff 0%,#f5f7ff 50%,#e8effe 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem;">

<div style="width:100%;max-width:420px">

  <!-- Logo -->
  <div style="text-align:center;margin-bottom:28px">
    <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:#004ac6;border-radius:14px;margin-bottom:12px;box-shadow:0 8px 24px rgba(0,74,198,.25)">
      <span class="material-symbols-outlined" style="color:#fff;font-size:24px">warehouse</span>
    </div>
    <h1 style="font-size:22px;font-weight:800;color:#004ac6;margin:0;letter-spacing:-0.3px">Wareflow</h1>
    <p style="font-size:13px;color:#64748b;margin:4px 0 0">Smart Warehouse &amp; Inventory Management</p>
  </div>

  <!-- Flash -->
  <?php $flash = get_flash(); if ($flash): ?>
  <div style="margin-bottom:16px;padding:12px 14px;border-radius:12px;font-size:13.5px;display:flex;align-items:center;gap:10px;
    <?= $flash['type']==='error'
      ? 'background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;'
      : 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;' ?>">
    <span class="material-symbols-outlined" style="font-size:18px;flex-shrink:0"><?= $flash['type']==='error' ? 'error' : 'check_circle' ?></span>
    <span><?= $flash['msg'] ?></span>
  </div>
  <?php endif; ?>

  <!-- Card -->
  <div style="background:#fff;border-radius:20px;padding:36px;box-shadow:0 4px 6px -1px rgba(0,0,0,.05),0 20px 40px -8px rgba(0,0,0,.08);border:1px solid #e2e8f0">
    <?= $content ?>
  </div>

  <p style="text-align:center;font-size:12px;color:#94a3b8;margin-top:20px">
    &copy; <?= date('Y') ?> Wareflow. All rights reserved.
  </p>
</div>
</body>
</html>
