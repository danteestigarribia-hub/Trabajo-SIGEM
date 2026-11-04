<?php $v = $_GET['v'] ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Docente - SIGEM</title>
<style>
*{box-sizing:border-box} body{margin:0;font-family:'Segoe UI',Arial,sans-serif;background:#dbeafe;display:flex;min-height:100vh;color:#0f172a}
.sidebar{width:250px;background:#0f172a;color:white;padding:28px 16px;flex-shrink:0}
.sidebar .logo{display:flex;align-items:center;gap:10px;padding:0 10px 22px;border-bottom:1px solid rgba(255,255,255,.15);margin-bottom:22px}
.sidebar .logo span{font-size:28px}
.sidebar .logo b{font-size:24px;letter-spacing:2px}
.sidebar .logo small{display:block;font-size:11px;color:#93c5fd;letter-spacing:1px;font-weight:400}
.sidebar a{display:flex;gap:10px;align-items:center;color:#cbd5e1;text-decoration:none;padding:13px 14px;border-radius:12px;margin-bottom:6px;font-size:15px;transition:.2s}
.sidebar a:hover{background:rgba(255,255,255,.12);color:white;transform:translateX(3px)}
.sidebar a.active{background:white;color:#1e40af;font-weight:700;box-shadow:0 4px 15px rgba(0,0,0,.25)}
.main{flex:1;padding:30px 34px;background:#dbeafe}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.header h2{margin:0;font-size:26px;color:#0f172a}
.header p{margin:4px 0 0;color:#475569;font-size:14px}
.avatar{width:44px;height:44px;background:#1e40af;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:18px}
.top{display:flex;gap:16px;margin-bottom:20px}
.stat{flex:1;background:white;border-radius:18px;padding:20px;display:flex;gap:16px;align-items:center;box-shadow:0 4px 18px rgba(0,0,0,.06);border:1px solid #e2e8f0;color:#0f172a}
.stat .icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px}
.stat b{font-size:28px;line-height:1;display:block}
.stat span{font-size:11px;color:#64748b;letter-spacing:1px;font-weight:700}
.card{background:white;border-radius:20px;padding:24px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:16px;border:1px solid #e2e8f0;color:#0f172a}
.card h3{margin:0 0 16px;font-size:17px}
.mesa{background:linear-gradient(90deg,#eff6ff,#f8fafc);border-left:6px solid #1e40af;padding:16px 18px;border-radius:14px;display:flex;justify-content:space-between;align-items:center}
.mesa b{font-size:17px}
.mesa small{color:#64748b}
.badge{background:#16a34a;color:white;padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700}
table{width:100%;border-collapse:collapse} th{text-align:left;color:#64748b;font-size:12px;padding:10px 0;letter-spacing:.5px} td{padding:13px 0;border-top:1px solid #f1f5f9;font-size:14px}
.nota{background:#fef9c3;padding:5px 14px;border-radius:20px;font-weight:800;color:#854d0e}
.btn{display:inline-block;background:#1e40af;color:white;text-decoration:none;padding:11px 20px;border-radius:12px;font-weight:700;font-size:14px;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(30,64,175,.25)}
.btn.green{background:#16a34a;box-shadow:0 4px 12px rgba(22,163,74,.25)}
input{padding:10px 12px;border:1px solid #ddd;border-radius:10px;width:90px;font-size:14px;font-weight:700;text-align:center}
</style>
</head>
<body>
<div class="sidebar">
  <div class="logo"><span>🎓</span><div><b>SIGEM</b><small>SISTEMA DE GESTIÓN</small></div></div>
  <a href="docente.php" class="<?php echo $v==''?'active':''; ?>">🏠 Panel principal</a>
  <a href="docente.php?v=mesas" class="<?php echo $v=='mesas'?'active':''; ?>">📅 Mis mesas</a>
  <a href="docente.php?v=alumnos" class="<?php echo $v=='alumnos'?'active':''; ?>">👥 Alumnos</a>
  <a href="docente.php?v=notas" class="<?php echo $v=='notas'?'active':''; ?>">✏️ Notas</a>
  <a href="docente.php?v=actas" class="<?php echo $v=='actas'?'active':''; ?>">🖨️ Actas</a>
</div>
<div class="main">
  <div class="header"><div><h2>Panel del Docente</h2><p>Gestión de mesas de examen y calificaciones</p></div><div class="avatar">D</div></div>

  <?php if($v=='' ): ?>
    <div class="top">
      <div class="stat"><div class="icon" style="background:#dbeafe">📅</div><div><b>1</b><span>MIS MESAS</span></div></div>
      <div class="stat"><div class="icon" style="background:#dcfce7">👥</div><div><b>1</b><span>ALUMNOS INSCRIPTOS</span></div></div>
    </div>
    <div class="card">
      <h3>📅 Próxima mesa</h3>
      <div class="mesa"><div><b>Matematica</b><br><small>📅 2026-11-10 &nbsp;|&nbsp; 📍 Aula 1 &nbsp;|&nbsp; 👥 1 alumno inscripto</small></div><span class="badge">● Confirmada</span></div>
      <br>
      <a class="btn" href="docente.php?v=mesas">Ver mis mesas →</a>
      <a class="btn green" href="docente.php?v=alumnos" style="margin-left:8px">Ver alumnos →</a>
    </div>
  <?php endif; ?>

  <?php if($v=='mesas'): ?>
    <div class="card"><h3>📅 Mis Mesas Asignadas</h3>
      <div class="mesa"><div><b>Matematica - Diciembre</b><br><small>📅 2026-11-10 | 📍 Aula 1</small></div><span class="badge">1 alumno</span></div>
    </div>
  <?php endif; ?>

  <?php if($v=='alumnos'): ?>
    <div class="card"><h3>Alumnos inscriptos por preceptoría</h3>
    <table><tr><th>ALUMNO</th><th>MATERIA</th><th>FECHA</th></tr>
    <tr><td>👤 aguirre lucas</td><td>Matematica</td><td>2026-11-10</td></tr></table></div>
  <?php endif; ?>

  <?php if($v=='notas'): ?>
    <div class="card"><h3>✏️ Cargar Notas</h3>
    <table><tr><th>ALUMNO</th><th>MATERIA</th><th>NOTA</th><th></th></tr>
    <tr><td>aguirre lucas</td><td>Matematica</td><td><input value="8"></td><td><button class="btn">Guardar</button></td></tr></table></div>
  <?php endif; ?>

  <?php if($v=='actas'): ?>
    <div class="card"><h3>🖨️ Actas para imprimir</h3>
    <table><tr><th>ALUMNO</th><th>MATERIA</th><th>NOTA</th></tr>
    <tr><td>aguirre lucas</td><td>Matematica</td><td><span class="nota">8.00</span></td></tr></table>
    <br><button class="btn" onclick="window.print()">🖨️ Imprimir acta</button></div>
  <?php endif; ?>
</div>
</body>
</html>
