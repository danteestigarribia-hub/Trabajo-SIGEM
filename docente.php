<?php $v = $_GET['v'] ?? ''; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Docente - SIGEM</title>
<style>
*{box-sizing:border-box} body{margin:0;font-family:'Segoe UI',Arial,sans-serif;background:#f1f5f9;display:flex;min-height:100vh}
.sidebar{width:230px;background:#0f172a;color:white;padding:24px 14px;flex-shrink:0}
.sidebar h1{margin:0 0 28px 10px;font-size:26px;letter-spacing:1px}
.sidebar a{display:block;color:#94a3b8;text-decoration:none;padding:12px 14px;border-radius:10px;margin-bottom:6px;font-size:15px}
.sidebar a:hover{background:#1e293b;color:white}
.sidebar a.active{background:#1e40af;color:white;font-weight:600}
.main{flex:1;padding:28px 32px}
.main h2{margin:0 0 20px;font-size:24px}
.top{display:flex;gap:14px;margin-bottom:20px}
.stat{flex:1;background:white;border-radius:14px;padding:18px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.stat b{font-size:26px;color:#1e40af;display:block}
.stat span{font-size:11px;color:#64748b;letter-spacing:1px}
.card{background:white;border-radius:16px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px}
.card h3{margin:0 0 14px}
table{width:100%;border-collapse:collapse} th{text-align:left;color:#64748b;font-size:12px;padding:10px 0} td{padding:12px 0;border-top:1px solid #eee;font-size:14px}
.mesa{background:#eef3ff;border-left:5px solid #1e40af;padding:14px;border-radius:12px;margin-bottom:10px;display:flex;justify-content:space-between}
.badge{background:#0f1a4a;color:white;padding:6px 12px;border-radius:20px;font-size:12px}
.nota{background:#fef9c3;padding:4px 12px;border-radius:20px;font-weight:700}
.btn{display:inline-block;background:#1e40af;color:white;text-decoration:none;padding:10px 18px;border-radius:10px;font-weight:600;font-size:14px;border:none;cursor:pointer}
input{padding:10px;border:1px solid #ddd;border-radius:10px;width:90px;font-size:14px}
</style>
</head>
<body>
<div class="sidebar">
  <h1>SIGEM</h1>
  <a href="docente.php" class="<?php echo $v==''?'active':''; ?>">🏠 Panel principal</a>
  <a href="docente.php?v=mesas" class="<?php echo $v=='mesas'?'active':''; ?>">📅 Mis mesas</a>
  <a href="docente.php?v=alumnos" class="<?php echo $v=='alumnos'?'active':''; ?>">👥 Alumnos</a>
  <a href="docente.php?v=notas" class="<?php echo $v=='notas'?'active':''; ?>">✏️ Notas</a>
  <a href="docente.php?v=actas" class="<?php echo $v=='actas'?'active':''; ?>">🖨️ Actas</a>
</div>
<div class="main">
  <h2>Panel principal - Docente</h2>
  <?php if($v=='' ): ?>
    <div class="top">
      <div class="stat"><b>1</b><span>MIS MESAS</span></div>
      <div class="stat"><b>1</b><span>ALUMNOS INSCRIPTOS</span></div>
    </div>
    <div class="card">
      <h3>📅 Próxima mesa</h3>
      <div class="mesa"><div><b>Matematica</b><br><small>📅 2026-11-10 | 📍 Aula 1 | 👥 1 alumno inscripto</small></div><span class="badge">Confirmada</span></div>
      <br>
      <a class="btn" href="docente.php?v=mesas">Ver mis mesas →</a>
      <a class="btn" href="docente.php?v=alumnos" style="background:#16a34a;margin-left:8px">Ver alumnos →</a>
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
    <tr><td>aguirre lucas</td><td>Matematica</td><td>2026-11-10</td></tr></table></div>
  <?php endif; ?>
  <?php if($v=='notas'): ?>
    <div class="card"><h3>✏️ Cargar Notas</h3>
    <table><tr><th>ALUMNO</th><th>MATERIA</th><th>NOTA</th></tr>
    <tr><td>aguirre lucas</td><td>Matematica</td><td><input value="8"> <button class="btn">Guardar</button></td></tr></table></div>
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
