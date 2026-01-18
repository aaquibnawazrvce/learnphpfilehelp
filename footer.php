    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5><i class="fas fa-cloud me-2"></i>CloudDrive</h5>
                    <p class="text-white-50">Your secure personal cloud storage solution. Store, access, and share your files from anywhere.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5><i class="fas fa-question-circle me-2"></i>FAQs</h5>
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item bg-transparent border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent text-white p-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How many files can I upload for free?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50 p-2">
                                    Free users can upload up to <strong>10 files</strong>. For unlimited storage, upgrade to Premium for just <strong><?php echo PREMIUM_PRICE; ?></strong>!
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent text-white p-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What file types are supported?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50 p-2">
                                    CloudDrive supports all file types including documents, images, videos, and more!
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-transparent text-white p-2" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How do I upgrade to Premium?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50 p-2">
                                    After uploading 10 files, visit the Premium page to submit your payment details and unlock unlimited uploads!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-crown me-2"></i>Premium Plan</h5>
                    <div class="bg-white bg-opacity-10 rounded p-3">
                        <p class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited file uploads</p>
                        <p class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority support</p>
                        <p class="mb-2"><i class="fas fa-check text-success me-2"></i>Larger file sizes</p>
                        <p class="mb-0 h4 text-warning"><?php echo PREMIUM_PRICE; ?></p>
                    </div>
                </div>
            </div>
            <hr class="my-4 bg-white">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; <?php echo date('Y'); ?> CloudDrive. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
