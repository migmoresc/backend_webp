<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES["files"]) && isset($_POST["json"])) {
        $uploadDir = 'uploads/';

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Check if files were uploaded
        if (!empty($_FILES['files']['name'][0])) {
            $ficheros = [];
            foreach ($_FILES['files']['name'] as $key => $fileName) {
                // Create a finfo object to check the actual MIME type of the file
                $finfo = new finfo(FILEINFO_MIME_TYPE);

                //         // Get the actual MIME type
                $tipoReal = $finfo->file($_FILES['files']['tmp_name'][$key]);
                $fileTmpName = $_FILES['files']['tmp_name'][$key];
                //         // // $fileType = $_FILES['files']['type'][$key];
                $fileSize = filesize($fileTmpName);
                $fileError = $_FILES['files']['error'][$key];

                // Allowed file types (jpg and png)
                $allowedTypes = ['image/jpeg', 'image/png'];
                // Validate the file
                if ($fileError === 0 && in_array($tipoReal, $allowedTypes) && $fileSize > 0 && $fileSize < 5242881) {
                    $safeFileName = preg_replace("/[^a-zA-Z0-9.-_]/", "", $fileName);
                    $filePath = $uploadDir . basename($safeFileName);
                    if (move_uploaded_file($fileTmpName, $filePath)) {
                        $jsonData = json_decode($_POST['json'], true);
                        $fileSizeWebp = convertToWebp($filePath, $uploadDir . pathinfo($safeFileName)["filename"] . ".webp", $jsonData["calidad"]);
                        array_push($ficheros, ['nombre' => $safeFileName, 'url' => $filePath, "size_original" => $fileSize, "size_webp" => $fileSizeWebp]);
                    } else {
                        echo json_encode("Error uploading file '$fileName'.\n");
                    }
                } else {
                    echo json_encode("Invalid file type or error in file '$fileName'.\n");
                }
            }
            echo json_encode($ficheros);
        } else {
            echo json_encode("No files uploaded.");
        }
    } else {
        // Check if the Content-Type is application/json
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') === 0) {
            // Get the raw POST data
            $rawInput = file_get_contents('php://input');

            // Attempt to decode the JSON
            $jsonData = json_decode($rawInput, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($jsonData["accion"]) && $jsonData["accion"] === "descargar") {
                $files = [];
                foreach ($jsonData["nombres"] as $key => $value) {
                    array_push($files, "uploads/" . explode(".", $value)[0] . ".webp");
                }

                $zip = new ZipArchive();
                $zipFile = 'files.zip';

                // Create ZIP file
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    foreach ($files as $file) {
                        if (file_exists($file)) {
                            $zip->addFile($file, basename($file));
                        }
                    }
                    $zip->close();

                    // Send ZIP file as a response
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="files.zip"');
                    header('Content-Length: ' . filesize($zipFile));
                    readfile($zipFile);

                    // Clean up
                    unlink($zipFile);
                } else {
                    echo 'Failed to create ZIP file.';
                }
            } else {
                // JSON decoding failed
                echo "Invalid JSON received.\n";
            }
        }
    }
    exit;
}

function convertToWebp($inputFile, $outputFile, $calidad)
{
    $image = imagecreatefromstring(file_get_contents($inputFile));

    // Check if EXIF extension is available
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($inputFile);

        if (isset($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3: // Rotate 180 degrees
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6: // Rotate 90 degrees clockwise
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8: // Rotate 90 degrees counterclockwise
                    $image = imagerotate($image, 90, 0);
                    break;
            }
        }
    }
    imagewebp($image, $outputFile, $calidad);
    imagedestroy($image);
    return filesize($outputFile);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert to webp</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link id="styles_css" rel="stylesheet" href="./css/styles_light.css">
</head>

<body>
    <header class="jc-sb">
        <div class="logo box">
            <img src="img/logo_128.png" alt="logo" class="logo">
        </div>
        <div class="opciones box">
            <div class="idiomas">
                <button type="button" class="bandera inglaterra" id="english"></button>
                <button type="button" class="bandera española" id="español"></button>
            </div>
            <div class="modos">
                <img src="img/sun-l.svg" alt="Día" class="modo">
                <input type="range" id="theme_switch" name="modo" min="0" max="1" step="1" value="0">
                <img src="img/moon-l.svg" alt="Noche" class="modo">
            </div>
        </div>
    </header>
    <main>
        <div class="containery">
            <div class="entrada_ficheros box">
                <label for="ficheros" data-translate="ficheros">Selecciona los archivos:</label>
                <input id="ficheros" name="files[]" type="file" multiple accept=".png,.jpg">
                <span id="error_ficheros" data-translate="error_ficheros"></span>
            </div>
            <div class="listado_ficheros box">
                <div id="div_ficheros">

                </div>
            </div>
            <div class="convertir box">
                <label for="calidad" data-translate="calidad">Calidad:</label>
                <input type="number" id="calidad" min="1" max="99" value="50" style="text-align: right" ;>
                <input type="button" id="boton_convertir" value="Convertir" data-translate="convertir">
                <span id="error_calidad" data-translate="error_calidad"></span>
            </div>
            <div class="procesando">
                <p data-translate="procesando"></p>
                <p data-translate="1minuto"></p>
            </div>
            <div class="visualizacion box">
                <div class="pesos containerx">
                    <span class="peso"></span>
                    <span class="peso"></span>
                </div>
                <div>
                    <img src="" alt="">
                    <img src="" alt="">
                </div>
            </div>
            <div class="descargar box">
                <input type="button" data-translate="descargar" value="Descargar">
            </div>
        </div>
    </main>
    <footer></footer>
    <script type="module" src="script.js"></script>
</body>

</html>