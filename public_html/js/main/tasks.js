import { btn_loader } from "/js/client/config.js";
import RequestClient from "/js/client/RequestClient.js";
import TasksService from "/js/client/TasksService.js";

const requestClient = new RequestClient();
const tasksService = new TasksService(requestClient);

window.getTasks = async function (page = 1) {
    try {
        let data = { page: page };
        const tasksCards = await tasksService.fetch(data);
        $("#tasksContainer").html(tasksCards);
    } catch (error) {
        console.error("Error loading tasks:", error);
    }
};

window.saveTask = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("tasksForm"));
    let selectedEmployees = $("#employee_ids").val();

    if (selectedEmployees) {
        selectedEmployees.forEach(id => formData.append("employee_ids[]", id));
    }

    try {
        if (formData.has("task_slug")) {
            await tasksService.update(formData);
        } else {
            await tasksService.save(formData);
        }
        getTasks();
    } finally {
        btn_loader(btn, false);
    }
};

window.editTask = async function (btn) {
    btn = $(btn);
    const task = btn.data("task");
    const data = { task_id: task };

    try {
        const form = await tasksService.edit(data);
        $('#tasksFormContainer').html(form);
    } finally {
    }
};

window.deleteTask = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    const task = btn.data("task");
    const data = { task_slug: task };

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
                await tasksService.delete(data);
                getTasks();
            } finally {
                btn_loader(btn, false);
            }
        } else {
            btn_loader(btn, false);
        }
    });
};

window.updateProgress = async function (btn) {
    btn = $(btn);
    btn_loader(btn, true);

    let formData = new FormData(document.getElementById("updateProgressForm"));

    try {
        await tasksService.progress(formData);
    } finally {
        btn_loader(btn, false);
    }
};

window.timelines = async function (task) {
    const data = { task_slug: task };

    try {
        const timelines = await tasksService.timelines(data);
        $('#timelinesContainer').html(timelines);
    } finally {
    }
};




// import { btn_loader } from "/js/client/config.js";
// import RequestClient from "/js/client/RequestClient.js";
// import TasksService from "/js/client/TasksService.js";

// const requestClient = new RequestClient();
// const tasksService = new TasksService(requestClient);

// window.getTasks = async function (page = 1) {
//     try {
//         let data = { page: page };
//         const tasksCards = await tasksService.fetch(data);
//         $("#tasksContainer").html(tasksCards);
//     } catch (error) {
//         console.error("Error loading tasks:", error);
//     }
// };

// window.saveTask = async function (btn) {
//     btn = $(btn);
//     btn_loader(btn, true);

//     let formData = new FormData(document.getElementById("tasksForm"));

//     try {
//         if (formData.has("task_slug")) {
//             await tasksService.update(formData);
//         } else {
//             await tasksService.save(formData);
//         }
//         getTasks();
//     } finally {
//         btn_loader(btn, false);
//     }
// };

// window.updateProgress = async function (btn) {
//     btn = $(btn);
//     btn_loader(btn, true);

//     alert('sammy')

//     let formData = new FormData(document.getElementById("updateProgressForm"));

//     try {
//         await tasksService.progress(formData);
//     } finally {
//         btn_loader(btn, false);
//     }
// };

// window.editTask = async function (btn) {
//     btn = $(btn);
//     const task = btn.data("task");
//     const data = { task_slug: task };

//     try {
//         const form = await tasksService.edit(data);
//         $('#tasksFormContainer').html(form);
//         initializeDatepicker()
//     } finally {
//     }
// };

// window.timelines = async function (task) {
//     const task = btn.data("task");
//     const data = { task_slug: task };

//     try {
//         const timelines = await tasksService.timelines(data);
//         $('#timelinesContainer').html(timelines);
//     } finally {
//     }
// };

// window.deleteTask = async function (btn) {
//     btn = $(btn);
//     btn_loader(btn, true);

//     const task = btn.data("task");
//     const data = { task_slug: task };

//     Swal.fire({
//         title: "Are you sure?",
//         text: "You won't be able to revert this!",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonColor: "#068f6d",
//         cancelButtonColor: "#d33",
//         confirmButtonText: "Yes, delete it!",
//     }).then(async (result) => {
//         if (result.isConfirmed) {
//             try {
//                 await tasksService.delete(data);
//                 getTasks();
//             } finally {
//                 btn_loader(btn, false);
//             }
//         } else {
//             btn_loader(btn, false);
//         }
//     });
// };
