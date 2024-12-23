const loadTranslations = async (language) => {
    const response = await fetch(`./idiomas/${language}.json`);
    const translations = await response.json();
    document.querySelectorAll("[data-translate]").forEach((element) => {
        const key = element.getAttribute("data-translate");
        if (element && element.tagName === 'INPUT' && element.type === 'button') {
            element.value = translations[key];
        } else {
            element.textContent = translations[key];
        }
    });
};

document.getElementById("english").addEventListener("click", () => {
    loadTranslations("en");
    localStorage.setItem("language", "en");
    cambiarBanderas("en");
});

document.getElementById("español").addEventListener("click", () => {
    loadTranslations("es");
    localStorage.setItem("language", "es");
    cambiarBanderas("es");
});

document.addEventListener("DOMContentLoaded", () => {
    const lang = navigator.language || navigator.userLanguage;
    const savedLanguage = localStorage.getItem("language") || lang;
    loadTranslations(savedLanguage);
    cambiarBanderas(lang)

    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
});

function cambiarBanderas(idioma) {
    if (idioma === "es") {
        document.getElementById("español").style.display = "none";
        document.getElementById("english").style.display = "block";
    } else {
        document.getElementById("español").style.display = "block";
        document.getElementById("english").style.display = "none";
    }
}

const toggleTheme = (e) => {
    let newTheme;
    if (e.target.value == 0) {
        newTheme = "light";
        document.getElementById("styles_css").setAttribute("href", "./css/styles_light.css");
    } else {
        newTheme = "dark";
        document.getElementById("styles_css").setAttribute("href", "./css/styles_dark.css");
    }

    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
};

document.getElementById('theme_switch').addEventListener('click', toggleTheme);

document.getElementById("boton_convertir").addEventListener("click", convertir);

function comprobarEntradas() {
    if (!document.getElementById("ficheros").files.length > 0) {
        document.getElementById("error_ficheros").style.display = "block";
        return false;
    } else {
        Array.from(document.getElementById("ficheros").files).forEach(e => {
            if (e.name.split(".").pop() != "png" || e.name.split(".").pop() != "jpg") {
                return false;
            }
        });
        document.getElementById("error_ficheros").style.display = "none";
    }

    if (parseInt(document.getElementById("calidad").value) < 1 || parseInt(document.getElementById("calidad").value) > 99) {
        document.getElementById("error_calidad").style.display = "block";
        return false;
    }
    return true;
}

function convertir() {
    const fileInput = document.getElementById('ficheros');
    const formData = new FormData();

    if (comprobarEntradas()) {
        // Append all selected files to FormData
        for (const file of fileInput.files) {
            formData.append('files[]', file); // 'files[]' allows sending multiple files
        }
        // Send the FormData to the server
        fetch('http://localhost/backend_webp/index.php', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
            .then(respuesta => {
                respuesta.forEach(e => {
                    const a = document.createElement("span");
                    a.innerText = e["nombre"];
                    a.addEventListener("click", () => {
                        mostrarImagen(e["nombre"]);
                    });

                    document.getElementById("div_ficheros").append(a);

                });
            })
            .catch(error => console.error('Error:', error));
    }
}

function mostrarImagen(nombre) {
    console.log(nombre);

}