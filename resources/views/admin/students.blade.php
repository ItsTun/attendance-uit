@extends('layouts.admin_layout')

@section('title')
	Students
@endsection

@section('content')
<div class="card" style="margin: 7px;">
    <div class="card-block">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group form-material">
                        <label class="col-md-12">Search By Name</label>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-8">
                                    <input class="form-control form-control-line" type="text"></div>
                                <div class="col-sm-4">
                                    <button class="btn btn-success">Search</button></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group form-material">
                        <label class="col-md-12">Group By Year</label>
                        <div class="col-md-12">
                            <select class="form-control form-control-line">
                                <option>First Yr</option>
                                <option>Second Yr</option>
                                <option>Third Yr</option>
                                <option>Fourth Yr</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-material">
                        <div style="padding: 20px 0px 0px 35px;">
                            <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#addStudent">Add Student</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card" style="margin: 7px;">
    <div class="row" style="padding: 30px;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editStudent">Edit</button>
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


<!--Add Student Modal-->
<div id="addStudent" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!--Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Student</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="container">
                  <form class="form-horizontal form-material">
                      <div class="form-group">
                        <label class="col-md-12">Roll No</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
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
<!-- Add Student Modal -->


<!--Edit Student Modal-->
<div id="editStudent" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!--Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Student</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="container">
                  <form class="form-horizontal form-material">
                      <div class="form-group">
                        <label class="col-md-12">Roll No</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
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
<!-- Edit Student Modal -->

@endsection