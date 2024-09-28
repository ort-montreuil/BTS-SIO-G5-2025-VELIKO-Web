document.getElementById('geolocate').addEventListener('click', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const marker = L.marker([lat, lng]).addTo(mymap);
            marker.bindPopup('Vous êtes ici !').openPopup();
            mymap.setView([lat, lng], 13);
        }, function (error) {
            alert('Erreur lors de la géolocalisation : ' + error.message);
        });
    } else {
        alert('La géolocalisation n\'est pas prise en charge par votre navigateur.');
    }
});