@extends('layouts.admin_layout')

@section('title')
	Classes
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-6"></div>
            <div class="col-md-6 col-4 align-self-center" style="padding: 10px;">
                <button type="button" class="btn btn-md btn-success pull-right" data-toggle="modal" data-target="#addClass">Add A Class</button>
            </div>
    </div>
</div>

<div class="card" style="margin:10px;">
    <div class="row" style="padding: 30px;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>Class</th>
                    <th>Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                 <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><button type="button" class="btn btn-md btn-primary" data-toggle="modal" data-target="#editClass">Edit</button>
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


<!--Add Class Modal-->
<div id="addClass" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add A Class</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="container">
                  <form class="form-horizontal form-material">
                      <div class="form-group">
                        <label class="col-md-12">Year</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Class</label>
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
<!--Add Clas Modal -->


<!--Edit Class Modal-->
<div id="editClass" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit A Class</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="container">
                  <form class="form-horizontal form-material">
                      <div class="form-group">
                        <label class="col-md-12">Year</label>
                          <div class="col-md-12">
                            <input class="form-control form-control-line" type="text">
                          </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-12">Class</label>
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
<!--Edit Class Modal -->

@endsection