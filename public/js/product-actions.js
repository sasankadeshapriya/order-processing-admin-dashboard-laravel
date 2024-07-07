$(document).ready(function() {
    $('.delete-product').click(function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "Warning: Deleting batches and unlocked vehicle inventories! Proceed with caution.",
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
                    url: '/product/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Product deleted successfully');
                            $('#example1').DataTable().row($('button[data-id="' + id + '"]').closest('tr')).remove().draw();
                        } else {
                            toastr.error(response.message || 'Failed to delete product *Please unlock related vehicle inventories before deleting the product.');
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
