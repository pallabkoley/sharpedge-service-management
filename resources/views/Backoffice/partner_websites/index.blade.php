{{-- resources/views/backoffice/partner_websites/index.blade.php --}}
@extends('layouts.Backoffice')

@section('page-title', 'Partner Websites')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-2 mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">
            <i class="bi bi-link-45deg me-2"></i>Partner Websites
        </h1>
        <span class="badge bg-secondary fs-6 px-3 py-2 rounded-pill">
            <i class="bi bi-globe me-1"></i> {{ $websites->count() }} active
        </span>
    </div>

    @if($websites->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-5 my-5">
            <div class="mb-4">
                <i class="bi bi-browser-chrome display-1 text-muted"></i>
            </div>
            <h3 class="fw-semibold text-secondary">No Websites Found</h3>
            <p class="text-muted mt-2">There are no active partner websites to display at this time.</p>
        </div>
    @else
        <!-- Responsive Grid: 2 cols mobile, 3 md, 4 lg, 5/6 xl -->
        <div class="row g-4">
            @foreach($websites as $site)
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <a href="{{ $site->url }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="text-decoration-none d-block h-100">
                        <div class="card partner-card h-100 shadow-sm rounded-4 border-0 text-center p-3 transition-all">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-2">
                                <!-- Logo -->
                                @if($site->logo && Storage::disk('public')->exists($site->logo))
                                    <img src="{{ asset('storage/' . $site->logo) }}" 
                                         alt="{{ $site->name }}" 
                                         class="partner-logo mb-3"
                                         style="height: 60px; width: auto; max-width: 100%; object-fit: contain;">
                                @else
                                    <div class="placeholder-logo bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" 
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-building fs-2 text-secondary"></i>
                                    </div>
                                @endif
                                <!-- Website Name -->
                                <h5 class="card-title text-dark fw-semibold mb-0 text-truncate w-100" 
                                    title="{{ $site->name }}">
                                    {{ $site->name }}
                                </h5>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    /* Partner Card Styles */
    .partner-card {
        background: #ffffff;
        transition: transform 0.25s ease-in-out, box-shadow 0.3s ease-in-out;
        cursor: pointer;
        overflow: hidden;
    }

    .partner-card:hover {
        transform: scale(1.02);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.12) !important;
    }

    .partner-logo {
        transition: opacity 0.2s ease;
    }

    .partner-card:hover .partner-logo {
        opacity: 0.85;
    }

    /* Ensure equal card height */
    .partner-card .card-body {
        min-height: 140px;
    }

    /* Responsive logo sizing */
    @media (max-width: 576px) {
        .partner-logo {
            height: 45px !important;
        }
        .partner-card .card-body {
            min-height: 120px;
        }
        .card-title {
            font-size: 0.95rem;
        }
    }

    /* Medium screens fine-tune */
    @media (min-width: 768px) and (max-width: 1199px) {
        .partner-logo {
            height: 55px;
        }
    }

    /* Accessibility: focus ring */
    a:focus .partner-card {
        outline: 2px solid #0d6efd;
        outline-offset: 2px;
    }
</style>
@endsection