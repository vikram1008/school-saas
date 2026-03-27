@extends('layouts.superadmin.superadmin')

@section('title', 'Super Admin Dashboard')

@section('content')
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Super Admin /</span> Dashboard
</h4>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title text-white mb-2">Welcome back, Vikram! 🚀</h4>
                    <p class="card-text mb-0">Aapka SaaS engine room bilkul ready hai. Yahan se saare schools manage honge.</p>
                </div>
                <div class="d-none d-sm-block">
                    <i class="bx bx-server" style="font-size: 4rem; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card shadow-none bg-label-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-primary text-white">
                            <i class="bx bx-buildings fs-4"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-3">
                    <h5 class="mb-1">12</h5>
                    <p class="mb-0 text-muted">Total Schools</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card shadow-none bg-label-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-success text-white">
                            <i class="bx bx-group fs-4"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-3">
                    <h5 class="mb-1">4,500+</h5>
                    <p class="mb-0 text-muted">Active Students</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card shadow-none bg-label-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-warning text-white">
                            <i class="bx bx-rupee fs-4"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-3">
                    <h5 class="mb-1">₹1.2L</h5>
                    <p class="mb-0 text-muted">Monthly Revenue</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection