<?php 
include('includes/header.php'); 
?>

<div class="contact-page">
<!-- Contact Header Section -->
<section class="contact-header-section section-padding">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Contact <span class="highlight-text">Us</span></h2>
            <p class="section-description">Any question or remark? just write us a message!</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-page-section section-padding">
    <div class="container">
        <div class="contact-split-container">
            <!-- Left Side: Contact Information -->
            <div class="contact-info-section">
                <div class="contact-info-content">
                    <!-- Decorative Shapes -->
                    <div class="decorative-shapes">
                        <div class="shape shape-1"></div>
                        <div class="shape shape-3"></div>
                    </div>
                    
                    <div class="contact-info-text">
                        <h2>Contact Information</h2>
                        <p>Fill up the form and our team will get back to you within 24 hours.</p>
                        
                        <div class="contact-details">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <span>+91-9729317513</span>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <span>sunriseglobaleducationgurgaon@gmail.com</span>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <span>Unit-828, Tower B3, Spaze i-Tech Park, Sector 49, Gurugram, Haryana 122018</span>
                            </div>
                        </div>
                        
                        <div class="social-media">
                            <a href="https://www.facebook.com/people/Sunrise-Global-Education-Pvt-Ltd/61577444481874/" class="social-icon" target="_blank" rel="noopener" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/sunriseglobaleducation_ggn/" class="social-icon" target="_blank" rel="noopener" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.linkedin.com/company/sunrise-global-education-ggn/" class="social-icon" target="_blank" rel="noopener" title="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://x.com/sge_ggn" class="social-icon" target="_blank" rel="noopener" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.youtube.com/@sunriseglobaleducationggn" class="social-icon" target="_blank" rel="noopener" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Contact Form -->
            <div class="contact-form-section">
                <div class="contact-form-content">
                    <form id="contact-form" action="process-contact.php" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first-name">First Name</label>
                                <input type="text" class="form-control" id="first-name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="last-name">Last Name</label>
                                <input type="text" class="form-control" id="last-name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="example@email.com" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Write your message..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">Send Message</button>
                    </form>

                    <!-- EmailJS Integration Script -->
                    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
                    <script>
                        // Initialize EmailJS
                        (function() {
                            emailjs.init("Xl2-rb_v5qwA8iJpI"); // Your EmailJS public key
                        })();

                        // Handle contact form submission
                        document.getElementById('contact-form').addEventListener('submit', function(event) {
                            event.preventDefault();
                            
                            const form = event.target;
                            const submitBtn = form.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            
                            // Show loading state
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                            submitBtn.disabled = true;
                            
                            // Prepare template parameters
                            const templateParams = {
                                first_name: form.first_name.value,
                                last_name: form.last_name.value,
                                from_name: form.first_name.value + ' ' + form.last_name.value,
                                from_email: form.email.value,
                                phone: form.phone.value,
                                message: form.message.value,
                                to_email: 'ajaysingh261526@gmail.com',
                                date: new Date().toLocaleString(),
                                subject: 'New Contact Form Submission from ' + form.first_name.value + ' ' + form.last_name.value
                            };
                            
                            // Send via EmailJS
                            emailjs.send('service_igiat6d', 'template_kxu5e1d', templateParams)
                                .then(function() {
                                    alert('Thank you! Your message has been sent successfully. We will get back to you within 24 hours.');
                                    form.reset();
                                }, function(error) {
                                    console.log('EmailJS Error:', error);
                                    console.log('Falling back to PHP form submission...');
                                    alert('Using backup email system...');
                                    // Fallback to PHP form
                                    submitContactFormViaPhp(form);
                                })
                                .finally(function() {
                                    // Restore button
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                });
                        });
                        
                        // Fallback to PHP submission
                        function submitContactFormViaPhp(form) {
                            const formData = new FormData(form);
                            
                            fetch('process-contact.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    alert(data.message);
                                    form.reset();
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('There was an error submitting your form. Please try again or contact us directly.');
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Office Locations Section -->
<section class="office-section section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2>Our Branch <span class="highlight-text">Offices</span></h2>
                <p class="mb-5">Visit our branch offices across the country for in-person consultation.</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-6">
                <div class="office-box">
                    <h3>Headquarter</h3>
                    <p>Unit-828, Tower B3, Spaze i-Tech Park,<br> Sector 49, Gurugram, Haryana 122018</p>
                    <ul class="office-contact">
                        <li><i class="fas fa-phone-alt"></i> <a href="tel:+91-9729317513">+91-9729317513</a></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:sunriseglobaleducationgurgaon@gmail.com">sunriseglobaleducationgurgaon@gmail.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="office-box">
                    <h3>Branch Office</h3>
                    <p>New Anaj Mandi, Nuh Hodal Rd,<br> above JJP office, Nuh, Haryana 122107</p>
                    <ul class="office-contact">
                        <li><i class="fas fa-phone-alt"></i> <a href="tel:+91-8059782607">+91-8059782607</a></li>
                        <li><i class="fas fa-map-marker-alt"></i> <span>Nuh, Haryana</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Map -->
<div class="google-map">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3509.2159924974585!2d77.04158387553461!3d28.41273877578543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d229aba105daf%3A0x272f48e3df8d9cf8!2sTower%20B3%2C%20Spaze%20i-Tech%20Park%2C%20Sector%2049%2C%20Gurugram%2C%20Haryana%20122018!5e0!3m2!1sen!2sin!4v1753273635864!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<!-- FAQ Section -->
<section class="faq-section section-padding">
    <div class="container">
        <div class="faq-container">
            <div class="faq-header">
                <p class="faq-prompt">Need more help?</p>
                <h2 class="faq-title">Frequently Asked <span class="highlight-text">Questions (FAQ)</span></h2>
                <p class="faq-subheading">Any question or remarks? Just write us a message!</p>
            </div>
            
            <div class="faq-accordion">
                <div class="accordion" id="contactFaq">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What programs and countries do you specialize in?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#contactFaq">
                            <div class="card-body">
                                We guide students into MBBS and related healthcare programs across India, Iran, Bangladesh, Russia, Kazakhstan, Kyrgyzstan, Georgia, and Uzbekistan. Our team helps match your profile to the best-fit universities and courses in each of these countries.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How do I know if I’m eligible to apply?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#contactFaq">
                            <div class="card-body">
                                Eligibility generally requires completion of 10+2 with Physics, Chemistry & Biology (PCB) and a minimum aggregate (usually 50–60%). Specific requirements vary by country and university—send us your transcripts and we’ll confirm your fit.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Can you help me get a scholarship or education loan?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#contactFaq">
                            <div class="card-body">
                                Absolutely. We’ll identify merit-based scholarships and assist with their applications, plus connect you with partner banks for education loans at competitive interest rates and guide you through the entire loan process.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingFour">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    What visa support do you provide?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#contactFaq">
                            <div class="card-body">
                                Our visa services include:<br>
                                <ul>
                                    <li>Document verification & preparation</li>
                                    <li>Embassy appointment booking</li>
                                    <li>Mock interview practice</li>
                                    <li>Real-time tracking from submission to approval</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingFive">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    What pre-departure services do you offer?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#contactFaq">
                            <div class="card-body">
                                We run comprehensive orientation sessions covering:<br>
                                <ul>
                                    <li>Cultural adaptation & academic expectations</li>
                                    <li>Budgeting & cost-of-living insights</li>
                                    <li>Accommodation options</li>
                                    <li>Health, safety, and local regulations</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingSix">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    Do you arrange travel and accommodation?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#contactFaq">
                            <div class="card-body">
                                Yes. We handle:<br>
                                <ul>
                                    <li>Flight bookings at student-friendly rates</li>
                                    <li>Airport pickup coordination</li>
                                    <li>On-campus or off-campus housing assistance</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingSeven">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    What post-arrival support can I expect?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#contactFaq">
                            <div class="card-body">
                                After you land, we’ll help with:<br>
                                <ul>
                                    <li>University registration processes</li>
                                    <li>Opening local bank accounts & SIM cards</li>
                                    <li>Troubleshooting any academic or administrative hurdles</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>
<!-- End contact-page wrapper -->

<?php 
include('includes/footer.php'); 
?> 