import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import MembershipPlansService from "/js/client/MembershipPlansService.js";

const requestClient = new RequestClient();
const membershipPlansService = new MembershipPlansService(requestClient);

window.getMembershipPlans = async function (page = 1) {
    try {
        let data = { page: page };
        const plansCard = await membershipPlansService.fetch(data);
        $("#membershipPlansContainer").html(plansCard);
    } catch (error) {
        console.error("Error loading membership plans:", error);
    }
};

window.saveMembershipPlan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();

    formData.append("name", $("#name").val());
    formData.append("price", $("#price").val());
    formData.append("max_requests_per_month", $("#max_requests_per_month").val());
    formData.append("priority_level", $("#priority_level").val());
    formData.append("commission_rate", $("#commission_rate").val());
    formData.append("support_included", $("#support_included").is(':checked'));
    formData.append("bid_limit", $("#bid_limit").val());
    formData.append("description", $("#description").val());

    try {
        await membershipPlansService.save(formData);
        getMembershipPlans();
    } finally {
        btn_loader(btn, false);
    }
};

// Update an existing membership plan
window.updateMembershipPlan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData();
    const planId = btn.data("id"); // Get the membership plan ID from button data attributes

    // Membership Plan Details
    formData.append("name", $("#name").val());
    formData.append("price", $("#price").val());
    formData.append("max_requests_per_month", $("#max_requests_per_month").val());
    formData.append("priority_level", $("#priority_level").val());
    formData.append("commission_rate", $("#commission_rate").val());
    formData.append("support_included", $("#support_included").is(':checked'));
    formData.append("bid_limit", $("#bid_limit").val());
    formData.append("description", $("#description").val());

    try {
        await membershipPlansService.update(planId, formData);
        getMembershipPlans(); // Refresh the list after updating
    } finally {
        btn_loader(btn, false);
    }
};

// Delete a membership plan
window.deleteMembershipPlan = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const planId = btn.data("id"); // Get the membership plan ID from button data attributes

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
                await membershipPlansService.delete(planId);
                getMembershipPlans(); // Refresh the list after deletion
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};
