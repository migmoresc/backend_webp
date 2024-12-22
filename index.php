<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert to webp</title>
    <link rel="stylesheet" href="reset.css">
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
                <input id="ficheros" type="file" multiple accept=".png,.jpg">
                <span id="error_ficheros"></span>
            </div>
            <div class="listado_ficheros box">
                <textarea name="listado_ficheros" id="listado_ficheros"></textarea>
            </div>
            <div class="convertir box">
                <label for="calidad" data-translate="calidad">Calidad:</label>
                <input type="number" id="calidad" min="1" max="99">
                <input type="button" id="boton_convertir" value="Convertir" data-translate="convertir">
                <span id="error_calidad"></span>
            </div>
            <div class="visualizacion box">
                <img src="" alt="imagen original">
                <img src="" alt="imagen en webp">
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