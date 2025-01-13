import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import TourPackageItinerariesService from "/js/client/TourPackageItinerariesService.js";

const requestClient = new RequestClient();
const tourPackageItinerariesService = new TourPackageItinerariesService(requestClient);

window.getItineraries = async function () {
    const tourPackageId = $("#tour_package").val();
    try {
        const response = await itinerariesService.fetchAll(tourPackageId);
    } catch (error) {
        console.error("Error fetching existing itineraries:", error);
    }
};

window.saveItineraries = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    formData.append("tour_package_id", $("#tour_package_id").val());

    const itineraryElements = document.querySelectorAll(".itinerary-item");
    const itineraries = [];

    itineraryElements.forEach(item => {
        const itinerary = {
            day: item.querySelector('input[name="day[]"]').value,
            activity_title: item.querySelector('input[name="activity_title[]"]').value,
            description: item.querySelector('textarea[name="description[]"]').value,
            destination: item.querySelector('input[name="destination[]"]').value,
            image: item.querySelector('input[name="image[]"]').files[0],
            accommodation: item.querySelector('input[name="accommodation[]"]').value,
            accommodation_images: item.querySelector('input[name="accommodation_images[]"]').files
        };

        itineraries.push(itinerary);
    });

    itineraries.forEach((itinerary, index) => {
        formData.append(`itineraries[${index}][day]`, itinerary.day);
        formData.append(`itineraries[${index}][activity_title]`, itinerary.activity_title);
        formData.append(`itineraries[${index}][description]`, itinerary.description);
        formData.append(`itineraries[${index}][destination]`, itinerary.destination);
        if (itinerary.image) {
            formData.append(`itineraries[${index}][image]`, itinerary.image);
        }
        if (itinerary.accommodation) {
            formData.append(`itineraries[${index}][accommodation]`, itinerary.accommodation);
        }
        if (itinerary.accommodation_images.length) {
            Array.from(itinerary.accommodation_images).forEach((file, imgIndex) => {
                formData.append(`itineraries[${index}][accommodation_images][${imgIndex}]`, file);
            });
        }
    });

    try {
        await tourPackageItinerariesService.save(formData);
        getExistingItineraries();
    } catch (error) {
        console.error("Error saving itineraries:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.updateItineraries = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    formData.append("tour_package_id", $("#tour_package_id").val());
    formData.append("itinerary_id", $("#itinerary_id").val());

    const itinerary = {
        day: document.querySelector('input[name="day"]').value,
        activity_title: document.querySelector('input[name="activity_title"]').value,
        description: document.querySelector('textarea[name="description"]').value,
        destination: document.querySelector('input[name="destination"]').value,
        meals: document.querySelector('input[name="meals"]').value,
        drinks: document.querySelector('input[name="drinks"]').value,
        image: document.querySelector('input[name="image"]').files[0],
        accommodation: document.querySelector('input[name="accommodation"]').value,
        accommodation_images: document.querySelector('input[name="accommodation_images[]"]').files
    };

    formData.append("day", itinerary.day);
    formData.append("activity_title", itinerary.activity_title);
    formData.append("description", itinerary.description);
    formData.append("destination", itinerary.destination);
    formData.append("meals", itinerary.meals);
    formData.append("drinks", itinerary.drinks);
    if (itinerary.image) {
        formData.append("image", itinerary.image);
    }
    if (itinerary.accommodation) {
        formData.append("accommodation", itinerary.accommodation);
    }
    if (itinerary.accommodation_images.length) {
        Array.from(itinerary.accommodation_images).forEach((file, imgIndex) => {
            formData.append(`accommodation_images[${imgIndex}]`, file);
        });
    }

    try {
        await tourPackageItinerariesService.update(formData);
    } catch (error) {
        console.error("Error updating itinerary:", error);
    } finally {
        btn_loader(btn, false);
    }
};
