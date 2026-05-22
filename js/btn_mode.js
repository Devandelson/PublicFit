const root = document.documentElement;
let darkMode = false;

document.getElementById("toggle").addEventListener("change", () => {
    darkMode = !darkMode;
    root.style.setProperty("--fondo", darkMode ? "rgba(61, 58, 58, 0.64)" : "white");
    root.style.setProperty("--fondo2", darkMode ? "rgba(2, 2, 2, 0.64)" : "rgba(255, 255, 255, 0.637)");
    root.style.setProperty("--fondo3", darkMode ? "rgb(54, 52, 52)" : "white");

    root.style.setProperty("--text-color", darkMode ? "white" : "black");
});
