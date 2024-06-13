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

// When deleting a group of vehicle inventories
$('.delete-group').click(function() {
    var vehicleNo = $(this).data('id');
    var groupRows = $(this).closest('tr').find('.details tbody tr');
    var ids = [];

    // Collect all item IDs within the group
    groupRows.each(function() {
        ids.push($(this).find('.delete-vehicle-inventory').data('id'));
    });

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#C8B400',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete them all!'
    }).then((result) => {
        if (result.isConfirmed) {
            if (!navigator.onLine) {
                toastr.error('No internet connection. Unable to delete products.');
                return; // Stop further execution
            }

            var total = ids.length;
            var successCount = 0;
            var errorCount = 0;

            ids.forEach(function(id) {
                $.ajax({
                    url: '/vehicle-inventory/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            successCount++;
                            groupRows.filter(`[data-id="${id}"]`).closest('tr').remove(); // Remove row on success
                        } else {
                            errorCount++;
                        }

                        if (successCount + errorCount === total) {
                            if (errorCount === 0) {
                                toastr.success('All products deleted successfully');
                                groupRows.closest('tr').remove();
                            } else {
                                toastr.warning(`${successCount} products deleted successfully, ${errorCount} failed to delete.`);
                            }
                        }
                    },
                    error: function(xhr) {
                        errorCount++;
                        if (successCount + errorCount === total) {
                            toastr.warning(`${successCount} products deleted successfully, ${errorCount} failed to delete.`);
                        }
                    }
                });
            });
        }
    });
});
