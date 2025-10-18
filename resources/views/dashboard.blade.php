@extends('layouts.app')
@section('title')
<div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
@endsection
@section('content')
<!-- Info boxes -->
<div class="row">
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon text-bg-primary shadow-sm">
        <i class="bi bi-person-fill-gear"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Masters</span>
        <span class="info-box-number">{{ $totalMasters }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon text-bg-danger shadow-sm">
        <i class="bi bi-people-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Employees</span>
        <span class="info-box-number">{{ $totalEmployees }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <!-- fix for small devices only -->
  <!-- <div class="clearfix hidden-md-up"></div> -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon text-bg-success shadow-sm">
        <i class="bi bi-check-circle-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Tasks</span>
        <span class="info-box-number">{{ $totalTasks }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon text-bg-warning shadow-sm">
        <i class="bi bi-chat-text-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Messages</span>
        <span class="info-box-number">{{ $totalMessages }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon text-bg-light shadow-sm">
        <i class="bi bi-clock-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Clock In</span>
        <span class="info-box-number">
          @if($todayAttendance && $todayAttendance->check_in_time)
            {{ $todayAttendance->check_in_time->format('H:i') }}
          @else
            --
          @endif
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box">
      <span class="info-box-icon text-bg-dark shadow-sm">
        <i class="bi bi-clock-history"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Clock Out</span>
        <span class="info-box-number">
          @if($todayAttendance && $todayAttendance->check_out_time)
            {{ $todayAttendance->check_out_time->format('H:i') }}
          @else
            --
          @endif
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

<!-- Additional Info boxes -->
<div class="row">
  <div class="col-12 col-sm-6 col-md-4">
    <div class="info-box">
      <span class="info-box-icon text-bg-info shadow-sm">
        <i class="bi bi-people-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Users</span>
        <span class="info-box-number">{{ $totalUsers }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-4">
    <div class="info-box">
      <span class="info-box-icon text-bg-secondary shadow-sm">
        <i class="bi bi-building-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Divisions</span>
        <span class="info-box-number">{{ $totalDivisions }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-6 col-md-4">
    <div class="info-box">
      <span class="info-box-icon text-bg-dark shadow-sm">
        <i class="bi bi-person-badge-fill"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text">Total Karyawans</span>
        <span class="info-box-number">{{ $totalKaryawans }}</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
<!--begin::Row-->
<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title">Monthly Recap Report</h5>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
          </button>
          <div class="btn-group">
            <button
              type="button"
              class="btn btn-tool dropdown-toggle"
              data-bs-toggle="dropdown"
            >
              <i class="bi bi-wrench"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" role="menu">
              <a href="#" class="dropdown-item">Action</a>
              <a href="#" class="dropdown-item">Another action</a>
              <a href="#" class="dropdown-item"> Something else here </a>
              <a class="dropdown-divider"></a>
              <a href="#" class="dropdown-item">Separated link</a>
            </div>
          </div>
          <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <!-- Search Form -->
        <div class="mb-3">
          <form method="GET" action="{{ route('dashboard') }}" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by task title or assignee name..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            @if(request('search'))
              <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary ms-2">Clear</a>
            @endif
          </form>
        </div>
        @if($tasks->hasPages())
          <div class="d-flex justify-content-center mt-4">
            {{ $tasks->appends(request()->query())->links() }}
          </div>
        @endif
        <div class="table-responsive">
          @if($user->role === 'master')
            <table class="table table-bordered table-striped table-sm">
              <thead>
                <tr>
                  <th>Employee Name</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Due Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tasks as $task)
                  <tr>
                    <td class="text-break">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                    <td class="text-break">{{ $task->title }}</td>
                    <td class="text-break">{{ Str::limit($task->description, 50) }}</td>
                    <td>
                      @switch($task->status)
                        @case('pending')
                          <span class="badge text-bg-warning">Pending</span>
                          @break
                        @case('in_progress')
                          <span class="badge text-bg-info">In Progress</span>
                          @break
                        @case('completed')
                          <span class="badge text-bg-success">Completed</span>
                          @break
                        @default
                          <span class="badge text-bg-secondary">{{ $task->status }}</span>
                      @endswitch
                    </td>
                    <td class="text-break">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'No due date' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <table class="table table-bordered table-striped table-sm">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Due Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tasks as $task)
                  <tr>
                    <td class="text-break">{{ $task->title }}</td>
                    <td class="text-break">{{ Str::limit($task->description, 50) }}</td>
                    <td>
                      @switch($task->status)
                        @case('pending')
                          <span class="badge text-bg-warning">Pending</span>
                          @break
                        @case('in_progress')
                          <span class="badge text-bg-info">In Progress</span>
                          @break
                        @case('completed')
                          <span class="badge text-bg-success">Completed</span>
                          @break
                        @default
                          <span class="badge text-bg-secondary">{{ $task->status }}</span>
                      @endswitch
                    </td>
                    <td class="text-break">{{ $task->due_date ? $task->due_date->format('Y-m-d') : 'No due date' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
      <!-- ./card-body -->
      @if($tasks->hasPages())
        <div class="d-flex justify-content-center mt-3">
          {{ $tasks->appends(request()->query())->links() }}
        </div>
      @endif
      <div class="card-footer">
        <!--begin::Row-->
        <div class="row">
          <div class="col-md-3 col-6">
            <div class="text-center border-end">
              <span class="text-success">
                <i class="bi bi-caret-up-fill"></i> 17%
              </span>
              <h5 class="fw-bold mb-0">$35,210.43</h5>
              <span class="text-uppercase">TOTAL REVENUE</span>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-6">
            <div class="text-center border-end">
              <span class="text-info"> <i class="bi bi-caret-left-fill"></i> 0% </span>
              <h5 class="fw-bold mb-0">$10,390.90</h5>
              <span class="text-uppercase">TOTAL COST</span>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-6">
            <div class="text-center border-end">
              <span class="text-success">
                <i class="bi bi-caret-up-fill"></i> 20%
              </span>
              <h5 class="fw-bold mb-0">$24,813.53</h5>
              <span class="text-uppercase">TOTAL PROFIT</span>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-6">
            <div class="text-center">
              <span class="text-danger">
                <i class="bi bi-caret-down-fill"></i> 18%
              </span>
              <h5 class="fw-bold mb-0">1200</h5>
              <span class="text-uppercase">GOAL COMPLETIONS</span>
            </div>
          </div>
        </div>
        <!--end::Row-->
      </div>
      <!-- /.card-footer -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!--end::Row-->
<!--begin::Row-->
<div class="row">
  <!-- Start col -->
  <div class="col-md-8">
    <!--begin::Row-->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <!-- DIRECT CHAT -->
        <div class="card direct-chat direct-chat-warning">
          <div class="card-header">
            <h3 class="card-title">Direct Chat</h3>
            <div class="card-tools">
              <span title="3 New Messages" class="badge text-bg-warning"> 3 </span>
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-collapse"
              >
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
              </button>
              <button
                type="button"
                class="btn btn-tool"
                title="Contacts"
                data-lte-toggle="chat-pane"
              >
                <i class="bi bi-chat-text-fill"></i>
              </button>
              <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages">
              <!-- Message. Default to the start -->
              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-start"> Alexander Pierce </span>
                  <span class="direct-chat-timestamp float-end"> 23 Jan 2:00 pm </span>
                </div>
                <!-- /.direct-chat-infos -->
                <img
                  class="direct-chat-img"
                  src="./assets/img/user1-128x128.jpg"
                  alt="message user image"
                />
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  Is this template really for free? That's unbelievable!
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->
              <!-- Message to the end -->
              <div class="direct-chat-msg end">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-end"> Sarah Bullock </span>
                  <span class="direct-chat-timestamp float-start">
                    23 Jan 2:05 pm
                  </span>
                </div>
                <!-- /.direct-chat-infos -->
                <img
                  class="direct-chat-img"
                  src="./assets/img/user3-128x128.jpg"
                  alt="message user image"
                />
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">You better believe it!</div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->
              <!-- Message. Default to the start -->
              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-start"> Alexander Pierce </span>
                  <span class="direct-chat-timestamp float-end"> 23 Jan 5:37 pm </span>
                </div>
                <!-- /.direct-chat-infos -->
                <img
                  class="direct-chat-img"
                  src="./assets/img/user1-128x128.jpg"
                  alt="message user image"
                />
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  Working with AdminLTE on a great new app! Wanna join?
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->
              <!-- Message to the end -->
              <div class="direct-chat-msg end">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-end"> Sarah Bullock </span>
                  <span class="direct-chat-timestamp float-start">
                    23 Jan 6:10 pm
                  </span>
                </div>
                <!-- /.direct-chat-infos -->
                <img
                  class="direct-chat-img"
                  src="./assets/img/user3-128x128.jpg"
                  alt="message user image"
                />
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">I would love to.</div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->
            </div>
            <!-- /.direct-chat-messages-->
            <!-- Contacts are loaded here -->
            <div class="direct-chat-contacts">
              <ul class="contacts-list">
                <li>
                  <a href="#">
                    <img
                      class="contacts-list-img"
                      src="./assets/img/user1-128x128.jpg"
                      alt="User Avatar"
                    />
                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Count Dracula
                        <small class="contacts-list-date float-end"> 2/28/2023 </small>
                      </span>
                      <span class="contacts-list-msg">
                        How have you been? I was...
                      </span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img
                      class="contacts-list-img"
                      src="./assets/img/user7-128x128.jpg"
                      alt="User Avatar"
                    />
                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Sarah Doe
                        <small class="contacts-list-date float-end"> 2/23/2023 </small>
                      </span>
                      <span class="contacts-list-msg"> I will be waiting for... </span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img
                      class="contacts-list-img"
                      src="./assets/img/user3-128x128.jpg"
                      alt="User Avatar"
                    />
                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Nadia Jolie
                        <small class="contacts-list-date float-end"> 2/20/2023 </small>
                      </span>
                      <span class="contacts-list-msg"> I'll call you back at... </span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img
                      class="contacts-list-img"
                      src="./assets/img/user5-128x128.jpg"
                      alt="User Avatar"
                    />
                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Nora S. Vans
                        <small class="contacts-list-date float-end"> 2/10/2023 </small>
                      </span>
                      <span class="contacts-list-msg"> Where is your new... </span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img
                      class="contacts-list-img"
                      src="./assets/img/user6-128x128.jpg"
                      alt="User Avatar"
                    />
                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        John K.
                        <small class="contacts-list-date float-end"> 1/27/2023 </small>
                      </span>
                      <span class="contacts-list-msg"> Can I take a look at... </span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img
                      class="contacts-list-img"
                      src="./assets/img/user8-128x128.jpg"
                      alt="User Avatar"
                    />
                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Kenneth M.
                        <small class="contacts-list-date float-end"> 1/4/2023 </small>
                      </span>
                      <span class="contacts-list-msg"> Never mind I found... </span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
              </ul>
              <!-- /.contacts-list -->
            </div>
            <!-- /.direct-chat-pane -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <form action="#" method="post">
              <div class="input-group">
                <input
                  type="text"
                  name="message"
                  placeholder="Type Message ..."
                  class="form-control"
                />
                <span class="input-group-append">
                  <button type="button" class="btn btn-warning">Send</button>
                </span>
              </div>
            </form>
          </div>
          <!-- /.card-footer-->
        </div>
        <!-- /.direct-chat -->
      </div>
      <!-- /.col -->
      <div class="col-md-6">
        <!-- USERS LIST -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Latest Members</h3>
            <div class="card-tools">
              <span class="badge text-bg-danger"> 8 New Members </span>
              <button
                type="button"
                class="btn btn-tool"
                data-lte-toggle="card-collapse"
              >
                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
              </button>
              <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body p-0">
            <div class="row text-center m-1">
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user1-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Alexander Pierce
                </a>
                <div class="fs-8">Today</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user1-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Norman
                </a>
                <div class="fs-8">Yesterday</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user7-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Jane
                </a>
                <div class="fs-8">12 Jan</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user6-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  John
                </a>
                <div class="fs-8">12 Jan</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user2-160x160.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Alexander
                </a>
                <div class="fs-8">13 Jan</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user5-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Sarah
                </a>
                <div class="fs-8">14 Jan</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user4-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Nora
                </a>
                <div class="fs-8">15 Jan</div>
              </div>
              <div class="col-3 p-2">
                <img
                  class="img-fluid rounded-circle"
                  src="./assets/img/user3-128x128.jpg"
                  alt="User Image"
                />
                <a
                  class="btn fw-bold fs-7 text-secondary text-truncate w-100 p-0"
                  href="#"
                >
                  Nadia
                </a>
                <div class="fs-8">15 Jan</div>
              </div>
            </div>
            <!-- /.users-list -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer text-center">
            <a
              href="javascript:"
              class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
              >View All Users</a
            >
          </div>
          <!-- /.card-footer -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!--end::Row-->
    <!--begin::Latest Order Widget-->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Latest Orders</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
          </button>
          <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table m-0">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Item</th>
                <th>Status</th>
                <th>Popularity</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR9842</a
                  >
                </td>
                <td>Call of Duty IV</td>
                <td><span class="badge text-bg-success"> Shipped </span></td>
                <td><div id="table-sparkline-1"></div></td>
              </tr>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR1848</a
                  >
                </td>
                <td>Samsung Smart TV</td>
                <td><span class="badge text-bg-warning">Pending</span></td>
                <td><div id="table-sparkline-2"></div></td>
              </tr>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR7429</a
                  >
                </td>
                <td>iPhone 6 Plus</td>
                <td><span class="badge text-bg-danger"> Delivered </span></td>
                <td><div id="table-sparkline-3"></div></td>
              </tr>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR7429</a
                  >
                </td>
                <td>Samsung Smart TV</td>
                <td><span class="badge text-bg-info">Processing</span></td>
                <td><div id="table-sparkline-4"></div></td>
              </tr>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR1848</a
                  >
                </td>
                <td>Samsung Smart TV</td>
                <td><span class="badge text-bg-warning">Pending</span></td>
                <td><div id="table-sparkline-5"></div></td>
              </tr>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR7429</a
                  >
                </td>
                <td>iPhone 6 Plus</td>
                <td><span class="badge text-bg-danger"> Delivered </span></td>
                <td><div id="table-sparkline-6"></div></td>
              </tr>
              <tr>
                <td>
                  <a
                    href="pages/examples/invoice.html"
                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                    >OR9842</a
                  >
                </td>
                <td>Call of Duty IV</td>
                <td><span class="badge text-bg-success">Shipped</span></td>
                <td><div id="table-sparkline-7"></div></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- /.table-responsive -->
      </div>
      <!-- /.card-body -->
      <div class="card-footer clearfix">
        <a href="javascript:void(0)" class="btn btn-sm btn-primary float-start">
          Place New Order
        </a>
        <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-end">
          View All Orders
        </a>
      </div>
      <!-- /.card-footer -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
  <div class="col-md-4">
    <!-- Info Boxes Style 2 -->
    <div class="info-box mb-3 text-bg-warning">
      <span class="info-box-icon"> <i class="bi bi-tag-fill"></i> </span>
      <div class="info-box-content">
        <span class="info-box-text">Inventory</span>
        <span class="info-box-number">5,200</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
    <div class="info-box mb-3 text-bg-success">
      <span class="info-box-icon"> <i class="bi bi-heart-fill"></i> </span>
      <div class="info-box-content">
        <span class="info-box-text">Mentions</span>
        <span class="info-box-number">92,050</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
    <div class="info-box mb-3 text-bg-danger">
      <span class="info-box-icon"> <i class="bi bi-cloud-download"></i> </span>
      <div class="info-box-content">
        <span class="info-box-text">Downloads</span>
        <span class="info-box-number">114,381</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
    <div class="info-box mb-3 text-bg-info">
      <span class="info-box-icon"> <i class="bi bi-chat-fill"></i> </span>
      <div class="info-box-content">
        <span class="info-box-text">Direct Messages</span>
        <span class="info-box-number">163,921</span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title">Browser Usage</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
          </button>
          <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <!--begin::Row-->
        <div class="row">
          <div class="col-12"><div id="pie-chart"></div></div>
          <!-- /.col -->
        </div>
        <!--end::Row-->
      </div>
      <!-- /.card-body -->
      <div class="card-footer p-0">
        <ul class="nav nav-pills flex-column">
          <li class="nav-item">
            <a href="#" class="nav-link">
              United States of America
              <span class="float-end text-danger">
                <i class="bi bi-arrow-down fs-7"></i>
                12%
              </span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              India
              <span class="float-end text-success">
                <i class="bi bi-arrow-up fs-7"></i> 4%
              </span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              China
              <span class="float-end text-info">
                <i class="bi bi-arrow-left fs-7"></i> 0%
              </span>
            </a>
          </li>
        </ul>
      </div>
      <!-- /.footer -->
    </div>
    <!-- /.card -->
    <!-- PRODUCT LIST -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recently Added Products</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
          </button>
          <button type="button" class="btn btn-tool" data-lte-toggle="card-remove">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
      <!-- /.card-header -->
      <div class="card-body p-0">
        <div class="px-2">
          <div class="d-flex border-top py-2 px-1">
            <div class="col-2">
              <img
                src="./assets/img/default-150x150.png"
                alt="Product Image"
                class="img-size-50"
              />
            </div>
            <div class="col-10">
              <a href="javascript:void(0)" class="fw-bold">
                Samsung TV
                <span class="badge text-bg-warning float-end"> $1800 </span>
              </a>
              <div class="text-truncate">Samsung 32" 1080p 60Hz LED Smart HDTV.</div>
            </div>
          </div>
          <!-- /.item -->
          <div class="d-flex border-top py-2 px-1">
            <div class="col-2">
              <img
                src="./assets/img/default-150x150.png"
                alt="Product Image"
                class="img-size-50"
              />
            </div>
            <div class="col-10">
              <a href="javascript:void(0)" class="fw-bold">
                Bicycle
                <span class="badge text-bg-info float-end"> $700 </span>
              </a>
              <div class="text-truncate">
                26" Mongoose Dolomite Men's 7-speed, Navy Blue.
              </div>
            </div>
          </div>
          <!-- /.item -->
          <div class="d-flex border-top py-2 px-1">
            <div class="col-2">
              <img
                src="./assets/img/default-150x150.png"
                alt="Product Image"
                class="img-size-50"
              />
            </div>
            <div class="col-10">
              <a href="javascript:void(0)" class="fw-bold">
                Xbox One
                <span class="badge text-bg-danger float-end"> $350 </span>
              </a>
              <div class="text-truncate">
                Xbox One Console Bundle with Halo Master Chief Collection.
              </div>
            </div>
          </div>
          <!-- /.item -->
          <div class="d-flex border-top py-2 px-1">
            <div class="col-2">
              <img
                src="./assets/img/default-150x150.png"
                alt="Product Image"
                class="img-size-50"
              />
            </div>
            <div class="col-10">
              <a href="javascript:void(0)" class="fw-bold">
                PlayStation 4
                <span class="badge text-bg-success float-end"> $399 </span>
              </a>
              <div class="text-truncate">PlayStation 4 500GB Console (PS4)</div>
            </div>
          </div>
          <!-- /.item -->
        </div>
      </div>
      <!-- /.card-body -->
      <div class="card-footer text-center">
        <a href="javascript:void(0)" class="uppercase"> View All Products </a>
      </div>
      <!-- /.card-footer -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!--end::Row-->
@endsection
