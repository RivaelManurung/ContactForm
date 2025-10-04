<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
<main class="container-md pt-4">
    <div class="mx-auto" style="max-width: 700px">
        <header class="text-center mb-4">
            <h1 class="fw-bold mb-2">Contact Us</h1>
            <p class="text-muted">If you found this project helpful, ⭐️ star the repo !</p>
        </header>

        <section class="shadow rounded p-4 bg-body">
            <!-- Status -->
            @if (session('success'))
                <div id="alert-status" class="alert alert-success">{{ session('success') }}</div>
            @elseif ($errors->any())
                <div id="alert-status" class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form id="contactForm" method="POST" action="{{ route('contact.submit') }}" class="needs-validation" novalidate autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required autocomplete="name" />
                    <div class="invalid-feedback">Please enter your name.</div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required autocomplete="email" />
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" required autocomplete="off" />
                    <div class="invalid-feedback">Please enter a subject.</div>
                </div>

                <div class="mb-4">
                    <label for="message" class="form-label">Message<span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="6" required autocomplete="off"></textarea>
                    <div class="invalid-feedback">Please enter your message.</div>
                </div>

                <!-- Honeypot -->
                <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

                <!-- Hidden field for reCAPTCHA -->
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                <button type="submit" class="btn btn-primary w-100 btn-lg d-flex align-items-center justify-content-center gap-2">
                    <span id="loading-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    <span>Submit</span>
                </button>
            </form>
        </section>
    </div>
</main>

<!-- reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITE_KEY') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const spinner = document.getElementById('loading-spinner');
    spinner.classList.remove('d-none');

    grecaptcha.ready(function() {
        grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY') }}', {action: 'contact'})
        .then(function(token) {
            document.getElementById('recaptcha_token').value = token;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                spinner.classList.add('d-none');
                alert(data.message || 'Message sent successfully!');
                form.reset();
            })
            .catch(err => {
                spinner.classList.add('d-none');
                alert('Failed to send message.');
            });
        });
    });
});
</script>
</body>
</html>
