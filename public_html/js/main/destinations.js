import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import DestinationsService from "/js/client/DestinationsService.js";

const requestClient = new RequestClient();
const destinationsService = new DestinationsService(requestClient);

window.getCountries = async function (page = 1) {
    try {
        const data = { page: page };
        const response = await destinationsService.countries(data);
        $("#countriesContainer").html(response);
        $(".nav-link").removeClass("active");
        if (country) {
            $("#" + country + "-tab").addClass("active");
        } else {
            $(".nav-link:first").addClass("active");
        }
    } catch (error) {
        console.error("Error loading countries:", error);
    }
};

window.editCountry = async function (country) {
    try {
        const data = { country: country };
        const response = await destinationsService.editCountry(data);
        $("#countriesFormContainer").html(response);
        $("#addCountryModal").modal("show");
    } catch (error) {
        console.error("Error loading countries:", error);
    }
};
window.saveCountry = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData();

    const country = document.getElementById("country").value;
    const continent = document.getElementById("continent").value;
    const countryImage = document.getElementById("country_image").files[0];

    formData.append("country", country);
    formData.append("continent", continent);

    if (countryImage) {
        formData.append("country_image", countryImage);
    }

    try {
        await destinationsService.saveCountry(formData);
    } catch (error) {
        console.error("Error saving country:", error);
    } finally {
        getCountries();
        btn_loader(btn, false);
    }
};

window.updateCountry = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData();

    const country_slug = document.getElementById("country_slug").value;
    const country = document.getElementById("country").value;
    const continent = document.getElementById("continent").value;
    const countryImage = document.getElementById("country_image").files[0];

    formData.append("country", country);
    formData.append("continent", continent);
    formData.append("country_slug", country_slug);

    if (countryImage) {
        formData.append("country_image", countryImage);
    }

    try {
        await destinationsService.updateCountry(formData);
    } catch (error) {
        console.error("Error updating country:", error);
    } finally {
        getCountries();
        btn_loader(btn, false);
    }
};

window.getDestinations = async function (page = 1, country = null) {
    try {
        const data = { page: page, country: country };
        const response = await destinationsService.fetch(data);
        $("#destinationsContainer").html(response);
        $(".nav-link").removeClass("active");
        if (country) {
            $("#" + country + "-tab").addClass("active");
        } else {
            $(".nav-link:first").addClass("active");
        }
    } catch (error) {
        console.error("Error loading destinations:", error);
    }
};

window.saveDestination = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const formData = new FormData();

    const country = document.getElementById("country").value;
    const name = document.getElementById("name").value;
    const description = document.getElementById("description").value;
    const destinationImage = document.getElementById("destination_image").files[0];

    formData.append("country", country);
    formData.append("name", name);
    formData.append("description", description);

    if (destinationImage) {
        formData.append("destination_image", destinationImage);
    }

    const highlightsData = [];

    $(".highlight-item").each(function(index) {
        const highlightName = $(this).find('[name^="highlights"]').first().val();
        const highlightDescription = $(this).find('[name^="highlights"]').eq(1).val();
        const highlightImages = $(this).find('[name^="highlights"]').last().prop("files");

        const highlight = {
            name: highlightName,
            description: highlightDescription,
            images: []
        };

        formData.append(`highlights[${index}][name]`, highlightName);
        formData.append(`highlights[${index}][description]`, highlightDescription);

        if (highlightImages.length > 0) {
            Array.from(highlightImages).forEach((file, fileIndex) => {
                formData.append(`highlights[${index}][images][]`, file);
                highlight.images.push(file.name);
            });
        }

        highlightsData.push(highlight);
    });

    try {
        await destinationsService.save(formData);
    } catch (error) {
        console.error("Error saving destination:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.updateDestination = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    formData.append("destination_slug", $("#destination_slug").val());
    formData.append("country", $("#country").val());
    formData.append("name", $("#name").val());
    formData.append("description", $("#description").val());
    const destinationImage = document.getElementById("destination_image").files[0];

    if (destinationImage) {
        formData.append("destination_image", destinationImage);
    }

    // Attach highlights for update
    $("#highlights-container .highlight-item").each(function (index, element) {
        const name = $(element).find("input[name*='[name]']").val();
        const description = $(element)
            .find("textarea[name*='[description]']")
            .val();
        const images = $(element).find("input[name*='[images][]']")[0].files;
        const id = $(element).find("input[name*='[id]']").val(); // Get highlight ID

        // If the highlight was marked for removal, include it in the deletion list
        if ($(element).hasClass('highlight-removed')) {
            formData.append(`highlights[${index}][id]`, id); // Include highlight ID for deletion
            formData.append(`highlights[${index}][delete]`, true); // Mark it for deletion
        } else {
            formData.append(`highlights[${index}][name]`, name);
            formData.append(`highlights[${index}][description]`, description);
            for (let i = 0; i < images.length; i++) {
                formData.append(`highlights[${index}][images][]`, images[i]);
            }
        }
    });

    try {
        await destinationsService.update(formData);
    } catch (error) {
        console.error("Error updating destination:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.deleteCountry = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const countrySlug = btn.data("country-slug");
    const data = { country: countrySlug };

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await destinationsService.deleteCountry(data);
                getCountries();
            } catch (error) {
                console.error("Error deleting country:", error);
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
window.deleteDestination = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const destinationSlug = btn.data("destination-slug");
    const data = { destination_slug: destinationSlug };

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#068f6d",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await destinationsService.delete(data);
                getDestinations();
            } catch (error) {
                console.error("Error deleting destination:", error);
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
