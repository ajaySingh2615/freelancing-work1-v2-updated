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
                    <form id="contact-form">
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
                            <label for="interested-course">Course Interested In</label>
                            <select class="form-control" id="interested-course" name="interested_course" required>
                                <option value="">Select Course</option>
                                <option value="MBBS">MBBS</option>
                                <option value="BDS">BDS</option>
                                <option value="MD">MD</option>
                                <option value="Nursing">Nursing</option>
                                <option value="Pharmacy">Pharmacy</option>
                                <option value="Other Medical Course">Other Medical Course</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred-country">Preferred Country</label>
                            <select class="form-control" id="preferred-country" name="preferred_country" required>
                                <option value="">Select Country</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Russia">Russia</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Nepal">Nepal</option>
                                <option value="China">China</option>
                                <option value="Egypt">Egypt</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Not Sure">Not Sure</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tell us about your academic background, goals, or any specific questions..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fab fa-whatsapp"></i> Send via WhatsApp
                        </button>
                    </form>

                    <!-- WhatsApp Integration Script -->
                    <script>
                        document.getElementById('contact-form').addEventListener('submit', function(event) {
                            event.preventDefault();
                            
                            const form = event.target;
                            const submitBtn = form.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            
                            // Show loading state
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing WhatsApp...';
                            submitBtn.disabled = true;
                            
                            // Get form data
                            const firstName = form.first_name.value.trim();
                            const lastName = form.last_name.value.trim();
                            const email = form.email.value.trim();
                            const phone = form.phone.value.trim();
                            const course = form.interested_course.value;
                            const country = form.preferred_country.value;
                            const message = form.message.value.trim();
                            
                            // Create WhatsApp message
                            const whatsappMessage = `🎓 *New Inquiry from Website*

👤 *Name:* ${firstName} ${lastName}
📧 *Email:* ${email}
📱 *Phone:* ${phone}
📚 *Course Interested:* ${course}
🌍 *Preferred Country:* ${country}

💬 *Message:*
${message}

📅 *Date:* ${new Date().toLocaleString()}

---
_This inquiry was submitted through the MedStudy Global contact form._`;

                            // WhatsApp phone number (Indian format with country code)
                            const whatsappNumber = '919729317513';
                            
                            // Create WhatsApp URL
                            const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(whatsappMessage)}`;
                            
                            // Small delay for better UX
                            setTimeout(() => {
                                // Open WhatsApp
                                window.open(whatsappURL, '_blank');
                                
                                // Show success message
                                alert('✅ Thank you! You will be redirected to WhatsApp where your message is ready to send. Our team will respond to you shortly.');
                                
                                // Reset form
                                form.reset();
                                
                                // Restore button
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }, 1000);
                        });
                        
                        // Form validation enhancement
                        const form = document.getElementById('contact-form');
                        const inputs = form.querySelectorAll('input, select, textarea');
                        
                        inputs.forEach(input => {
                            input.addEventListener('blur', function() {
                                validateField(this);
                            });
                        });
                        
                        function validateField(field) {
                            const value = field.value.trim();
                            const fieldContainer = field.closest('.form-group');
                            
                            // Remove previous validation classes
                            fieldContainer.classList.remove('has-error', 'has-success');
                            
                            if (field.hasAttribute('required') && !value) {
                                fieldContainer.classList.add('has-error');
                                return false;
                            }
                            
                            // Email validation
                            if (field.type === 'email' && value) {
                                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                if (!emailRegex.test(value)) {
                                    fieldContainer.classList.add('has-error');
                                    return false;
                                }
                            }
                            
                            // Phone validation (basic)
                            if (field.type === 'tel' && value) {
                                const phoneRegex = /^[+]?[\d\s\-\(\)]{10,}$/;
                                if (!phoneRegex.test(value)) {
                                    fieldContainer.classList.add('has-error');
                                    return false;
                                }
                            }
                            
                            fieldContainer.classList.add('has-success');
                            return true;
                        }
                    </script>
                    
                    <!-- Add some custom CSS for form validation -->
                    <style>
                        .form-group.has-error .form-control {
                            border-color: #dc3545;
                            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
                        }
                        
                        .form-group.has-success .form-control {
                            border-color: #28a745;
                            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
                        }
                        
                        .btn-submit {
                            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
                            border: none;
                            color: white;
                            padding: 15px 30px;
                            border-radius: 8px;
                            font-size: 16px;
                            font-weight: 600;
                            transition: all 0.3s ease;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 10px;
                            width: 100%;
                        }
                        
                        .btn-submit:hover {
                            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
                            transform: translateY(-2px);
                            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
                        }
                        
                        .btn-submit:disabled {
                            opacity: 0.7;
                            cursor: not-allowed;
                            transform: none;
                        }
                        
                        .form-control {
                            transition: all 0.3s ease;
                        }
                        
                        .form-control:focus {
                            border-color: #25D366;
                            box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25);
                        }
                    </style>
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
            <div class="col-lg-4 col-md-6">
                <div class="office-box">
                    <h3>Headquarter</h3>
                    <p>Unit-828, Tower B3, Spaze i-Tech Park,<br> Sector 49, Gurugram, Haryana 122018</p>
                    <ul class="office-contact">
                        <li><i class="fas fa-phone-alt"></i> <a href="tel:+91-9729317513">+91-9729317513</a></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:sunriseglobaleducationgurgaon@gmail.com">sunriseglobaleducationgurgaon@gmail.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="office-box">
                    <h3>Branch Office</h3>
                    <p>New Anaj Mandi, Nuh Hodal Rd,<br> above JJP office, Nuh, Haryana 122107</p>
                    <ul class="office-contact">
                        <li><i class="fas fa-phone-alt"></i> <a href="tel:+91-8059782607">+91-8059782607</a></li>
                        <li><i class="fas fa-map-marker-alt"></i> <span>Nuh, Haryana</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="office-box">
                    <h3>Our International Office</h3>
                    <p>302027, Oryol region, Oktyabrskaya st., 211,<br> House No.114, office No. 5, OREL STATE RUSSIA</p>
                    <ul class="office-contact">
                        <li><i class="fas fa-phone-alt"></i> <a href="tel:+7-9538131758">+7 9538131758</a></li>
                        <li><i class="fas fa-globe"></i> <span>International Office</span></li>
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