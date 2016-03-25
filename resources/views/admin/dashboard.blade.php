@extends('admin-layout')

@section('content')
    
<div class="row">
  <div class="col-lg-3">
      <div class="ibox float-e-margins">
          <div class="ibox-title">
              <span class="label label-success pull-right">Total</span>
              <h5>Vendors</h5>
          </div>
          <div class="ibox-content">
              <h1 class="no-margins">{{ $response[0]->total_vendors }}</h1>
              <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> -->
              <small>Total Vendors</small>
          </div>
      </div>
  </div>
  <div class="col-lg-3">
      <div class="ibox float-e-margins">
          <div class="ibox-title">
              <span class="label label-success pull-right">Total</span>
              <h5>Weddings</h5>
          </div>
          <div class="ibox-content">
              <h1 class="no-margins">{{ $response[0]->total_weddings }}</h1>
              <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> -->
              <small>Total Weddings</small>
          </div>
      </div>
  </div>
  <div class="col-lg-3">
      <div class="ibox float-e-margins">
          <div class="ibox-title">
              <span class="label label-success pull-right">Total</span>
              <h5>Users</h5>
          </div>
          <div class="ibox-content">
              <h1 class="no-margins">{{ $response[0]->total_users }}</h1>
              <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> -->
              <small>Total Users</small>
          </div>
      </div>
  </div>
  <div class="col-lg-3">
      <div class="ibox float-e-margins">
          <div class="ibox-title">
              <span class="label label-success pull-right">Total</span>
              <h5>Collaborators</h5>
          </div>
          <div class="ibox-content">
              <h1 class="no-margins">{{ $response[0]->total_collaborators }}</h1>
              <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> -->
              <small>Total Collaborators</small>
          </div>
      </div>
  </div>
  <div class="col-lg-3">
      <div class="ibox float-e-margins">
          <div class="ibox-title">
              <span class="label label-success pull-right">Total</span>
              <h5>Collaborator Groups</h5>
          </div>
          <div class="ibox-content">
              <h1 class="no-margins">{{ $response[0]->total_collaborator_groups }}</h1>
              <!-- <div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div> -->
              <small>Total Collaborator Groups</small>
          </div>
      </div>
  </div>
</div>

@stop