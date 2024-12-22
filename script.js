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
    console.log(e.target.value);
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