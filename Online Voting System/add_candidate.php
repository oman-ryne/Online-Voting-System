<?php
// Use absolute path for includes
require __DIR__ . '/config/dbconnection.php';


// Validate election_id more securely
if (!isset($_GET['election_id']) || !ctype_digit($_GET['election_id'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? 'index.php');
    exit;
}

$election_id = (int)$_GET['election_id'];

// Prepared statement with error handling
try {
    $stmt = $conn->prepare("SELECT id, name FROM categories WHERE election_id = ?");
    if (!$stmt) {
        throw new Exception("Database query preparation failed");
    }
    
    $stmt->bind_param('i', $election_id);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed");
    }
    
    $result = $stmt->get_result();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die("An error occurred while loading categories. Please try again later.");
}
?>

<form id="add-candidate-form" enctype="multipart/form-data">
  <div class="row g-3">
    <div class="col-md-12">
      <label for="category" class="form-label">Category<span class="text-danger"> *</span></label>
      <input type="hidden" name="election" value="<?= htmlspecialchars($election_id) ?>">
      <select class="form-select" name="category" id="category" required data-error-message="Select Category">
        <option value="" hidden>Select Category</option>
        <?php while ($row = $result->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($row['id']) ?>">
            <?= htmlspecialchars($row['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <div class="invalid-feedback"></div>
    </div>
    
    <div class="col-md-12">
      <label for="candidate" class="form-label">Candidate Name<span class="text-danger"> *</span></label>
      <input type="text" name="candidate" class="form-control" id="candidate" 
             required pattern="[A-Za-z\s]{2,50}" 
             data-error-message="Please enter a valid name (2-50 characters)"
             placeholder="Enter Candidate name">
      <div class="invalid-feedback"></div>
    </div>
    
    <div class="col-md-6">
      <label for="candidate_photo" class="form-label">Candidate Photo</label>
      <input class="form-control" type="file" accept="image/jpeg,image/png" 
             id="candidate_photo" name="candidate_photo" 
             data-error-message="Please upload a valid image (JPEG/PNG, max 2MB)">
      <div class="invalid-feedback"></div>
    </div>
    
    <div class="col-md-6">
      <label for="candidate_year" class="form-label">Age<span class="text-danger"> *</span></label>
      <select class="form-select" name="candidate_year" id="candidate_year" required
              data-error-message="Please select an age group">
        <option value="" hidden>Select Age Group</option>
        <option value="30">30+</option>
        <option value="40">40+</option>
        <option value="50">50+</option>
        <option value="60">60+</option>
      </select>
      <div class="invalid-feedback"></div>
    </div>
    
    <!-- Fellow candidate fields (optional) -->
    <div class="col-md-12">
      <label for="fellow_candidate" class="form-label">Fellow Candidate Name</label>
      <input type="text" name="fellow_candidate" class="form-control" id="fellow_candidate" 
             pattern="[A-Za-z\s]{2,50}" 
             placeholder="If not applicable, leave empty">
      <div class="invalid-feedback"></div>
    </div>
    
    <div class="col-md-6">
      <label for="fellow_candidate_photo" class="form-label">Fellow Candidate Photo</label>
      <input class="form-control" type="file" accept="image/jpeg,image/png" 
             id="fellow_candidate_photo" name="fellow_candidate_photo">
      <div class="invalid-feedback"></div>
    </div>
    
    <div class="col-md-6">
      <label for="fellow_candidate_year" class="form-label">Age Group</label>
      <select class="form-select" name="fellow_candidate_year" id="fellow_candidate_year">
        <option value="" hidden>Select Age Group</option>
        <option value="30">30+</option>
        <option value="40">40+</option>
        <option value="50">50+</option>
        <option value="60">60+</option>
      </select>
    </div>
  </div>
  
  <div class="modal-footer mt-3">
    <button type="reset" class="btn btn-secondary">Reset</button>
    <button type="submit" class="btn btn-primary">Add Candidate</button>
  </div>
</form>

<script>
$(document).ready(function() {
    // Form validation
    const validateField = (input) => {
        const value = input.val().trim();
        const errorMessage = input.data('error-message');
        const isValid = input.prop('required') ? value !== '' : true;
        
        if (!isValid) {
            input.addClass('is-invalid');
            input.siblings('.invalid-feedback').text(errorMessage);
            return false;
        }
        
        // Additional pattern validation for text inputs
        if (input.attr('pattern') && value) {
            const regex = new RegExp(input.attr('pattern'));
            if (!regex.test(value)) {
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').text(errorMessage);
                return false;
            }
        }
        
        input.removeClass('is-invalid');
        input.siblings('.invalid-feedback').text('');
        return true;
    };

    // Real-time validation
    $('#add-candidate-form').find('input, select').on('input change', function() {
        validateField($(this));
    });

    // Form submission
    $('#add-candidate-form').submit(function(e) {
        e.preventDefault();
        let isValid = true;
        
        // Validate all required fields
        $(this).find('[required]').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });
        
        if (!isValid) return;
        
        // File validation
        const photoInput = $('#candidate_photo')[0];
        if (photoInput.files.length > 0) {
            const file = photoInput.files[0];
            const validTypes = ['image/jpeg', 'image/png'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!validTypes.includes(file.type)) {
                $('#candidate_photo').addClass('is-invalid')
                    .siblings('.invalid-feedback')
                    .text('Only JPEG/PNG images are allowed');
                isValid = false;
            } else if (file.size > maxSize) {
                $('#candidate_photo').addClass('is-invalid')
                    .siblings('.invalid-feedback')
                    .text('Image must be less than 2MB');
                isValid = false;
            }
        }
        
        if (!isValid) return;
        
        // AJAX submission
        const formData = new FormData(this);
        
        $.ajax({
            url: 'controllers/app.php?action=add_candidate',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: showLoadingOverlay,
            success: function(response) {
                hideLoadingOverlay();
                if (response.status === 'success') {
                    window.location.href = response.redirect_url;
                } else {
                    const alert = $(`<div class="alert alert-danger">${response.message || 'An error occurred'}</div>`);
                    $('#add-candidate-form').prepend(alert);
                    setTimeout(() => alert.fadeOut(), 5000);
                }
            },
            error: function(xhr) {
                hideLoadingOverlay();
                const alert = $('<div class="alert alert-danger">Server error. Please try again.</div>');
                $('#add-candidate-form').prepend(alert);
                setTimeout(() => alert.fadeOut(), 5000);
                console.error('Error:', xhr.responseText);
            }
        });
    });
});
</script>