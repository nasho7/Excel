<?php
require('conexion.php');

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $file_tmp = $_FILES['archivo']['tmp_name'];
        $file_name = $_FILES['archivo']['name'];

        if (pathinfo($file_name, PATHINFO_EXTENSION) === 'csv') {
            if (($handle = fopen($file_tmp, 'r')) !== false) {
                $query = $conexion->prepare("INSERT INTO queso (DNI, NOMBRE, APELLIDO, DIRECCIÓN) VALUES (?, ?, ?, ?)");

                while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                    if (isset($data[0], $data[1], $data[2], $data[3])) {
                        $query->bind_param('ssss', $data[0], $data[1], $data[2], $data[3]);
                        $query->execute();
                    }
                }
                fclose($handle);
                $mensaje = "Datos insertados correctamente";
                $tipo = "exito";
            } else {
                $mensaje = "Error al abrir el archivo CSV";
                $tipo = "error";
            }
        } else {
            $mensaje = "Por favor, suba un archivo CSV válido";
            $tipo = "error";
        }
    } else {
        $mensaje = "No se ha recibido ningún archivo o hubo un error en la carga";
        $tipo = "error";
    }
}

$consultaSQL = "SELECT * FROM queso";
$resultado = mysqli_query($conexion, $consultaSQL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir CSV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto p-6 bg-white rounded-lg shadow-xl" x-data="{ nombreArchivo: '' }">
        <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Suba su Excel</h2>
        
        <form action="" method="post" enctype="multipart/form-data" class="mb-8">
            <div class="flex flex-col items-center space-y-4">
                <label for="archivo" class="w-64 flex flex-col items-center px-4 py-6 bg-white text-blue-500 rounded-lg shadow-lg tracking-wide uppercase border border-blue-500 cursor-pointer hover:bg-blue-500 hover:text-white transition-colors duration-300">
                    <svg class="w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                    </svg>
                    <span class="mt-2 text-base leading-normal" x-text="nombreArchivo || 'Seleccionar archivo'"></span>
                    <input type='file' id="archivo" name="archivo" accept=".csv" class="hidden" x-on:change="nombreArchivo = $event.target.files[0].name" />
                </label>
                <button type="submit" name="importar" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300">
                    Subir
                </button> <a href="pdf-excel.php"><button" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-300 ">
                    Imprimir
                </button>
            </a>
            </div>
        </form>
        
        <?php if (!empty($mensaje)): ?>
            <div id="respuesta" class="mb-6 p-4 <?php echo $tipo === 'exito' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> rounded-lg">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 text-left">DNI</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Apellido</th>
                            <th class="px-4 py-2 text-left">Dirección</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = mysqli_fetch_array($resultado)): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($fila['DNI']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($fila['NOMBRE']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($fila['APELLIDO']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($fila['DIRECCIÓN']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600">No hay datos disponibles.</p>
        <?php endif; ?>
    </div>
</body>
</html>