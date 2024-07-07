$(document).ready(function() {
    $('.delete-employee').click(function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C8B400',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!navigator.onLine) {
                    toastr.error('No internet connection. Unable to delete employee.');
                    return; 
                }

                $.ajax({
                    url: '/employee/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Employee deleted successfully');
                            $('#example1').DataTable().row($('button[data-id="' + id + '"]').closest('tr')).remove().draw();
                        } else {
                            toastr.error(response.message || 'Failed to delete employee');
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
