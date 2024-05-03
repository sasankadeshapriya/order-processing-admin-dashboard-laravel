$(document).ready(function() {
    // Use delegated events to attach handlers to present and future elements
    $(document).on('click', '.delete-vehicle', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!navigator.onLine) {
                    toastr.error('No internet connection. Unable to delete vehicle.');
                    return;
                }
                $.ajax({
                    url: '/vehicle/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Vehicle deleted successfully');
                            $('#example1').DataTable().row($('button[data-id="' + id + '"]').closest('tr')).remove().draw();
                        } else {
                            toastr.error(response.message || 'Failed to delete vehicle');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                    }
                });
            }
        });
    });
});
