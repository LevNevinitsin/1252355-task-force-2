ymaps.ready(init);
function init(){
  const mapContainer = document.querySelector('#map');
  const taskLatitude = mapContainer.dataset.latitude;
  const taskLongitude = mapContainer.dataset.longitude;

  const myMap = new ymaps.Map("map", {
    center: [taskLatitude, taskLongitude],
    zoom: 14.5
  });

  myMap.geoObjects.add(new ymaps.Placemark([taskLatitude, taskLongitude]));
}
