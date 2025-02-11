<form id="applicantForm" method="POST">
    @csrf

    <div class="row">
        <!-- Personal Information -->
        <div class="col-md-6">

            <div class="mb-3">
                <label for="address" class="form-label">First Name</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="123 Main St">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Last Name</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="123 Main St">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Email</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="123 Main St">
            </div>

            <div class="mb-3">
                <label for="phone0" class="form-label">Phone</label>
                <input type="text" class="phone-input-control" name="phone" id="phpne0">
                <input type="text" hidden class="form-control" name="code" id="code0">
                <input type="text" hidden class="form-control" name="country" id="country0">
            </div>


            <div class="mb-3">
                <label for="address" class="form-label">Login Password <strong>Enable aplicant to log in.</strong> </label>
                <input type="text" class="form-control" name="password" id="password" placeholder="Login password">
            </div>


            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="address" placeholder="123 Main St">
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" name="city" id="city" placeholder="Nairobi">
            </div>

            <div class="mb-3">
                <label for="state" class="form-label">State</label>
                <input type="text" class="form-control" name="state" id="state" placeholder="Nairobi County">
            </div>

            <div class="mb-3">
                <label for="zip_code" class="form-label">Zip Code</label>
                <input type="text" class="form-control" name="zip_code" id="zip_code">
            </div>

            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" class="form-control" name="country" id="country" placeholder="Kenya">
            </div>
        </div>

        <!-- Professional Information -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="linkedin_profile" class="form-label">LinkedIn Profile</label>
                <input type="url" class="form-control" name="linkedin_profile" id="linkedin_profile" placeholder="https://linkedin.com/in/username">
            </div>

            <div class="mb-3">
                <label for="portfolio_url" class="form-label">Portfolio URL</label>
                <input type="url" class="form-control" name="portfolio_url" id="portfolio_url" placeholder="https://myportfolio.com">
            </div>

            <div class="mb-3">
                <label for="current_job_title" class="form-label">Current Job Title</label>
                <input type="text" class="form-control" name="current_job_title" id="current_job_title" placeholder="Software Engineer">
            </div>

            <div class="mb-3">
                <label for="current_company" class="form-label">Current Company</label>
                <input type="text" class="form-control" name="current_company" id="current_company" placeholder="Google">
            </div>

            <div class="mb-3">
                <label for="experience_level" class="form-label">Experience Level</label>
                <select name="experience_level" id="experience_level" class="form-control">
                    <option value="">Select Level</option>
                    <option value="Entry-level">Entry-level</option>
                    <option value="Mid-level">Mid-level</option>
                    <option value="Senior">Senior</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="education_level" class="form-label">Education Level</label>
                <select name="education_level" id="education_level" class="form-control">
                    <option value="">Select Level</option>
                    <option value="High School">High School</option>
                    <option value="Bachelor's">Bachelor's</option>
                    <option value="Master's">Master's</option>
                    <option value="PhD">PhD</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="desired_salary" class="form-label">Desired Salary (KES)</label>
                <input type="text" class="form-control" name="desired_salary" id="desired_salary" placeholder="100000">
            </div>

            <div class="mb-3">
                <label for="job_preferences" class="form-label">Job Preferences</label>
                <input type="text" class="form-control" name="job_preferences" id="job_preferences" placeholder="Software Engineering, DevOps">
            </div>

            <div class="mb-3">
                <label for="source" class="form-label">Source</label>
                <input type="text" class="form-control" name="source" id="source" placeholder="LinkedIn, Referral">
            </div>
        </div>
    </div>

    <button type="button" onclick="saveApplicant(this)" class="btn btn-primary w-100"> <i class="bi bi-check-circle me-2"></i> Save Applicant</button>
</form>
