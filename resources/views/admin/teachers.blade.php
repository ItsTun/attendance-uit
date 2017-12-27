@extends('layouts.admin_layout')

@section('title')
	Teachers
@endsection

@section('content')
<head>
<script type="text/javascript" src="{{ URL::asset('js/adminTeachers.js') }}"></script>
</head>
    
<div class="card" style="margin: 7px;">
    <div class="row">
        <div class="col-md-2" style="padding: 30px 0px 0px 40px;">Group By Teacher</div>
        <div class="col-md-4" style="padding: 20px 0px 0px 10px;">
            <div class="form-group form-material" style="padding: 5px;">
                <select class="form-control form-control-line" id="selectfaculty">
                    <option value="0">Select Faculty</option>
                    <option value="1">2</option>
                    <option value="2">3</option>
                </select>
            </div>
        </div>
        
        <div class="col-md" style="padding: 20px 0px 0px 250px;">
            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addTeacher">Add Teacher</button>
        </div>
    </div>
</div>


<div class="card" style="margin: 7px;">
    <div class="row" style="padding: 30px;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subjects</th>
                    <th>Faculty</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
               
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editTeacher">Edit</button>
                        <button type="button" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<!-- Pigination -->
    <nav class="my-4">
        <ul class="pagination pagination-circle pg-blue mb-0 justify-content-center">
            <!--Arrow left-->
            <li class="page-item disabled">
                <a class="page-link" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
            </li>

            <!--Numbers-->
            <li class="page-item active"><a class="page-link">1</a></li>
            <li class="page-item"><a class="page-link">2</a></li>
            <li class="page-item"><a class="page-link">3</a></li>
            <li class="page-item"><a class="page-link">4</a></li>
            <li class="page-item"><a class="page-link">5</a></li>

            <!--Arrow right-->
            <li class="page-item">
                <a class="page-link" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
            </li>
        </ul>
    </nav>
</div>


<!--Add Teacher Modal-->
<div id="addTeacher" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Teacher</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="container">
                  <form class="form-horizontal form-material">
                      <div class="form-group">
                        <label class="col-md-12">Name</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Email</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Faculty</label>
                          <div class="col-md-12">
                              <select class="form-control form-control-line" type="text">
                                <option>Select Faculty</option>
                                <option>1</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Subjects</label>
                          <div class="col-md-12">
                              <textarea class="form-control form-control-line" type="textarea" rows="2" readonly></textarea>
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Search Subject</label>
                          <div class="col-md-12 ">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                  </form>
              </div>
        </div>
      
      <div class="modal-footer">
        <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" data-dismiss="modal" id="savedata">Save</button>
      </div>
    </div>
  </div>
</div>
<!-- Add Teacher Modal -->


<!--Edit Teacher Modal-->
<div id="editTeacher" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Teacher</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="container">
                  <form class="form-horizontal form-material">
                      <div class="form-group">
                        <label class="col-md-12">Name</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Email</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Faculty</label>
                          <div class="col-md-12">
                              <select class="form-control form-control-line" type="text">
                                <option>Select Faculty</option>
                                <option>1</option>
                              </select>
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Subjects</label>
                          <div class="col-md-12">
                              <textarea class="form-control form-control-line" type="textarea" rows="2" readonly></textarea>
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Search Subject</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                  </form>
              </div>
        </div>
      
      <div class="modal-footer">
        <button type="cancel" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" data-dismiss="modal" id="savedata">Save</button>
      </div>
    </div>
  </div>
</div>
<!-- Edit Teacher Modal -->

@endsection