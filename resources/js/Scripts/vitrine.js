function photoAR() {
  // Sélectionner tous les composants class="conteneur notext"
  const conteneurs = document.querySelectorAll('.conteneur.notext');
  
  conteneurs.forEach(container => {
    // On enregistre le ratio demandé par le conteneur (ratio naturel de l'image)
    const originalRatio = container.style.aspectRatio;
    container.style.aspectRatio = 'unset'; // On overwrite le ratio
    // On mesure sa taille "naturelle" (sans la directive aspect-ratio)
    const height = container.offsetHeight;
    container.style.aspectRatio = originalRatio; // On remet la directive initiale
    
    // Par défaut, l'élément a un ratio donné par le conteneur dynamique
    // Mais celui-ci est écrasé par la directive CSS suivante, liée à .natural
    // .conteneur.notext.natural { aspect-ratio: unset !important; }

    if (height < 10) { // Si l'élément est applatit
      // On enlève .natural, ce qui retire la directive !important
      // Et le ratio forcé par l'image reprend le dessus
      container.classList.remove("natural");
    } else {
      container.classList.add("natural");
    }
  });
}

window.addEventListener('load', photoAR);

let photoARTimeout; // On utilise un léger timeout pour éviter de rapidfire lors de resize
window.addEventListener('resize', () => {
  clearTimeout(photoARTimeout);
  photoARTimeout = setTimeout(photoAR, 25);
});
