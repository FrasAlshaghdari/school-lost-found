// Global JavaScript functions and utilities for School Lost & Found

$(document).ready(function() {
    // Initialize tooltips if Bootstrap tooltips are used
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').not('.alert-permanent').fadeOut('slow');
    }, 5000);
    
    // Form validation enhancement
    $('form').on('submit', function(e) {
        var isValid = true;
        $(this).find('input[required], textarea[required], select[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showAlert('danger', 'Please fill in all required fields');
        }
    });
    
    // Remove invalid class on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Image preview for file inputs
    $('input[type="file"]').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            // Check file size (5MB max)
            if (file.size > 5242880) {
                alert('File size must be less than 5MB');
                $(this).val('');
                return;
            }
            
            // Check file type
            var validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, or GIF)');
                $(this).val('');
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = $('<img>').attr('src', e.target.result).addClass('img-fluid mt-2 rounded').css('max-height', '200px');
                $('#imagePreview').html(preview);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Confirm before leaving page with unsaved changes
    var formChanged = false;
    $('form input, form textarea, form select').on('change', function() {
        formChanged = true;
    });
    
    $('form').on('submit', function() {
        formChanged = false;
    });
    
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
});

// Utility function to format date
function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Utility function to truncate text
function truncateText(text, maxLength) {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
}

// Show loading indicator
function showLoading(container) {
    $(container).html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
}

// Show alert message
function showAlert(type, message) {
    var alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Find alert container or create one
    if ($('#alertMessage').length) {
        $('#alertMessage').html(alertHtml);
    } else {
        $('body').prepend('<div id="alertMessage" class="container mt-3">' + alertHtml + '</div>');
    }
    
    // Auto dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

// AJAX error handler
$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    console.error('AJAX Error:', thrownError);
    if (jqxhr.status === 401) {
        window.location.href = 'login.php';
    } else {
        showAlert('danger', 'An error occurred. Please try again later.');
    }
});

// Smooth scroll to top button
$(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
        if ($('#scrollTop').length === 0) {
            $('body').append('<button id="scrollTop" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px;"><i class="fas fa-arrow-up"></i></button>');
            
            $('#scrollTop').on('click', function() {
                $('html, body').animate({scrollTop: 0}, 'smooth');
            });
        }
        $('#scrollTop').fadeIn();
    } else {
        $('#scrollTop').fadeOut();
    }
});

// Prevent double submission
$('form').on('submit', function() {
    var submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true);
    
    setTimeout(function() {
        submitBtn.prop('disabled', false);
    }, 3000);
});

// Search functionality with debounce
var searchTimeout;
function debounceSearch(func, delay) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(func, delay);
}

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showAlert('success', 'Copied to clipboard!');
    }).catch(function(err) {
        console.error('Failed to copy:', err);
    });
}

// Print function
function printElement(elementId) {
    var printContents = document.getElementById(elementId).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    var csv = [];
    var rows = document.querySelectorAll('#' + tableId + ' tr');
    
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (var j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    
    csvFile = new Blob([csv], {type: 'text/csv'});
    downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}