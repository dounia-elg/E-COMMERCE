<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>{{ config('app.name', 'Laravel') }}</h5>
                <p>Your one-stop online shopping destination for quality products at competitive prices.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-white">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-white">Products</a></li>
                    <li><a href="#" class="text-white">About Us</a></li>
                    <li><a href="#" class="text-white">Contact Us</a></li>
                    <li><a href="#" class="text-white">Privacy Policy</a></li>
                    <li><a href="#" class="text-white">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <address>
                    <p><i class="fa fa-map-marker-alt me-2"></i> 123 Street Name, City, Country</p>
                    <p><i class="fa fa-phone me-2"></i> +1 234 567 8900</p>
                    <p><i class="fa fa-envelope me-2"></i> info@example.com</p>
                </address>
                <h5 class="mt-4">Newsletter</h5>
                <form action="#" method="POST">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Your Email" required>
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-md-0">© {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <img src="{{ asset('images/payment-methods.png') }}" alt="Payment Methods" class="img-fluid" style="max-height: 30px;">
            </div>
        </div>
    </div>
</footer>