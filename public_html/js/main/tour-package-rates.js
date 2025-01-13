import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import TourPackageRatesService from "/js/client/TourPackageRatesService.js"; // Assuming you have a service for tour rates

const requestClient = new RequestClient();
const tourPackageRatesService = new TourPackageRatesService(requestClient);

window.saveRates = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    formData.append("tour_package_id", $("#tour_package").val());

    // Get min and max participants from your form or data attributes
    const minParticipants = parseInt($('#rateFieldsContainer').data('min-participants') || 1);
    const maxParticipants = parseInt($('#rateFieldsContainer').data('max-participants'));

    let rates = {};

    // Handle solo rate
    rates.solo = {
        rate: $('input[name="rate_solo_person[]"]').val(),
        rooms: $('input[name="rate_solo_person_room[]"]').val()
    };

    // Handle rates for groups
    for (let i = 2; i <= maxParticipants; i++) {
        rates[i] = {
            rate: $(`input[name="rate_${i}_people[]"]`).val(),
            rooms: $(`input[name="rate_${i}_people_rooms[]"]`).val()
        };
    }

    // Append the rates object as JSON
    formData.append("rates", JSON.stringify(rates));
    formData.append("quote_for_more_people", $("select[name='quote_for_more_people']").val());

    try {
        await tourPackageRatesService.save(formData);
        getExistingRates();
    } catch (error) {
        console.error("Error saving rates:", error);
    } finally {
        btn_loader(btn, false);
    }
};

window.getExistingRates = async function () {
    const tourPackageId = "{{ $tour_package->id }}"; // Make sure this is accessible
    try {
        const response = await tourPackageRatesService.fetchAll(tourPackageId);
        // Assuming you have a function to render these rates in the existing HTML structure
        renderRates(response);
    } catch (error) {
        console.error("Error fetching existing rates:", error);
    }
};

function renderRates(rates) {
    const ratesContainer = $(".card-body").find(".existing-rates-container");
    ratesContainer.empty(); // Clear existing content

    if (rates.length === 0) {
        ratesContainer.append("<p>No rates available for this tour package.</p>");
    } else {
        const list = $("<ul class='list-group'></ul>");
        rates.forEach(rate => {
            list.append(`
                <li class='list-group-item'>
                    <strong>Start Date:</strong> ${new Date(rate.start_date).toLocaleDateString()}<br>
                    <strong>Solo Rate:</strong> $${parseFloat(rate.solo_rate).toFixed(2)}<br>
                    <strong>Rate for 2 People:</strong> $${parseFloat(rate.rate_2_people).toFixed(2)}<br>
                    <strong>Quote for 7+ People:</strong> ${rate.quote_for_7_plus_people ? 'Yes' : 'No'}
                    <div class="mt-2">
                        <form action="{{ route('admin.tour-rates.destroy', ['tour_package_id' => $tour_package->id, 'rate_id' => '']) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </li>
            `);
        });
        ratesContainer.append(list);
    }
}
