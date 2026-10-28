<?php include("conexion.php"); 
// cuenta tus 7 tablas para que sea tuyo
$total = $pdo->query("SHOW TABLES")->rowCount();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SIGEM Grupo 1</title>
<style>
body{margin:0;background:#0f1a4a;display:flex;min-height:100vh;font-family:Arial;color:white}
.izq{padding:60px;font-size:22px;width:60%}
.box{background:white;color:#333;width:300px;padding:25px;border-radius:15px;margin:auto;text-align:center;height:fit-content}
input{width:100%;padding:11px;margin:7px 0;border:1px solid #ccc;border-radius:8px;box-sizing:border-box}
button{width:100%;padding:11px;background:#0f1a4a;color:white;border:none;border-radius:8px;font-weight:bold}
</style>
</head>
<body>
<div class="izq">
Conexión OK - SIGEM_BD con <?php echo $total; ?> tablas
</div>
<div class="box">
<h2>SIGEM</h2>
<p>Grupo 1</p>
<form method="POST">
<input name="user" placeholder="Usuario">
<input name="pass" type="password" placeholder="Contraseña">
<button>Ingresar</button>
</form>
</div>
</body>
</html>
