(function ($) {
	"use strict";
	var windowOn = $(window);

	/*======================================
	Preloader activation
	========================================*/
	$(window).on('load', function (event) {
		$('#preloader').delay(500).fadeOut(500);
	});

	/*======================================
	Isotope Js
	========================================*/

	// init Isotope
	var $grid = $('.isotope-grid').isotope({
		itemSelector: '.isotope-item',
		layoutMode: 'fitRows'
	});
	// filter functions
	var filterFns = {
		// show if number is greater than 50
		numberGreaterThan50: function () {
			var number = $(this).find('.number').text();
			return parseInt(number, 10) > 50;
		},
		// show if name ends with -ium
		ium: function () {
			var name = $(this).find('.name').text();
			return name.match(/ium$/);
		}
	};
	// bind filter on select change
	$('#filters-select').on('change', function () {
		// get filter value from option value
		var filterValue = this.value;
		// use filterFn if matches value
		filterValue = filterFns[filterValue] || filterValue;
		$grid.isotope({ filter: filterValue });
	});

	/*======================================
	smoothSctollTop js
	========================================*/
	function smoothSctollTop() {
		$('.smooth a').on('click', function (event) {
			var target = $(this.getAttribute('href'));
			if (target.length) {
				event.preventDefault();
				$('html, body').stop().animate({
					scrollTop: target.offset().top - 100
				}, 1000);
			}
		});
	}
	smoothSctollTop();

	/* footer year */
	var yearElement = document.getElementById("year");
	if (yearElement) { yearElement.innerHTML = new Date().getFullYear(); }
	/* footer year */

	/* Body overlay Js */
	$(".body-overlay").on("click", function () {
		$(".offcanvas__area").removeClass("opened");
		$(".body-overlay").removeClass("opened");
	});

	/* Sticky Header Js */
	var lastScrollTop = 200;
	$(window).scroll(function (event) {
		var scroll = $(this).scrollTop();
		if (scroll > lastScrollTop) {
			$('#header-sticky').removeClass('sticky');
		} else {
			$('#header-sticky').addClass('sticky');
		}

		if (scroll < 200) {
			$("#header-sticky").removeClass("sticky");
		}
		lastScrollTop = scroll;
	});

	/* Data Css js */
	$("[data-background").each(function () {
		$(this).css("background-image", "url( " + $(this).attr("data-background") + "  )");
	});

	$("[data-width]").each(function () {
		$(this).css("width", $(this).attr("data-width"));
	});

	$("[data-bg-color]").each(function () {
		$(this).css("background-color", $(this).attr("data-bg-color"));
	});

	/* Cart Quantity Js */
	$(".cart-minus").click(function () {
		var $input = $(this).parent().find("input");
		var count = parseInt($input.val()) - 1;
		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		return false;
	});

	/* MagnificPopup image view */
	$('.popup-image').magnificPopup({
		type: 'image',
		gallery: {
			enabled: true
		}
	});

	/* MagnificPopup video view */
	$(".popup-video").magnificPopup({
		type: "iframe",
	});

	/* settings append in body Js */
	function bd_settings_append($x) {
		var settings = $('body');
		let dark;
		$x === true ? dark = 'd-block' : dark = 'd-none';
		/* no need switcher then add 'd-none' */
		var settings_html = `<div class="bd-theme-settings-area transition-3">
		<div class="bd-theme-wrapper">
		<div class="bd-theme-header text-center">
		   <h4 class="bd-theme-header-title">App Settings</h4>
		</div>

		<!-- THEME TOGGLER -->
		<div class="bd-theme-toggle mb-20 ${dark}">
		   <label class="bd-theme-toggle-main" for="bd-theme-toggler">
		   <span class="bd-theme-toggle-dark"><i class="fa-light fa-moon"></i> Dark </span>
				 <input type="checkbox" id="bd-theme-toggler">
				 <i class="bd-theme-toggle-slide"></i>
				 <span class="bd-theme-toggle-light active"><i class="fa-light fa-sun-bright"></i> Light</span>
		   </label>
		</div>

		<div class="bd-theme-settings">
		   <div class="bd-theme-settings-wrapper">
			  <div class="bd-theme-settings-open">
				 <button class="bd-theme-settings-open-btn">
					<span class="bd-theme-settings-gear">
					   <i class="fa-light fa-gear"></i>
					</span>
					<span class="bd-theme-settings-close">
					   <i class="fa-regular fa-xmark"></i>
					</span>
				 </button>
			  </div>
		   </div>
		</div>
	 </div>
		 </div>`;
		settings.append(settings_html);
	}

	function bd_rtl_settings() {
		$('#bd-dir-toggler').on("change", function () {
			toggle_rtl();
			location.reload(true);
		});

		function bd_set_scheme(bd_dir) {
			localStorage.setItem('bd_dir', bd_dir);
			document.documentElement.setAttribute("dir", bd_dir);

			var list = $("[href='assets/vendor/css/bootstrap.min.css']");
			$(list).attr("href", bd_dir === 'rtl' ? "assets/vendor/css/bootstrap.rtl.min.css" : "assets/vendor/css/bootstrap.min.css");
		}

		function toggle_rtl() {
			if (localStorage.getItem('bd_dir') === 'rtl') {
				bd_set_scheme('ltr'); /* change ltr to rtl */
			} else {
				bd_set_scheme('rtl');
			}
		}

		function bd_init_dir() {
			var savedDir = localStorage.getItem('bd_dir');
			bd_set_scheme(savedDir || 'ltr'); /* change ltr to rtl */
			document.getElementById('bd-dir-toggler').checked = savedDir === 'rtl';
		}

		bd_init_dir();
	}

	function bd_theme_toggler() {
		$('#bd-theme-toggler').on("change", function () {
			toggleTheme();
		});

		function bd_set_scheme(bd_theme) {
			localStorage.setItem('bd_theme_scheme', bd_theme);
			document.documentElement.setAttribute("bd-theme", bd_theme);
		}

		function toggleTheme() {
			var currentTheme = localStorage.getItem('bd_theme_scheme');
			bd_set_scheme(currentTheme === 'bd-theme-light' ? 'bd-theme-dark' : 'bd-theme-light');
		}

		function bd_init_theme() {
			var savedTheme = localStorage.getItem('bd_theme_scheme');
			bd_set_scheme(savedTheme || 'bd-theme-light'); /* change bd-theme-light to bd-theme-dark */
			document.getElementById('bd-theme-toggler').checked = savedTheme !== 'bd-theme-light';
		}

		bd_init_theme();
	}
	/* Append settings HTML  */
	bd_settings_append(true); /* if you want to enable dark mode, send "true" */

	/* Event listeners  */
	$(".bd-theme-settings-open-btn").on("click", function () {
		$(".bd-theme-settings-area").toggleClass("settings-opened");
	});

	/* Initialize RTL settings if the element is present  */
	if ($("#bd-dir-toggler").length > 0) {
		bd_rtl_settings();
	}

	/* Initialize dark/light mode toggler if the element is present  */
	if ($("#bd-theme-toggler").length > 0) {
		bd_theme_toggler();
	}

	var bd_rtl = localStorage.getItem('bd_dir');
	let rtl_setting = bd_rtl === 'rtl' ? true : false;


	/* Tooltip Activation Js */
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	/* Parallax js */
	var b = document.getElementsByTagName("BODY")[0];
	b.addEventListener("mousemove", function (event) {
		parallaxed(event);

	});
	function parallaxed(e) {
		var amountMovedX = (e.clientX * -0.3 / 8);
		var amountMovedY = (e.clientY * -0.3 / 8);
		var x = document.getElementsByClassName("parallaxed");
		var i;
		for (i = 0; i < x.length; i++) {
			x[i].style.transform = 'translate(' + amountMovedX + 'px,' + amountMovedY + 'px)'
		}
	}

	/* Sidebar js */

	$("#sidebar__active").on("click", function () {
		if (window.innerWidth > 0 && window.innerWidth <= 1199) {
			$(".app-sidebar").toggleClass("close_sidebar");
		} else {
			$(".app-sidebar").toggleClass("collapsed");
		}
		$(".app__offcanvas-overlay").toggleClass("overlay-open");
	});

	$(".app__offcanvas-overlay").on("click", function () {
		$(".app-sidebar").removeClass("collapsed");
		$(".app-sidebar").removeClass("close_sidebar");
		$(".app__offcanvas-overlay").removeClass("overlay-open");
	});


	/* Map js */
	if (jQuery("#map").length > 0) {
		var markers = [
			{ coords: [37.7749, -122.4194] },
			{ coords: [51.1657, 10.4515] },
			{ coords: [-30.5595, 22.9375] },
			{ coords: [36.2048, 138.2529] },
		];

		/* Update the color property for each marker in a single iteration  */
		markers.forEach((marker, index) => {
			/* Change to the desired color code for each marker  */
			if (index === 0) {
				marker.color = "var(--clr-theme-primary)";
			} else if (index === 1) {
				marker.color = "var(--clr-theme-secondary)";
			} else if (index === 2) {
				marker.color = "var(--clr-action-success)";
			} else if (index === 3) {
				marker.color = "var(--clr-alert-danger)";
			}
			marker.style = {
				fill: marker.color,
				r: 15, /* Radius  */
				"stroke-width": 10, /* Stroke width */
				"fill-opacity": 1 /* Fill opacity */
			};
		});

		var jvm = new jsVectorMap({
			map: "world_merc",
			selector: "#map",
			zoomButtons: false,
			zoomOnScroll: false,
			regionStyle: {
				initial: {
					fill: "#C6C6C6"
				},
				selected: {
					fill: "#ff5050" // Color for the selected region
				}
			},
			labels: {
				markers: {
					render: (marker) => marker.name,
				}
			},
			markers: markers.map(marker => ({
				...marker,
				latLng: marker.coords,
				style: marker.style // Use the updated style
			})),
			markerLabelStyle: {
				initial: {
					fontFamily: "'Manrope', sans-serif",
					fontWeight: 400,
					fontSize: 14,
					fill: 'var(--clr-chart-2)'
				}
			},
		});
	}

	/* Scrollbar js */
	var Scrollbar = window.Scrollbar;
	const customizeOptions = {
		'damping': 0.1,
		'thumbMinSize': 5,
		renderByPixels: true,
		alwaysShowTracks: false,
	}
	$(".card__scroll").map(function (i, element) {
		Scrollbar.init(element)
	})

	/* Notify dropdown Js */
	$("#notifydropdown").on("click", function () {
		$(".notification__dropdown").toggleClass("notifydropdown-enable");
		$(".body__overlay").toggleClass("notifydropdown-enable");
		$(".email__dropdown").removeClass("email-enable");
		$(".user__dropdown").removeClass("user-enable");
		$(".lang__dropdown").removeClass("lang-enable");

	});
	$(".body__overlay").on("click", function () {
		$(".notification__dropdown").removeClass("notifydropdown-enable");
		$(".body__overlay").removeClass("notifydropdown-enable");
	});

	/* Email dropdown Js */

	$("#emaildropdown").on("click", function () {
		$(".email__dropdown").toggleClass("email-enable");
		$(".body__overlay").toggleClass("email-enable");
		$(".user__dropdown").removeClass("user-enable");
		$(".lang__dropdown").removeClass("lang-enable");
		$(".notification__dropdown").removeClass("notifydropdown-enable");
	});
	$(".body__overlay").on("click", function () {
		$(".email__dropdown").removeClass("email-enable");
		$(".body__overlay").removeClass("email-enable");

	});

	/* User dropdown Js */
	$("#userportfolio").on("click", function () {
		$(".user__dropdown").toggleClass("user-enable");
		$(".body__overlay").toggleClass("user-enable");
		$(".notification__dropdown").removeClass("notifydropdown-enable");
		$(".email__dropdown").removeClass("email-enable");
		$(".lang__dropdown").removeClass("lang-enable");
	});
	$(".body__overlay").on("click", function () {
		$(".user__dropdown").removeClass("user-enable");
		$(".body__overlay").removeClass("user-enable");
	});

	/* lang dropdown Js */
	$("#langdropdown").on("click", function () {
		$(".lang__dropdown").toggleClass("lang-enable");
		$(".body__overlay").toggleClass("lang-enable");
		$(".notification__dropdown").removeClass("notifydropdown-enable");
		$(".email__dropdown").removeClass("email-enable");
		$(".user__dropdown").removeClass("user-enable");
	});
	$(".body__overlay").on("click", function () {
		$(".lang__dropdown").removeClass("lang-enable");
		$(".body__overlay").removeClass("lang-enable");
	});


	/* Dropdown action  js */
	$(".dropdown").click(function () {
		$(this).find(".dropdown-list").fadeToggle(100);
	});
	$(document).on("click", function (event) {
		var $trigger = $(".dropdown");
		if ($trigger !== event.target && !$trigger.has(event.target).length) {
			$(this).find(".dropdown-list").fadeOut(100);
		}
	});

	/* email filter btn Js */
	$(document).ready(function () {
		$(".email__sidebar .email__toggle-btn").on("click", function (e) {
			e.stopPropagation(); /* Prevents the event from reaching the document and closing the sidebar */
			$(".email__sidebar .email__left-side").toggleClass("open");
		});

		$(".app__slide-wrapper").on("click", function (e) {
			e.stopPropagation(); /* Prevents the event from reaching the document and reopening the sidebar */
			$(".email__sidebar .email__left-side").removeClass("open");
		});

		/* Close the sidebar if clicking anywhere else on the document */
		$(document).on("click", function () {
			$(".email__sidebar .email__left-side").removeClass("open");
		});
	});

	/*  tinymce text editor */
	tinymce.init({
		selector: 'tinymce_textarea',
		toolbar: 'undo redo blockquote blocks bold italic alignleft aligncenter alignright outdent indent code anchor link restoredraft charmap codesample ltr rtl emoticons fullscreen help image insertdatetime lists media nonbreaking pagebreak preview save searchreplace template visualblocks visualchars wordcount accordion print',
		toolbar_mode: 'wrap',
		plugins: ['code', 'table', 'lists', 'anchor', 'autolink', "autosave", "charmap", "codesample", "directionality", "emoticons", "fullscreen", "help", "image", "importcss", "insertdatetime", "visualblocks", "visualchars", "wordcount", "accordion",],
		link_default_target: '_blank',
		quickbars_insert_toolbar: false,
		height: "200"
	});
	tinymce.init({
		selector: '#tinymce_textarea_form',
		toolbar: "undo redo aligncenter alignjustify alignleft alignnone alignright| anchor | blockquote blocks | backcolor | bold | copy | cut | fontfamily fontsize forecolor h1 h2 h3 h4 h5 h6 hr indent | italic | language | lineheight | newdocument | outdent | paste pastetext | print | remove removeformat | selectall | strikethrough |  | subscript superscript underline | visualaid | a11ycheck advtablerownumbering typopgraphy anchor restoredraft casechange charmap checklist code codesample addcomment showcomments ltr rtl editimage fliph flipv imageoptions rotateleft rotateright emoticons export footnotes footnotesupdate formatpainter fullscreen help image insertdatetime link openlink unlink bullist numlist media mergetags mergetags_list nonbreaking pagebreak pageembed permanentpen preview quickimage quicklink quicktable cancel save searchreplace spellcheckdialog spellchecker | table tablecellprops tablecopyrow tablecutrow tabledelete tabledeletecol tabledeleterow tableinsertdialog tableinsertcolafter tableinsertcolbefore tableinsertrowafter tableinsertrowbefore tablemergecells tablepasterowafter tablepasterowbefore tableprops tablerowprops tablesplitcells tableclass tablecellclass tablecellvalign tablecellborderwidth tablecellborderstyle tablecaption tablecellbackgroundcolor tablecellbordercolor tablerowheader tablecolheader | tableofcontents tableofcontentsupdate | template typography | insertfile | visualblocks visualchars | wordcount",
		plugins: ['code', 'table', 'lists', 'anchor', 'autolink', "autosave", "charmap", "codesample", "directionality", "emoticons", "fullscreen", "help", "image", "importcss", "insertdatetime", "link", "media", "nonbreaking", "pagebreak", "preview", "quickbars", "save", "searchreplace"],
		font_family_formats: 'Manrope=manrope,Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats',
		toolbar_mode: 'wrap',
	});
	tinymce.init({
		selector: '#tinymce_simple_textarea',
		toolbar: 'undo redo blockquote blocks bold italic alignleft aligncenter alignright outdent indent code anchor link restoredraft charmap codesample ltr rtl emoticons fullscreen help image insertdatetime lists media nonbreaking pagebreak preview save searchreplace template visualblocks visualchars wordcount accordion print',
		toolbar_mode: 'wrap',
		plugins: ['code', 'table', 'lists', 'anchor', 'autolink', "autosave", "charmap", "codesample", "directionality", "emoticons", "fullscreen", "help", "image", "importcss", "insertdatetime", "visualblocks", "visualchars", "wordcount", "accordion"],
		link_default_target: '_blank',
		quickbars_insert_toolbar: false,
		height: "300"
	});

	/* Dropzone Activation */
	Dropzone.options.myDropzone = {
		url: "/fake/location",
		autoProcessQueue: false,
		paramName: "file",
		clickable: true,
		maxFilesize: 5000,
		maxFilesQuantity: 3,
		addRemoveLinks: true,
		acceptedFiles: '.png,.jpg,.zip',
		dictDefaultMessage: "Upload your file here",
		init: function () {
			this.on("sending", function (file, xhr, formData) {
				console.log("Sending file");
			});

			this.on("success", function (file, responseText) {
				console.log('Great success');
			});

			this.on("addedfile", function (file) {
				console.log('File added');
			});

			this.on("complete", function (file) {
				console.log('Upload complete for: ' + file.name);
			});
		}
	};
	$(document).ready(function () {
		/* Check if Dropzone container exists */
		if ($("#singleFile").length > 0) {
			/* Initialize Dropzone */
			Dropzone.options.myDropzone = {
				url: "/fake/location",
				autoProcessQueue: false,
				paramName: "file",
				clickable: true,
				addRemoveLinks: true,
				acceptedFiles: '.png, .jpg, .zip',
				dictDefaultMessage: "Upload your file here",
				fileTypeValidationRegex: null,
				maxImageHeight: null,
				maxImageWidth: null,
				maxFilesQuantity: 1,
				maxFileSize: 8,
				minImageHeight: 100,
				minImageWidth: 100,

				init: function () {
					/* Event handlers */
					this.on("sending", function (file, xhr, formData) {
						console.log("Sending file");
					});

					this.on("success", function (file, responseText) {
						console.log('Great success');
					});

					this.on("addedfile", function (file) {
						console.log('File added');
					});

					this.on("complete", function (file) {
						console.log('Upload complete for: ' + file.name);
					});
				}
			};
		}
	});
	if ($(".dropify").length) {
		$(".dropify").dropify();
	}

	/* content hidden class js */
	$('.contentHidden').remove();

	/* password hidden class js */
	/* password show and remember */
	document.addEventListener('DOMContentLoaded', function () {
		/* Password show */
		const triggerPasswords = document.querySelectorAll('#passwordToggle, #passwordToggle2');
		if (triggerPasswords.length > 0) {
			triggerPasswords.forEach(triggerPassword => {
				triggerPassword.addEventListener('click', () => {
					const passwordInputId = triggerPassword.id === 'passwordToggle' ? 'passwordInput' : 'passwordInput2';
					const passwordInput = document.getElementById(passwordInputId);
					if (passwordInput.type === 'password') {
						passwordInput.type = 'text';
						triggerPassword.innerHTML = '<i class="fa-sharp fa-light fa-eye"></i>';
					} else {
						passwordInput.type = 'password';
						triggerPassword.innerHTML = '<i class="fa-sharp fa-light fa-eye-slash"></i>';
					}
				});
			});
		}

		/* Remember me */
		const rmCheck = document.getElementById('rememberMe');
		const emailInput = document.getElementById('email');

		if (rmCheck && emailInput) {
			if (localStorage.checkbox && localStorage.checkbox !== "") {
				rmCheck.checked = true;
				emailInput.value = localStorage.username;
			}
			rmCheck.addEventListener('change', function () {
				if (rmCheck.checked && emailInput.value !== "") {
					localStorage.username = emailInput.value;
					localStorage.checkbox = rmCheck.checked;
				} else {
					localStorage.username = "";
					localStorage.checkbox = "";
				}
			});
		}
	});


	// In your Javascript (external .js resource or <script> tag) */
	$(document).ready(function () {
		$('.js-example-basic-single').select2();
	});
	$(document).ready(function () {
		$('.js-example-basic-singles').select2();
	});
	// Multi Select */
	$(document).ready(function () {
		$(".js-example-basic-multiple").select2();
	});
	// With Placeholder */
	$(".js-example-placeholder-multiple").select2({
		placeholder: "Select Your State"
	});
	//Limited Numbers */
	$(".js-example-basic-multiple-limit").select2({
		maximumSelectionLength: 3
	});

	$(".js-example-disabled").select2();
	$(".js-example-disabled-multi").select2();

	$(".js-programmatic-enable").on("click", function () {
		$(".js-example-disabled").prop("disabled", false);
		$(".js-example-disabled-multi").prop("disabled", false);
	});

	$(".js-programmatic-disable").on("click", function () {
		$(".js-example-disabled").prop("disabled", true);
		$(".js-example-disabled-multi").prop("disabled", true);
	});

	// Single Search Select */
	$(".js-example-basic-single").select2();
	$(".js-example-disabled-results").select2({});

	//RTL Suppoort */
	$(".js-example-rtl").select2({
		dir: "rtl"
	});

	// Responsive width Search Select
	$(".js-example-basic-hide-search").select2({
		minimumResultsForSearch: Infinity
	});

	$(".js-example-disabled").select2({
		disabled: true
	});

	$(".js-programmatic-enable").on("click", function () {
		$(".js-example-disabled").prop("disabled", false);
	});
	$(".js-programmatic-disable").on("click", function () {
		$(".js-example-disabled").prop("disabled", true);
	});

	$(".product-status").select2({
		minimumResultsForSearch: Infinity
	});

	$(document).ready(function () {
		if ($('.multiple-img').length > 0) {
			function formatState(state) {
				if (!state.id) { return state.text; }
				var $state = $(
					'<span><img src="' + $(state.element).attr('data-image') + '" class="img-flag border-circle" / " width="22px"> ' + state.text + '</span>'
				);
				return $state;
			};
			$('.multiple-img').each(function () {
				$(this).select2({
					minimumResultsForSearch: Infinity,
					templateResult: formatState,
					templateSelection: formatState
				});
			});
		}
	});

	// js-example-basic-hide-search
	$("#js-example-basic-hide-search").select2({
		minimumResultsForSearch: Infinity
	});
	// border activation Js

	$('.addBorder').on('click', function () {
		$('.checked.addBorder').removeClass('checked');
		$(this).addClass('checked');
	});

	// flatpickr activation
	$("#joiningDate").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});

	$("#clockInTime").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		defaultDate: "01:45"
	});

	$("#clockOutTime").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		defaultDate: "01:45"
	});

	function basicInput(containerId) {
		$(`#${containerId}`).flatpickr({
			dateFormat: "Y-m-d",
		});
	}
	basicInput("basicInput");
	basicInput("basicInput2");
	basicInput("basicInput3");
	basicInput("basicInput4");
	basicInput("basicInput5");
	basicInput("basicInput6");

	$("#dateTime").flatpickr({
		enableTime: true,
		dateFormat: "Y-m-d H:i",
	});

	$("#humanFriendlyDates").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});

	$("#humanFriendlyDate2").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#startingDate").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#startingDate2").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#startingDate3").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#completeDate").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#completeDate2").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#completeDate3").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#periodTo").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#periodTo2").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#periodTo3").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#periodFrom").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#periodFrom2").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#periodFrom3").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#issueDate").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#expiryDate").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#purchaseDate").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#purchaseDate2").flatpickr({
		altInput: true,
		altFormat: "F j, Y",
		dateFormat: "Y-m-d",
	});
	$("#loanDate").flatpickr({
		altInput: true,
		altFormat: "F, Y",
		dateFormat: "Y-m",
	});
	$("#loanDate").flatpickr({
		altInput: true,
		altFormat: "F, Y",
		dateFormat: "Y-m",
	});
	$("#dateDuration").flatpickr({
		mode: "range",
		altInput: true,
		dateFormat: "m-Y",
	});
	$("#minimumDates").flatpickr({
		minDate: "2022-01"
	});
	$("#maximumDates").flatpickr({
		dateFormat: "d.m.Y",
		maxDate: "21.02.2024"
	});
	$("#miniMaxDates").flatpickr({
		minDate: "today",
		maxDate: new Date().fp_incr(14)
	});
	$("#disablingSpecificDates").flatpickr({
		disable: ["2024-01-14", "2023-12-15", "2025-03-08", new Date(2025, 4, 9)],
		dateFormat: "Y-m-d",
	});
	$("#selectingMultipleDates").flatpickr({
		mode: "multiple",
		dateFormat: "Y-m-d"
	});
	$("#preloadingMultipleDates").flatpickr({
		mode: "multiple",
		dateFormat: "Y-m-d",
		defaultDate: ["2023-12-16", "2023-12-17"]
	});
	$("#rangeCalendar").flatpickr({
		mode: "range"
	});
	$("#preloadingRangeCalendar").flatpickr({
		mode: "range",
		dateFormat: "Y-m-d",
		defaultDate: ["2023-12-11", "2023-12-16"]
	});
	$("#timePicker").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
	});
	$("#timePicker24").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		time_24hr: true
	});
	$("#preloadingTimePicker").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		defaultDate: "01:45"
	});
	$("#meetingTime").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "h:i K",
		time_24hr: false,
	});
	$("#meetingTime2").flatpickr({
		enableTime: true,
		noCalendar: true,
		dateFormat: "h:i K",
		time_24hr: false,
	});
	$("#displayWeekNumbers").flatpickr({
		wrap: true,
		weekNumbers: true,
	});
	$("#functionDate").flatpickr({
		enable: [
			function (date) {
				// return true to enable
				return (date.getMonth() % 2 === 0 && date.getDate() < 15);
			}
		]
	});
	$("#functionDisablingDate").flatpickr({
		"disable": [
			function (date) {
				// return true to disable
				return (date.getDay() === 0 || date.getDay() === 6);

			}
		],
		"locale": {
			"firstDayOfWeek": 1 // start week on Monday
		}
	});
	$("#inlineCalendar").flatpickr({
		inline: true
	});

	// jQuery time picker
	$(function () {
		$('#basicExample').timepicker();
	});
	$(function () {
		$('#scrollDefaultExample').timepicker({ 'scrollDefault': 'now' });
	});
	$(function () {
		$('#setTimeExample').timepicker();
		$('#setTimeButton').on('click', function () {
			$('#setTimeExample').timepicker('setTime', new Date());
		});
	});
	$(function () {
		$('#durationExample').timepicker({
			'minTime': '2:00pm',
			'maxTime': '11:30pm',
			'showDuration': true
		});
	});
	$(function () {
		$('#onselectExample').timepicker();
		$('#onselectExample').on('changeTime', function () {
			$('#onselectTarget').text($(this).val());
		});
	});
	$(function () {
		$('#disableTimeRangesExample').timepicker({
			'disableTimeRanges': [
				['1am', '2am'],
				['3am', '4:01am']
			]
		});
	});
	$(function () {
		$('#noneOptionExample').timepicker({
			'noneOption': [{
				'label': 'Foobar',
				'className': 'shibby',
				'value': 'oh hai'
			},
				'Foobar2'
			]
		});
	});
	$(function () {
		$('#timeformatExample1').timepicker({ 'timeFormat': 'H:i:s' });
		$('#timeformatExample2').timepicker({ 'timeFormat': 'h:i A' });
	});
	$(function () {
		$('#stepExample1').timepicker({ 'step': 15 });
		$('#stepExample2').timepicker({
			'step': function (i) {
				return (i % 2) ? 15 : 45;
			}
		});
	});
	$(function () {
		$('#roundTimeExample').timepicker({ 'forceRoundTime': true });
	});

	// cleave js activation start
	function cardMonthYear(containerId) {
		if (jQuery(`#${containerId}`).length > 0) {
			var dateCleave = new Cleave(`#${containerId}`, {
				date: true,
				datePattern: ['m', 'y']
			});
		}
	}
	cardMonthYear("cardmmyy");
	cardMonthYear("cardmmyy2");

	function cardCvvCode(containerId) {
		if (jQuery(`#${containerId}`).length > 0) {
			var cvvCleave = new Cleave(`#${containerId}`, {
				delimiter: '',
				blocks: [3],
			});
		}
	}
	cardCvvCode("cvvcode");
	cardCvvCode("cvvcode2");

	if (jQuery("#creditCard").length > 0) {
		var cleave = new Cleave('#creditCard', {
			creditCard: true,
			onCreditCardTypeChanged: function (type) {
				var creditCardLogo = document.getElementById('creditCardLogo',);
				switch (type) {
					case 'visa':
						creditCardLogo.className = 'fa-brands fa-cc-visa'; // FontAwesome class for Visa
						break;
					case 'mastercard':
						creditCardLogo.className = 'fa-brands fa-cc-mastercard'; // FontAwesome class for MasterCard
						break;
					case 'amex':
						creditCardLogo.className = 'fa-brands fa-cc-amex'; // FontAwesome class for American Express
						break;
					case 'discover':
						creditCardLogo.className = 'fa-brands fa-cc-discover'; // FontAwesome class for Discover
						break;
					case 'jcb':
						creditCardLogo.className = 'fa-brands fa-cc-jcb'; // FontAwesome class for Discover
						break;
					case 'diners':
						creditCardLogo.className = 'fa-brands fa-cc-diners-club'; // FontAwesome class for Discover
						break;
					default:
						creditCardLogo.className = ''; // Clear the class if no matching type is found
						break;
				}
			}
		});
	}

	if (jQuery("#creditCard2").length > 0) {
		var cleave = new Cleave('#creditCard2', {
			creditCard: true,
			onCreditCardTypeChanged: function (type) {
				var creditCardLogo2 = document.getElementById('creditCardLogo2');
				switch (type) {
					case 'visa':
						creditCardLogo2.className = 'icon-visa'; // FontAwesome class for Visa
						break;
					case 'mastercard':
						creditCardLogo2.className = 'icon-mastercard'; // FontAwesome class for MasterCard
						break;
					case 'amex':
						creditCardLogo2.className = 'icon-americanexpress'; // FontAwesome class for American Express
						break;
					case 'discover':
						creditCardLogo2.className = 'icon-discover'; // FontAwesome class for Discover
						break;
					default:
						creditCardLogo2.className = ''; // Clear the class if no matching type is found
						break;
				}
			}
		});
	}
	// cleave js activation end

	//-----JS for Price Range slider-----
	$(function () {
		$("#slider-range").slider({
			range: true,
			min: 0,
			max: 10000,
			values: [499, 2999],
			slide: function (event, ui) {
				$("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
			}
		});
		$("#amount").val("$" + $("#slider-range").slider("values", 0) +
			" - $" + $("#slider-range").slider("values", 1));
	});

	// Cart Quantity Js
	$(".cart-minus").click(function () {
		var $input = $(this).parent().find("input");
		var count = parseInt($input.val()) - 1;
		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		return false;
	});

	$(".cart-plus").click(function () {
		var $input = $(this).parent().find("input");
		$input.val(parseInt($input.val()) + 1);
		$input.change();
		return false;
	});

	// sweetalert2 activation
	if (jQuery("#removeBtn").length > 0) {
		document.addEventListener('DOMContentLoaded', function () {
			// Find the button element
			const removeBtn = document.getElementById('removeBtn');

			// Add a click event listener to the button
			removeBtn.addEventListener('click', function () {
				// Show the SweetAlert2 dialog
				Swal.fire({
					title: "Are you sure?",
					text: "You won't be able to revert this!",
					icon: "warning",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Yes, delete it!"
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							title: "Deleted!",
							text: "Your file has been deleted.",
							icon: "success"
						});
					}
				});
			});
		});
	}

	if (jQuery("#removeBtn2").length > 0) {
		document.addEventListener('DOMContentLoaded', function () {
			// Find the button element
			const removeBtn2 = document.getElementById('removeBtn2');

			// Add a click event listener to the button
			removeBtn2.addEventListener('click', function () {
				// Show the SweetAlert2 dialog
				Swal.fire({
					title: "Are you sure?",
					text: "You won't be able to revert this!",
					icon: "warning",
					showCancelButton: true,
					confirmButtonColor: "#3085d6",
					cancelButtonColor: "#d33",
					confirmButtonText: "Yes, delete it!"
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							title: "Deleted!",
							text: "Your file has been deleted.",
							icon: "success"
						});
					}
				});
			});
		});
	}

	// removeBTN
	document.addEventListener('DOMContentLoaded', function () {
		// Find all elements with class 'removeBtn'
		const removeBtns = document.querySelectorAll('.removeBtn');

		// Add click event listener to each button
		removeBtns.forEach(function (removeBtn) {
			removeBtn.addEventListener('click', function () {
				// Show the SweetAlert2 dialog
				Swal.fire({
					title: "Are you sure?",
					text: "You won't be able to revert this!",
					icon: "warning",
					showCancelButton: true,
					// confirmButtonColor: "#3085d6",
					// cancelButtonColor: "#d33",
					confirmButtonText: "Yes, delete it!"
				}).then((result) => {
					if (result.isConfirmed) {
						Swal.fire({
							title: "Deleted!",
							text: "Your file has been deleted.",
							icon: "success"
						});
					}
				});
			});
		});
	});

	// crypto order list search js
	$(document).ready(function () {
		// Initialize the repeater
		$('#productTableRepeater').repeater({
			show: function () {
				$(this).slideDown();
			},
			hide: function (deleteElement) {
				if (confirm('Are you sure you want to delete this row?')) {
					$(this).slideUp(deleteElement);
				}
			}
		});

		// Add product button click event
		$('#addItemBtn').on('click', function () {
			var newRow = $('#productTableRepeater .product').clone();
			newRow.find('input, textarea').val(''); // Clear input values
			newRow.find('.product-quantity').val('0'); // Set quantity to 0
			newRow.find('.product-line-price').val('$0.00'); // Set price to $0.00
			$('#productTableRepeater').append(newRow);
		});

		// Corrected delete button click event
		$('#productTableRepeater').on('click', '#productRemoval', function () {
			if (confirm('Are you sure you want to delete this row?')) {
				$(this).closest('.product').slideUp(function () {
					$(this).remove();
				});
			}
		});
	});

	$(document).ready(function () {
		// Function to update productTotalPrice based on productRate and productQnty
		function updateTotalPrice() {
			var rate = parseFloat($('.product-rate').val()) || 0;
			var quantity = parseInt($('.product-quantity').val()) || 0;
			var totalPrice = rate * quantity;
			$('.total-price').val('$' + totalPrice.toFixed(2));
		}

		// Event handlers for rate and quantity input fields
		$('.product-rate, .product-quantity').on('input', updateTotalPrice);

		// Event handler for delete button
		$('#productTableRepeater').on('click', '.productRemoval', function () {
			if (confirm('Are you sure you want to delete this row?')) {
				$(this).closest('.product').remove();
			}
		});
	});

	$(document).ready(function () {
		$('#selectall').click(function () {
			$('.selectedId').prop('checked', this.checked);
		});

		$('.selectedId').change(function () {
			var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);
			$('#selectall').prop("checked", check);
		});
	});

	// profile picture change
	$(document).ready(function () {
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
					$('#imagePreview').hide();
					$('#imagePreview').fadeIn(650);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
		$("#imageUpload").change(function () {
			readURL(this);
		});
	});
	$(document).ready(function () {
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#imagePreview2').css('background-image', 'url(' + e.target.result + ')');
					$('#imagePreview2').hide();
					$('#imagePreview2').fadeIn(650);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
		$("#imageUpload2").change(function () {
			readURL(this);
		});
	});
	$(document).ready(function () {
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#imagePreview3').css('background-image', 'url(' + e.target.result + ')');
					$('#imagePreview3').hide();
					$('#imagePreview3').fadeIn(650);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
		$("#imageUpload3").change(function () {
			readURL(this);
		});
	});

	// checked selected
	$(document).ready(function () {
		$('#selectall').click(function () {
			$('.selectedId').prop('checked', this.checked);
		});

		$('.selectedId').change(function () {
			var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);
			$('#selectall').prop("checked", check);
		});
	});

	// star
	setTimeout(function () {
		$(".rating__select").on('click', function () {
			$(this).toggleClass("filled");
		});
	}, 100);

	// Drag Item
	$(document).ready(() => {
		$(".droppable-area").sortable({
			connectWith: ".connected-sortable",
			stack: '.connected-sortable ul'
		}).disableSelection();
	});

	// Dropzone Activision
	Dropzone.options.myDropzone = {
		url: "/fake/location",
		autoProcessQueue: false,
		paramName: "file",
		clickable: true,
		maxFilesize: 5000, // in MB
		addRemoveLinks: true,
		dictDefaultMessage: "Upload your file here",
		init: function () {
			this.on("sending", function (file, xhr, formData) {
				console.log("sending file");
			});
			this.on("success", function (file, responseText) {
				console.log('great success');
			});
			this.on("addedfile", function (file) {
				console.log('file added');
			});
		}
	};


	Dropzone.options.myNewDropzone = {
		url: "/fake/location",
		autoProcessQueue: false,
		paramName: "file",
		clickable: true,
		maxFiles: 1, // Limit the number of files to 1
		maxFilesize: 5000, // in mb
		addRemoveLinks: true,
		dictDefaultMessage: "Upload your file here",
		init: function () {
			this.on("sending", function (file, xhr, formData) {
				console.log("sending file");
			});
			this.on("success", function (file, responseText) {
				console.log('great success');
			});
			this.on("addedfile", function (file) {
				console.log('file added');

				// Remove any existing files before adding the new one
				if (this.files.length > 1) {
					this.removeFile(this.files[0]);
				}
			});
		}
	};

	// bootstarp Popover
	const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
	const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

	// custom toaster
	document.addEventListener("DOMContentLoaded", function () {
		window.showToast = function (position) {
			const container = document.getElementById('toaster-container');
			const toast = document.createElement('div');
			toast.className = `toaster ${position}`;
			toast.textContent = `${position.replace('-', ' ')} Notification`;

			container.appendChild(toast);

			setTimeout(() => {
				toast.style.display = 'none';
				container.removeChild(toast);
			}, 3000); // Hide the toast after 3 seconds

			// Adjust position to stack notifications
			const toasts = document.querySelectorAll(`.toaster.${position}`);
			let offset = 20; // Initial offset
			toasts.forEach((t, index) => {
				if (position.includes('top')) {
					t.style.top = `${offset}px`;
				} else {
					t.style.bottom = `${offset}px`;
				}
				offset += t.offsetHeight + 10; // Add height and margin for next toast
			});
		}
	});

	// bootstarp toast

	document.addEventListener('DOMContentLoaded', function () {
		const selectToastPlacement = document.getElementById('selectToastPlacement');
		const toastContainer = document.getElementById('toastPlacement');
		const toastEl = document.querySelector('.toast');
		const liveToastBtn = document.getElementById('liveToastBtn');

		// Check if selectToastPlacement element exists
		if (selectToastPlacement && toastContainer && toastEl && liveToastBtn) {
			const toast = new bootstrap.Toast(toastEl);

			selectToastPlacement.addEventListener('change', function () {
				const selectedPosition = selectToastPlacement.value;
				toastContainer.className = 'toast-container p-3'; // Reset classes
				if (selectedPosition) {
					toastContainer.classList.add(...selectedPosition.split(' '));
				}
			});

			liveToastBtn.addEventListener('click', function () {
				toast.show();
			});
		}
	});

	/* swiper js activation */
	var swiperDefault = new Swiper(".swiperDefault", {});

	var swiperNavigation = new Swiper(".swiperNavigation", {
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
	});
	var swiperNavigation = new Swiper(".swiperPaginationNavigation", {
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
		pagination: {
			el: ".swiper-pagination",
		},
	});

	var swiperPagination = new Swiper(".swiperPagination", {
		pagination: {
			el: ".swiper-pagination",
		},
	});
	var swiperPaginationDynamic = new Swiper(".swiperPaginationDynamic", {
		pagination: {
			el: ".swiper-pagination",
			dynamicBullets: true,
		},
	});

	var swiper = new Swiper(".swiperPaginationProgress", {
		pagination: {
			el: ".swiper-pagination",
			type: "progressbar",
		},
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
	});

	var swiper = new Swiper(".swiperPaginationFraction", {
		pagination: {
			el: ".swiper-pagination",
			type: "fraction",
		},
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
	});

	var swiperPaginationCustom = new Swiper(".swiperPaginationCustom", {
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
			renderBullet: function (index, className) {
				return '<span class="' + className + '">' + (index + 1) + "</span>";
			},
		},
	});

	var swiperPaginationScrollbar = new Swiper(".swiperPaginationScrollbar", {
		scrollbar: {
			el: ".swiper-scrollbar",
			hide: true,
		},
	});

	var swiperVertical = new Swiper(".swiperVertical", {
		direction: "vertical",
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
	});

	var swiperSlidesPerView = new Swiper(".swiperSlidesPerView", {
		slidesPerView: 2,
		spaceBetween: 30,
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
	});
	var swiperSlidesEffect = new Swiper(".swiperSlidesEffect", {
		slidesPerView: 2,
		spaceBetween: 30,
		effect: "fade",
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
	});
	var swiperSlidesEffectCards = new Swiper(".swiperSlidesEffectCards", {
		effect: "cards",
		grabCursor: true,
	});

	var swiper = new Swiper(".swiperThumbs", {
		loop: false,
		spaceBetween: 10,
		slidesPerView: 10,
		freeMode: true,
		watchSlidesProgress: true,
	});
	var swiper2 = new Swiper(".swiperThumbsGallery", {
		loop: false,
		spaceBetween: 10,
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
		thumbs: {
			swiper: swiper,
		},
	});
	var swiperSlidesRtl = new Swiper(".swiperSlidesRtl", {
		pagination: {
			el: ".swiper-pagination",
			clickable: true,
		},
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},

	});


	const progressCircle = document.querySelector(".autoplay-progress svg");
	const progressContent = document.querySelector(".autoplay-progress span");
	var swiper = new Swiper(".swiperAutoplayProgress", {
		spaceBetween: 30,
		centeredSlides: true,
		autoplay: {
			delay: 6000,
			disableOnInteraction: false
		},
		pagination: {
			el: ".swiper-pagination",
			clickable: true
		},
		navigation: {
			nextEl: ".swiper-button_next",
			prevEl: ".swiper-button_prev",
		},
		on: {
			autoplayTimeLeft(s, time, progress) {
				progressCircle.style.setProperty("--progress", 3 - progress);
				progressContent.textContent = `${Math.ceil(time / 1000)}s`;
			}
		}
	});


	var range_slider_custom = {
		init: function () {
			$("#range-active-1").ionRangeSlider(),
				$("#range-active-2").ionRangeSlider({
					min: 100,
					max: 1000,
					from: 550
				}), $("#range-active-3").ionRangeSlider({
					type: "double",
					grid: true,
					min: 0,
					max: 1000,
					from: 200,
					to: 800,
					prefix: "$"
				}), $("#range-active-4").ionRangeSlider({
					type: "double",
					grid: true,
					min: -1000,
					max: 1000,
					from: -500,
					to: 500
				}), $("#range-active-5").ionRangeSlider({
					type: "double",
					grid: true,
					min: -1000,
					max: 1000,
					from: -500,
					to: 500,
					step: 250
				}), $("#range-active-6").ionRangeSlider({
					grid: true,
					from: 3,
					values: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
				}), $("#range-active-7").ionRangeSlider({
					grid: true,
					min: 1000,
					max: 10000,
					from: 2000,
					step: 100,
					prettify_enabled: true
				}), $("#range-active-8").ionRangeSlider({
					min: 100,
					max: 1000,
					from: 550,
					disable: true
				});
		}
	};


	$(function () {
		var bdTimelineBlocks = $(".bd-timeline-item-4"),
			offset = 0.8;

		//hide timeline blocks which are outside the viewport
		hideBlocks(bdTimelineBlocks, offset);

		//on scolling, show/animate timeline blocks when entering the viewport
		$(window).on("scroll", function () {
			!window.requestAnimationFrame
				? setTimeout(function () {
					showBlocks(bdTimelineBlocks, offset);
				}, 100)
				: window.requestAnimationFrame(function () {
					showBlocks(bdTimelineBlocks, offset);
				});
		});

		function hideBlocks(blocks, offset) {
			blocks.each(function () {
				$(this).offset().top >
					$(window).scrollTop() + $(window).height() * offset &&
					$(this).find(".bd-timeline-icon-4, .bd-timeline-content-4").addClass("is-hidden");
			});
		}

		function showBlocks(blocks, offset) {
			blocks.each(function () {
				$(this).offset().top <=
					$(window).scrollTop() + $(window).height() * offset &&
					$(this).find(".bd-timeline-icon-4").hasClass("is-hidden") &&
					$(this)
						.find(".bd-timeline-icon-4, .bd-timeline-content-4")
						.removeClass("is-hidden")
						.addClass("animate-it");
			});
		}
	});

	/* SimpleBar */
	var myElement = document.getElementById('sidebar-scroll');
	if (myElement) {
		new SimpleBar(myElement, { autoHide: true });
	}
	/* SimpleBar */


	/* Comming Soon */
	document.addEventListener('DOMContentLoaded', function () {
		/* Replace with your launch date  */
		var launchDate = new Date('2030-07-14 00:00:00').getTime();

		var countdown = setInterval(function () {
			var now = new Date().getTime();
			var distance = launchDate - now;

			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			var countdownElement = document.getElementById('countdown');
			if (countdownElement) {
				countdownElement.innerHTML = days + "d " + hours + "h "
					+ minutes + "m " + seconds + "s ";
			}

			if (distance < 0) {
				clearInterval(countdown);
				if (countdownElement) {
					countdownElement.innerHTML = "EXPIRED";
				}
			}
		}, 1000);
	});



})(jQuery);
