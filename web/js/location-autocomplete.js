const latitudeInput = document.querySelector('.js-latitude');
const longitudeInput = document.querySelector('.js-longitude');
const cityNameInput = document.querySelector('.js-cityName');

const config = {
  selector: '[data-library="autoComplete"]',
  data: {
    'src': async (query) => {
      try {
        const locationInput = document.querySelector('[data-library="autoComplete"]')
        const userCityName = locationInput.dataset.userCityName;
        const userCityCoordinates = locationInput.dataset.userCityCoordinates;

        const source = await fetch(`/location/geocode/${query}/${userCityName}/${userCityCoordinates}`);
        let data     = await source.json();
        return data;
      } catch (error) {
        return error;
      }
    },
    'keys': ['fullAddress'],
  },
  events: {
      input: {
          selection: (event) => {
              const selection = event.detail.selection.value;
              autoCompleteJS.input.value = selection.fullAddress;
              latitudeInput.value = selection.latitude;
              longitudeInput.value = selection.longitude;
              cityNameInput.value = selection.cityName;
          }
      }
  },
  debounce: 200,
  searchEngine: "loose",
  resultsList: {
    element: (list, data) => {
      if (!data.results.length) {
        const message = document.createElement("div");
        message.classList.add('autocomplete-no-result');
        message.textContent = `В вашем городе нет объектов с адресом "${data.query}"`;
        list.appendChild(message);
      }
    },
    noResults: true,
  }
}

const autoCompleteJS = new autoComplete(config);
