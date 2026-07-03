@extends('layouts.app')

@section('title', 'Contact Us')
@section('meta_description', 'Get in touch with the FoodRescue team. Send us your feedback, questions, or business inquiries in Trincomalee.')

@section('content')
<section class="py-5 bg-light-gradient">
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold text-dark mb-3">Get in <span class="text-primary text-gradient">Touch</span></h1>
        <p class="lead text-muted max-width-600 mx-auto">
            Have questions or feedback? We would love to hear from you.
        </p>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Info cards --}}
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4 pe-lg-4">
                    <h3 class="fw-bold text-dark mb-2">Contact Information</h3>
                    <p class="text-muted">
                        Reach out to our support team or visit our partner coordinate offices in Trincomalee.
                    </p>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Office Address</h6>
                            <span class="text-muted small">Trincomalee Town, Eastern Province, Sri Lanka</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Email Address</h6>
                            <a href="mailto:info.foodrescue@gmail.com" class="text-muted small text-decoration-none">info.foodrescue@gmail.com</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Phone Number</h6>
                            <a href="https://wa.me/94716787083" class="text-muted small text-decoration-none" target="_blank">+94 71 678 7083 (WhatsApp)</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Column --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 p-md-5 bg-light rounded-3">
                    <h4 class="fw-bold text-dark mb-4">Send a Message</h4>

                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="fa fa-check-circle"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label text-dark small fw-semibold">Your Name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-dark small fw-semibold">Your Email</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="john@example.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label text-dark small fw-semibold">Your Message</label>
                                <textarea name="message" id="message" rows="5" class="form-control @error('message') is-invalid @enderror" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-3 w-100 rounded-pill fw-semibold shadow-sm">
                                    <i class="fa fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
