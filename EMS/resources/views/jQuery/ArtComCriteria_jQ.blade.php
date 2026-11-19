<script>
    $(document).ready(function() {

        loadCriteria();

        function loadCriteria() {
            $.ajax({
                url: "{{ route('artcomcriteria.index') }}",
                type: "GET",
                success: function(data) {
                    let html = `
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>File</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    if (data.length === 0) {
                        html += `<tr><td colspan="4" class="text-center">No criteria found.</td></tr>`;
                    } else {
                        data.forEach(function(item) {
                            html += `
                        <tr>
                            <td>${item.id}</td>
                            <td><a href="/competitor_files/${item.Path}" target="_blank">View File</a></td>
                            <td>${item.Year}</td>
                            <td>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${item.id}">Delete</button>
                            </td>
                        </tr>`;
                        });
                    }

                    html += `</tbody></table>`;
                    $("#student").html(html);
                }
            });
        }


        // Add new criteria
        $("#new_competitor_adding").on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: $(this).attr("action"),
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message
                        });
                        $("#new_competitor_adding")[0].reset();
                        fetchAllCriteria();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Oops!',
                            text: res.message
                        });
                    }
                }
            });
        });


        // Fetch criteria table
        fetchAllCriteria();

        function fetchAllCriteria() {
            $.ajax({
                url: "{{ route('artcomcriteria.fetchAll') }}",
                method: 'GET',
                success: function(response) {
                    $("#criteria").html(response);
                    $("table").DataTable({
                        order: [0, 'asc'],
                        scrollY: '45vh',
                        scrollCollapse: true,
                        paging: false,
                        info: false,
                        columnDefs: [{
                            orderable: false,
                            targets: [1, 2, 3]
                        }]
                    });
                }
            });
        }


        // Delete button
        $(document).on("click", ".delete-btn", function(e) {
            e.preventDefault();
            let id = $(this).data("id");
            let csrf = "{{ csrf_token() }}";

            Swal.fire({
                title: 'Are you sure?',
                text: "This will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {

                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('artcomcriteria.delete') }}",
                        method: "DELETE",
                        data: {
                            id: id,
                            _token: csrf
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("Deleted!", "Criteria deleted.", "success");
                                fetchAllCriteria();
                            }
                        }
                    });
                }
            });
        });

    });
</script>