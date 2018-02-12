@extends('layouts.admin_layout')

@section('title')
    Subjects
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('/css/subjects.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/select2/css/select2.min.css') }}"/>
@endsection

@section('content')
    <div class="card" style="margin: 7px;">
        <div class="card-block">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <form action="#" method="get">
                        <div class="row">
                            <div class="col-9">
                                <input class="form-control" type="text" name="q" placeholder="Name"
                                       value="{{ $query }}"/>
                            </div>
                            <div class="col-2" style="padding-left: 0px;">
                                <button type="submit" class="btn btn-success" style="height:36px;"
                                >Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 add-btn-wrapper" style="text-align: right;">
                    <button class="btn btn-success" id="add-btn" style="height:36px;" data-toggle="modal"
                            data-target="#addOrEditSubject">Add New Subject
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="card" style="margin: 7px;">
        <div class="row" style="padding: 30px;">
            @if(sizeof($subjects) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Name</th>
                            <th>Classes</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($subjects as $subject)
                            <tr>
                                <td>{{ $subject->subject_code }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>
                                    @foreach($subject->subject_class as $subject_class)
                                        <span class="label label-primary"
                                              title="{{ $subject_class->klass->name }}">{{ $subject_class->klass->short_form }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <button type="button" id="edit-btn" class="btn btn-success" data-toggle="modal"
                                            data-subject-id="{{ $subject->subject_id }}"
                                            data-subject-name="{{ $subject->name }}"
                                            data-subject-code="{{ $subject->subject_code }}"
                                            data-subject-classes="{{ $subject->subject_class }}"
                                            data-prefix-option="@php if(is_null($subject->subject_class[0]->custom_prefix)) echo "null"; else echo $subject->subject_class[0]->custom_prefix; @endphp"
                                            data-target="#addOrEditSubject">Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="col-md-12">
                    <center> No subjects @if(!is_null($query))for "{{ $query }}"@endif</center>
                </div>
            @endif
        </div>
        <center>{{ $subjects->appends(Request::only('q'))->links('pagination.circle-pagination') }}</center>
    </div>


    <!--Add Subject Modal-->
    <div id="addOrEditSubject" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Subject</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form class="form-horizontal form-material" method="post" action="addOrUpdateSubject">
                    {{ csrf_field() }}
                    <input type="hidden" name="subject_id" class="input-subject-id"/>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group">
                                <label class="col-md-12">Subject Prefix</label>
                                <input type="radio" name="radio_subject_prefix" id="radio-use-class" value="default"
                                       checked
                                       required/>
                                <label for="radio-use-class" style="margin-left: 10px;margin-top: 15px">Use Class's
                                    Short
                                    Form</label>
                                <input type="radio" name="radio_subject_prefix" id="radio-use-custom" value="custom"
                                       required/>
                                <label for="radio-use-custom">Use Custom</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-subject-prefix"
                                           id="input-subject-prefix" type="text"
                                           name="subject_prefix" value=""/>
                                </div>
                                <label class="col-md-12" style="margin-top: 15px;">Subject Code</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-subject-code"
                                           id="input-subject-code" type="text"
                                           name="subject_code" value="" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input class="form-control form-control-line input-subject-name" type="text"
                                           name="name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12" style="margin-bottom: 10px;">Classes</label>
                                <div class="col-md-12">
                                    <select class="class-select col-md-12" style="width: 100%;" name="classes[]"
                                            multiple="multiple" required>
                                        @foreach($years as $year)
                                            <optgroup label="{{ $year->name }}" data-year-id="{{ $year->year_id }}">
                                                @foreach($year->klasses as $klass)
                                                    <option value="{{ $klass->class_id }}">{{ $klass->name }}</option>
                                                @endforeach
                                            </optgroup>
                                    @endforeach
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
    <!-- Add Subject Modal -->
@endsection

@section('scripts')
    <script src="{{ asset('/select2/js/select2.min.js') }}"></script>
    <script>
        var prefix;

        $(document).ready(function () {
            $('.class-select').select2({
                placeholder: "Select classes that this subject will be taught",
            });
            prefix = $('#input-subject-prefix');
            prefix.hide();
        });

        $('input[type=radio][name=radio_subject_prefix]').change(function () {
            if (this.value == 'default') {
                hidePrefix();
            }
            else if (this.value == 'custom') {
                showPrefix();
            }
        });

        function hidePrefix() {
            prefix.hide();
        }

        function showPrefix() {
            prefix.show();
        }

        $(document).on("click", "#edit-btn", function () {
            prefix.val("");
            var subjectId = $(this).data('subject-id');
            var subjectName = $(this).data('subject-name');
            var subjectCode = $(this).data('subject-code');
            var subjectClasses = $(this).data('subject-classes');
            var prefixOption = $(this).data('prefix-option');

            var classes = [];

            subjectClasses.forEach(function (entry) {
                classes[classes.length] = entry['class_id'];
            });

            console.log(prefixOption);

            if (prefixOption == null) {
                $('input[name=radio_subject_prefix][value=default]').prop('checked', 'checked');
                hidePrefix();
            } else {
                $('input[name=radio_subject_prefix][value=custom]').prop('checked', 'checked');
                showPrefix();
                prefix.val(prefixOption);
            }

            $('.input-subject-id').val(subjectId);
            $('.input-subject-name').val(subjectName);
            $('.input-subject-code').val(subjectCode);
            $('.class-select').val(classes);
            $('.class-select').trigger('change');
        });

        $(document).on("click", "#add-btn", function () {
            prefix.val("");
            $('.input-subject-id').val('');
            $('.input-subject-name').val('');
            $('.input-subject-code').val('');
            $('.class-select').val('');
            $('.class-select').trigger('change');
        });
    </script>
@endsection