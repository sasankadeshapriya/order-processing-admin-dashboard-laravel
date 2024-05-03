<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- File input -->
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#example1').DataTable({
            "responsive": false,
            "lengthChange": true,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        bsCustomFileInput.init(); // Initialize bs-custom-file-input for bootstrap file input enhancement

        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Example function to open an image popup
        window.openImagePopup = function(imageUrl) {
            var popupWindow = window.open(imageUrl, 'Product Image', 'width=600,height=400');
            popupWindow.focus();
        }
    });

    $(document).ready(function() {
        // Define all colors and skins
        var navbar_dark_skins = [
            'navbar-primary', 'navbar-secondary', 'navbar-info', 'navbar-success', 'navbar-danger',
            'navbar-indigo', 'navbar-purple', 'navbar-pink', 'navbar-navy', 'navbar-lightblue',
            'navbar-teal', 'navbar-cyan', 'navbar-dark', 'navbar-gray-dark', 'navbar-gray'
        ];
        var navbar_light_skins = [
            'navbar-light', 'navbar-warning', 'navbar-white', 'navbar-orange'
        ];
        var navbar_all_colors = navbar_dark_skins.concat(navbar_light_skins);

        // Function to update the navbar color
        function updateNavbarColor(color) {
            var $main_header = $('.main-header');
            $main_header.removeClass('navbar-dark navbar-light').removeClass(navbar_all_colors.join(' '));

            if (navbar_dark_skins.includes(color)) {
                $main_header.addClass('navbar-dark').addClass(color);
            } else {
                $main_header.addClass('navbar-light').addClass(color);
            }
        }

        // Function to preload the preloader images
        function preloadImages() {
            var preloaderImages = [
                '{{ asset('dist/img/app_logo.svg') }}',
                '{{ asset('dist/img/app_logo_dark.svg') }}'
            ];
            $(preloaderImages).each(function() {
                $('<img/>')[0].src = this;
            });
        }

        // Initialize preloader logo and dark mode from localStorage immediately on page load
        var isDarkMode = localStorage.getItem('darkMode') === 'true';
        var preloaderImgSrc = isDarkMode ? '{{ asset('dist/img/app_logo.svg') }}' :
            '{{ asset('dist/img/app_logo.svg') }}';
        $('#preloaderImg').attr('src', preloaderImgSrc); // Set the correct preloader image before anything else

        // Preload images
        preloadImages();

        // Set body class for dark mode
        $('body').toggleClass('dark-mode', isDarkMode);
        updateNavbarColor(isDarkMode ? 'navbar-gray-dark' : 'navbar-white');

        // Toggle dark mode
        $('#darkModeToggle').click(function(e) {
            e.preventDefault();
            isDarkMode = $('body').toggleClass('dark-mode').hasClass('dark-mode');

            // Set the dark mode status in localStorage
            localStorage.setItem('darkMode', isDarkMode);

            // Update Navbar color based on dark mode
            updateNavbarColor(isDarkMode ? 'navbar-gray-dark' : 'navbar-white');

            // Update preloader logo
            preloaderImgSrc = isDarkMode ? '{{ asset('dist/img/app_logo.svg') }}' :
                '{{ asset('dist/img/app_logo.svg') }}';
            $('#preloaderImg').attr('src', preloaderImgSrc);

            // Optionally change the icon for the toggle
            $(this).find('i').toggleClass('fa-sun fa-moon');
        });
    });
</script>
