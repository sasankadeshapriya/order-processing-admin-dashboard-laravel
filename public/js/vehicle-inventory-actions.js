// When deleting a vehicle inventory
$('.delete-vehicle-inventory').click(function() {
    var id = $(this).data('id');
    var row = $(this).closest('tr'); // Get the closest row

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
                toastr.error('No internet connection. Unable to delete product.');
                return; // Stop further execution
            }

            $.ajax({
                url: '/vehicle-inventory/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Product deleted successfully');

                        // Remove the entire row from the main table
                        row.remove();
                    } else {
                        toastr.error(response.message || 'Failed to delete product');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                }
            });
        }
    });
});
