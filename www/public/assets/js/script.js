//================= garden/about.twig =========================
// affichage de la météo sur le point cliqué sur la carte dans about
function afficheMeteo(response) {
    if (response.name == "Montlucon") {
        response.name = "Montluçon";
    }
    document.getElementById("ville").innerText = response.name;
    document.getElementById("temperature").innerText = Math.round(response.main.temp) + " °C";
    document.getElementById("description").innerText = response.weather[0].description;
    document.getElementById("vitesse").innerText = response.wind.speed + " km/h";
    document.getElementById("humidite").innerText = response.main.humidity + " %";
    document.getElementById("minmax").innerText = response.main.temp_min + '°C / ' + response.main.temp_max + '°C';
}

// get météo du point cliqué sur la carte dans about
function getWeather(zipcode, city, lat, lon) {
    var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr";
    // en jquery
    if (lat == null || lon == null) {
        if (city != null) {
            url = "https://api.openweathermap.org/data/2.5/weather?q=" + city + ",fr&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr";
        } else if (zipcode != null) {
            url = "https://api.openweathermap.org/data/2.5/weather?zip=" + zipcode + ",fr&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr";
        }
    }

    $.get(url, null, function(response) {
        afficheMeteo(response);
    });

    // AUTRE SOLUTION ============================================================

    // var settings = {
    //     "async": true,
    //     //"crossDomain": true,
    //     "url": "https://api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr",
    //     "method": "GET" //,
    //         //"headers": {
    //         //"cache-control": "no-cache"
    //         //}
    // }

    // $.ajax(settings).done(function(response) {
    //     console.log(response); // en json
    // });

    // AUTRE SOLUTION ============================================================
    // var data = null;
    // var xhr = new XMLHttpRequest();
    // //xhr.withCredentials = true;

    // xhr.addEventListener("readystatechange", function() {
    //     if (this.readyState === 4) {
    //         console.log(this.responseText);
    //         // renseigner la meteo
    //     }
    // });

    // xhr.open("GET", "http://api.openweathermap.org/data/2.5/weather?lat=" + lat + "&lon=" + lon + "&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr", true);
    // //xhr.setRequestHeader("cache-control", "no-cache"); //
    // //xhr.setRequestHeader("Postman-Token", "539bbb25-d7b8-4eb2-abd1-ade3138e1917");
    //
    // xhr.send();
}

// Fonction d'initialisation de la carte OpenStreetMap_LeafletJs
// dans la page about
function initMap() {
    // On initialise la latitude et la longitude (centre de la carte)
    // Paris  lat = 48.852969 lon = 2.349903
    // Auvergne :
    var lat = 45.778593;
    var lon = 3.081186;

    // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
    var macarte = L.map('map').setView([lat, lon], 11);

    // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. 
    // Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        // Il est toujours bien de laisser le lien vers la source des données
        attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
        minZoom: 1,
        maxZoom: 8
    }).addTo(macarte);

    // On charge en Ajax les données des marqueurs qui sont dans datas.json sur le serveur :
    $.getJSON("/assets/js/datas.json", function(villes) {
        //$.post("/sites", { id: null }, function(data) {

        //initialise les groupes de marqueurs
        var markerClusters = L.markerClusterGroup();

        // pour traiter $.getJSON("/assets/js/datas.json", function(villes) {
        // fonction callback au chargement du fichier json :
        // le tableau json est utilisable tel quel, pas de json.parse

        // liste des jardins de la communauté
        //var villes = JSON.parse(data);

        // ajouter les marqueurs sur la carte
        for (ville in villes) {
            // objet SiteEntity
            // var obj = villes[ville];
            // if (obj.city == 'Montluçon') {
            //     obj.city = 'Montlucon';
            // }

            // if (obj.latitude != null && obj.longitude != null) {
            //     var marker = L.marker([obj.latitude, obj.longitude]);
            //     var bulle = "Nom : " + obj.name + "<br/>" +
            //         "Adresse : " + obj.address + "<br/>" +
            //         "Code Postal : " + obj.zip_code + "<br/>" +
            //         "Ville : " + obj.city + "<br/>" +
            //         "Latitude : " + obj.latitude + "<br/>" +
            //         "Longitude : " + obj.longitude + "<br/>" +
            //         "<button class='justify-content-center btn btn-success' onclick='getWeather(" + obj.zip_code + ", \"" + obj.city + "\", " + obj.latitude + ", " + obj.longitude + ")'>Météo</button>";

            // pour traiter $.getJSON("/assets/js/datas.json", function(villes) {
            if (villes[ville].latitude != null && villes[ville].longitude != null) {
                var marker = L.marker([villes[ville].latitude, villes[ville].longitude]);
                // construire le texte en html
                var obj = villes[ville];
                var bulle = "Nom : " + obj.nom + "<br/>" +
                    "Adresse : " + obj.adresse + "<br/>" +
                    "Code Postal : " + obj.code_postal + "<br/>" +
                    "Ville : " + obj.ville + "<br/>" +
                    "Tél : " + obj.tel + "<br/>" +
                    "e-mail : " + obj.email + "<br/>" +
                    "Site Web : " + obj.site_web + "<br/>" +
                    "Porteur : " + obj.porteur + "<br/>" +
                    "Latitude : " + obj.latitude + "<br/>" +
                    "Longitude : " + obj.longitude + "<br/>" +
                    "<button onclick='getWeather(" + obj.latitude + ", " + obj.longitude + ")'>Météo</button>";

                marker.bindPopup(bulle);

                // ajout du marqueur aux groupes
                markerClusters.addLayer(marker);
            }
        }

        macarte.addLayer(markerClusters);
    });
}

//================= plant/admin.twig =========================

// création d'une page modale pour modifier une plante en admin
function getPlantModal(id, name, groupId, image, uploadpath) {
    $('#modal-message').removeAttr('class').text('');

    // remplir les champs de saisie du formulaire
    $('#modal-idPlant').val(id);

    $('#modal-title').text(name);
    $('#modal-name').val(name);

    $('#modal-group-select').val(groupId);
    $('#modal-image').attr('src', uploadpath + image);
}

//================= seed/admin.twig =========================
// création d'une page modale pour modifier une graine en admin
function getSeedModal(id, name, plantId, plantName, originId, stock, limitedAt, growingDays, image, uploadpath) {
    $('#modal-message').removeAttr('class').text('');

    // remplir les champs de saisie du formulaire
    $('#modal-idSeed').val(id);
    $('#modal-title').text(plantName + ': ' + name);
    $('#modal-name').val(name);
    $('#modal-image').attr('src', uploadpath + image);

    $('#modal-plant-select').val(plantId);
    $('#modal-origin-select').val(originId);
    $('#modal-stock').val(stock);
    $('#modal-days').val(growingDays);

    $('#modal-limitedAt')[0].value = limitedAt.substr(0, 10);
}

//================= users/profil.twig =========================

// lecture en base et affichage des coordonnées d'un user_infos
// à partir d'un select contenant la liste des user_infos_id
function selectClient() {
    var index = document.getElementById("clients").selectedIndex;
    var id = document.getElementById("clients").options[index].value;

    // appel AJAX pour lancer un post 
    $.post("/getClient", { idClient: id },
        function(data) {
            obj = JSON.parse(data);
            if (obj) {
                for (var input in obj) {
                    if (document.getElementById(input)) {
                        document.getElementById(input).value = obj[input];
                    }
                }
            }
        });
}

// lecture en base et affichage des coordonnées d'un user_infos
// à partir d'un navbar
function selectAdresse(id) {
    // appel AJAX pour lancer un post 
    $.post("/getClient", { user_infos_id: id },
        function(data) {
            elts = document.getElementsByClassName("nav-link active");
            for (var elt = 0; elt < elts.length; elt++) {
                $('#' + elts[elt].id).removeClass("active");
            }
            $('#a_' + id).addClass("active");
            obj = JSON.parse(data);
            //document.getElementById("user_infos_id").value = obj["id"];
            if (obj) {
                for (var input in obj) {
                    if (document.getElementById(input)) {
                        document.getElementById(input).value = obj[input];
                    }
                }
            }

        });
}

//================= plant/all.twig =========================
//================= seed/all.twig =========================

/* clic sur une case 'favori' sur plante ou graine */
/* id: id de la plante ou graine, selon la page courante */
function setFavori(id) {
    var chknId = "chk_" + id.toString();
    if (document.getElementById(chknId).checked) {
        addFavori(id);
    } else {
        delFavori(id);
    }
}

/* ajout d'une plante ou d'une graine dans les favoris stockés dans le localStorage de la page */
function addFavori(id) {
    var gNotifications = [];

    // url de la page courante : plant ou seed
    var favori = document.location.href.split("/").pop();
    favori = favori.split(".")[0];

    if (localStorage.getItem(favori) == null) {
        localStorage.setItem(favori, "");
    }
    if (localStorage.getItem(favori).length > 0) {
        gNotifications = JSON.parse(localStorage.getItem(favori));
    } else {
        gNotifications.length = 0;
    }
    gNotifications.push(id);
    localStorage.setItem(favori, JSON.stringify(gNotifications));

}

/* suppression d'une plante ou d'une graine dans les favoris stockés dans le localStorage de la page */
function delFavori(id) {
    var gNotifications = [];

    // nom de la page courante
    var favori = document.location.href.split("/").pop();
    favori = favori.split(".")[0];

    if (localStorage.getItem(favori) == null) {
        localStorage.setItem(favori, "");
    }
    if (localStorage.getItem(favori).length > 0) {
        gNotifications = JSON.parse(localStorage.getItem(favori));
    } else {
        gNotifications.length = 0;
    }

    gNotifications.pop(id);
    localStorage.setItem(favori, JSON.stringify(gNotifications));

}

/* cocher tous les favoris de la page courante (plant/all ou seed/all) */
function afficheFavoris() {
    var gNotifications = [];
    var url = document.location.href.split("/");
    // nom de la page courante
    var favori = document.location.href.split("/").pop();
    if (parseInt(favori)) {
        // si numérique
        favori = url[url.length - 2];
    }
    favori = favori.split(".")[0];

    if (localStorage) {
        ''
        if (localStorage.getItem(favori) != null) {
            if (localStorage.getItem(favori).length > 0) {
                gNotifications = JSON.parse(localStorage.getItem(favori));
                for (var i = 0; i < gNotifications.length; i++) {
                    var chknId = "chk_" + gNotifications[i];
                    if (document.getElementById(chknId)) {
                        document.getElementById(chknId).checked = true;
                    }
                }
            }
        }
    }
}



//================= garden/action.twig =========================

// sélection d'un titre dans la liste déroulante
// affectation de ce titre dans l'input
function selectTitle(elt) {
    document.getElementById("title").value = elt.selectedOptions[0].innerText;
}

// sélection d'un content dans la liste déroulante
// affectation de ce content dans l'input
function selectContent(elt) {
    document.getElementById("content").value = elt.selectedOptions[0].innerText;
}

/* clic sur la poubelle d'une action */
/* id: id de l'action */
function actionDelete(id) {
    if (id >= 0) {
        if (confirm("Veuillez confirmez la suppression de cette action.")) {
            // appel AJAX pour lancer un post de lecture en base des plantes du groupe
            $.post("/action", { idDelete: id },
                function(data) {
                    document.location.replace("/action");
                    // var trId = "traction_" + id.toString();
                    // trElt = document.getElementById(trId);
                    // trElt.innerHTML = "";
                });
        }
    }

}