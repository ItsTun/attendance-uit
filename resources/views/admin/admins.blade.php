@extends('layouts.admin_layout')

@section('title')
    Admin
@endsection

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/admins.css') }}"/>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8"></div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 10px;">
                <button type="button" class="btn btn-md btn-success pull-right" data-toggle="modal" id="add-btn"
                        data-target="#addOrEditUser">
                    Add New Admin
                </button>
            </div>
        </div>
    </div>
    <div class="card" style="margin:10px;">
        <div class="row" style="padding: 30px;">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody>
                @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            <button type="button" class="btn btn-primary edit-btn" id="edit-btn" data-toggle="modal"
                                    data-id="{{ $admin->id }}" data-name="{{ $admin->name }}"
                                    data-email="{{ $admin->email }}"
                                    data-target="#addOrEditUser">Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{ $admins->links('pagination.circle-pagination') }}

    </div>
    <div id="addOrEditUser" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form class="form-horizontal form-material" action="addOrUpdateUser" onsubmit="return validate()"
                      method="post">
                    <div class="modal-body">
                        <div class="container">
                            {{ csrf_field() }}
                            <input type="hidden" type="text" name="user_id" class="input-id"/>
                            <div class="form-group">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-name" type="text"
                                           name="name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Email</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-email" type="email"
                                           name="email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Password</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-password" type="password"
                                           name="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Confirm Password</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-confirm-password" type="password"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-success" id="savedata" value="Save"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var isUpdate = false;
        var oldEmail = '';

        $(document).on("click", "#edit-btn", function () {
            isUpdate = true;
            var userId = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');

            oldEmail = email;

            $('.input-id').val(userId);
            $('.input-name').val(name);
            $('.input-email').val(email);

            $('.input-password').prop('required', false);
            $('.input-confirm-password').prop('required', false);
        });

        $(document).on("click", "#add-btn", function () {
            isUpdate = false;
            oldEmail = '';

            $('.input-id').val('');
            $('.input-name').val('');
            $('.input-email').val('');
            $('.input-password').val('');
            $('.input-confirm-password').val('');

            $('.input-password').prop('required', true);
            $('.input-confirm-password').prop('required', true);
        });

        function validate() {
            var email = $('.input-email').val();
            var password = $('.input-password').val();
            var confirmPassword = $('.input-confirm-password').val();

            if(password.trim() != '') {
                if(password != confirmPassword) {
                    alert("Passwords are not the same");
                    return false;
                }
            }
            if ((isUpdate && oldEmail != email) || !isUpdate) {
                var responseText = $.ajax({
                    type: "GET",
                    data: {
                        "email": email,
                    },
                    async: false,
                    url: "checkIfUserEmailExists",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).responseText;
                if (responseText != 'false') alert('Another admin with email,' + email + ', already existed');
                return (responseText == 'false');
            }
        }
    </script>
@endsection